<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\LeaveRequest;
use Illuminate\Support\Carbon;

class RecommendationController extends Controller
{
    public function index()
    {
        $conflicts = $this->detectConflicts();
        $riskScores = $this->computeRiskScores();
        $patterns = $this->analyzePatterns();

        $stats = [
            'high_risk_depts' => $this->countHighRiskDepartments(),
            'active_conflicts' => $conflicts->count(),
            'predicted_spikes' => $this->countPredictedSpikes(),
        ];
        $stats['recommendations'] = $stats['active_conflicts'] + count($patterns);

        return view('recommendations.index', compact('conflicts', 'riskScores', 'patterns', 'stats'));
    }

    /**
     * JSON endpoint for the day-of-week pattern chart.
     */
    public function patternData()
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $types = ['Annual Leave' => 'annual', 'Sick Leave' => 'sick', 'Personal Leave' => 'personal'];

        $result = ['labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'annual' => [], 'sick' => [], 'personal' => []];

        foreach ($types as $typeName => $key) {
            foreach ($days as $day) {
                $count = LeaveRequest::where('leave_type', $typeName)
                    ->get()
                    ->filter(fn($l) => Carbon::parse($l->start_date)->format('l') === $day)
                    ->count();
                $result[$key][] = $count;
            }
        }

        return response()->json($result);
    }

    /**
     * Detect days where too many employees from the same department
     * have overlapping approved/pending leave (>30% of department headcount).
     */
    private function detectConflicts()
    {
        $conflicts = collect();

        $departments = Department::withCount('users')->get();

        foreach ($departments as $dept) {
            if ($dept->users_count === 0) continue;

            $leaves = LeaveRequest::whereHas('user', function ($q) use ($dept) {
                    $q->where('department_id', $dept->id);
                })
                ->whereIn('status', ['approved', 'pending'])
                ->where('end_date', '>=', now())
                ->get();

            // Group by overlapping date ranges (simple day-bucket approach)
            $dayBuckets = [];
            foreach ($leaves as $leave) {
                $period = Carbon::parse($leave->start_date)->daysUntil($leave->end_date);
                foreach ($period as $day) {
                    $key = $day->format('Y-m-d');
                    $dayBuckets[$key] = ($dayBuckets[$key] ?? 0) + 1;
                }
            }

            foreach ($dayBuckets as $date => $count) {
                $ratio = $count / $dept->users_count;
                if ($ratio >= 0.3 && $count >= 2) {
                    $conflicts->push([
                        'department' => $dept->name,
                        'date' => $date,
                        'count' => $count,
                        'total' => $dept->users_count,
                        'coverage' => round((1 - $ratio) * 100),
                        'risk' => $ratio >= 0.5 ? 'High Risk' : 'Medium Risk',
                    ]);
                }
            }
        }

        return $conflicts->sortByDesc('count')->take(5)->values();
    }

    private function countHighRiskDepartments(): int
    {
        return Department::withCount('users')->get()->filter(function ($dept) {
            if ($dept->users_count === 0) return false;
            $pendingCount = LeaveRequest::whereHas('user', fn($q) => $q->where('department_id', $dept->id))
                ->where('status', 'pending')->count();
            return $pendingCount / max($dept->users_count, 1) > 0.2;
        })->count();
    }

    private function countPredictedSpikes(): int
    {
        // Count distinct future dates with 3+ leave requests scheduled (simple spike heuristic)
        $leaves = LeaveRequest::where('start_date', '>=', now())->get();
        $dayBuckets = [];
        foreach ($leaves as $leave) {
            $key = Carbon::parse($leave->start_date)->format('Y-m-d');
            $dayBuckets[$key] = ($dayBuckets[$key] ?? 0) + 1;
        }
        return collect($dayBuckets)->filter(fn($c) => $c >= 3)->count();
    }

    /**
     * Risk score per pending leave request based on: team conflict overlap + SLA proximity.
     */
    private function computeRiskScores()
    {
        $pending = LeaveRequest::with('user.department', 'slaRecord')
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        return $pending->map(function ($leave) {
            $score = 0;
            $reasons = [];

            // SLA proximity
            if ($leave->slaRecord) {
                $hoursLeft = round(now()->diffInHours($leave->slaRecord->deadline, false));
                if ($hoursLeft < 4) {
                    $score += 2;
                    $reasons[] = 'SLA near breach';
                }
            }

            // Department overlap on same dates
            if ($leave->user && $leave->user->department_id) {
                $overlapping = LeaveRequest::whereHas('user', function ($q) use ($leave) {
                        $q->where('department_id', $leave->user->department_id);
                    })
                    ->where('id', '!=', $leave->id)
                    ->whereIn('status', ['approved', 'pending'])
                    ->where('start_date', '<=', $leave->end_date)
                    ->where('end_date', '>=', $leave->start_date)
                    ->count();

                if ($overlapping >= 2) {
                    $score += 2;
                    $reasons[] = 'Team overlap detected';
                } elseif ($overlapping == 1) {
                    $score += 1;
                    $reasons[] = 'Minor overlap';
                }
            }

            $riskLabel = $score >= 3 ? 'High' : ($score >= 1 ? 'Medium' : 'Low');

            return [
                'name' => $leave->user->name ?? 'Unknown',
                'type' => $leave->leave_type,
                'risk' => $riskLabel,
                'reason' => count($reasons) ? implode(' + ', $reasons) : 'Normal pattern',
            ];
        });
    }

    /**
     * Detect day-of-week absence patterns (e.g. high Monday sick leave).
     */
    private function analyzePatterns()
    {
        $patterns = collect();

        $sickLeaves = LeaveRequest::where('leave_type', 'Sick Leave')->get();
        $totalSick = $sickLeaves->count();

        if ($totalSick > 0) {
            $byDay = $sickLeaves->groupBy(fn($l) => Carbon::parse($l->start_date)->format('l'));
            $mondayCount = $byDay->get('Monday', collect())->count();
            $mondayRatio = $mondayCount / $totalSick;

            if ($mondayRatio > 0.3) {
                $patterns->push([
                    'level' => 'low',
                    'tag' => 'pattern',
                    'title' => 'Monday Absence Pattern',
                    'description' => 'Sick leave requests show ' . round($mondayRatio * 100) . '% concentration on Mondays. May indicate weekend-related issues.',
                    'action' => 'Schedule 1:1 wellness check-ins',
                ]);
            }
        }

        // Predicted spike pattern
        $futureLeaves = LeaveRequest::where('start_date', '>=', now())->get();
        $dayBuckets = $futureLeaves->groupBy(fn($l) => Carbon::parse($l->start_date)->format('Y-m-d'));
        $topSpike = $dayBuckets->sortByDesc(fn($g) => $g->count())->first();

        if ($topSpike && $topSpike->count() >= 3) {
            $date = Carbon::parse($topSpike->first()->start_date)->format('F j');
            $patterns->push([
                'level' => 'high',
                'tag' => 'spike prediction',
                'title' => "Predicted Leave Spike – {$date}",
                'description' => "Expected {$topSpike->count()} leave requests around {$date}. Consider pre-approving or staggering leave.",
                'action' => 'Send notification to department heads',
            ]);
        }

        // Pending volume pattern
        $pendingCount = LeaveRequest::where('status', 'pending')->count();
        if ($pendingCount > 10) {
            $patterns->push([
                'level' => 'medium',
                'tag' => 'restriction',
                'title' => 'High Pending Volume',
                'description' => "{$pendingCount} requests are currently pending. Consider reviewing approval workflow capacity.",
                'action' => 'Review delegation settings',
            ]);
        }

        return $patterns;
    }
}
