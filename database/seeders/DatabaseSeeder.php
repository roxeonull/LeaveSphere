<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\Approval;
use App\Models\Delegation;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\SlaRecord;
use App\Models\NotificationItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Departments =====
        $departments = [
            'Engineering' => Department::create(['name' => 'Engineering']),
            'Sales' => Department::create(['name' => 'Sales']),
            'HR' => Department::create(['name' => 'HR']),
            'Marketing' => Department::create(['name' => 'Marketing']),
            'Finance' => Department::create(['name' => 'Finance']),
        ];

        // ===== Super Admin & Manager =====
        $admin = User::create([
            'employee_id' => 'EMP001',
            'name' => 'Dafah Nilamanta',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'department_id' => $departments['Engineering']->id,
            'role' => 'super_admin',
            'position' => 'Project Manager / Super Admin',
            'leave_balance' => 25,
            'status' => 'active',
        ]);

        $manager = User::create([
            'employee_id' => 'EMP002',
            'name' => 'Alfian Riffat Athari',
            'email' => 'manager@company.com',
            'password' => Hash::make('password'),
            'department_id' => $departments['HR']->id,
            'role' => 'manager',
            'position' => 'HR Manager / System Analyst',
            'leave_balance' => 25,
            'status' => 'active',
        ]);

        // ===== Named Employees (incl. Kelompok 7 team + Indonesian names) =====
        $employeeNames = [
            ['Muhammad Arya Pradana', 'Engineering', 'DevOps Engineer'],
            ['Muhammad Zaki Zakariyya', 'Engineering', 'Technical Writer'],
            ['Alifito Rabbani Citra', 'Engineering', 'Frontend Web Developer'],
            ['Hanif Ghasanof', 'Engineering', 'QA Engineer'],
            ['Budi Santoso', 'Sales', 'Sales Executive'],
            ['Siti Nurhaliza', 'Marketing', 'Marketing Lead'],
            ['Rizky Maulana', 'HR', 'HR Specialist'],
            ['Dewi Anggraini', 'Finance', 'Finance Analyst'],
            ['Agus Setiawan', 'Engineering', 'Senior Developer'],
            ['Putri Wulandari', 'Sales', 'Account Manager'],
            ['Andi Pratama', 'Marketing', 'Content Strategist'],
            ['Fajar Nugroho', 'Engineering', 'Engineering Manager'],
            ['Indah Permatasari', 'HR', 'Recruitment Officer'],
            ['Yusuf Hakim', 'Finance', 'Accountant'],
            ['Nadia Ramadhani', 'Marketing', 'Social Media Specialist'],
            ['Eko Saputra', 'Sales', 'Business Development'],
            ['Lestari Wahyuni', 'HR', 'Compensation & Benefits'],
            ['Bayu Aji Saputro', 'Engineering', 'Backend Developer'],
            ['Citra Kirana', 'Finance', 'Tax Specialist'],
            ['Dian Permana', 'Marketing', 'Brand Manager'],
            ['Hendra Gunawan', 'Sales', 'Regional Sales Manager'],
            ['Intan Permatasari', 'Engineering', 'UI/UX Designer'],
            ['Joko Widodo Santosa', 'Finance', 'Finance Manager'],
            ['Kartika Sari', 'HR', 'Talent Acquisition'],
            ['Lukman Hakim', 'Engineering', 'Mobile Developer'],
            ['Maya Sari Dewi', 'Marketing', 'Digital Marketing'],
            ['Novi Indriani', 'Sales', 'Sales Coordinator'],
            ['Oka Pratama', 'Engineering', 'Data Engineer'],
            ['Putra Wijaya', 'Finance', 'Budget Analyst'],
            ['Qonita Salsabila', 'HR', 'HR Generalist'],
        ];

        $employees = [];
        foreach ($employeeNames as $i => [$name, $deptName, $position]) {
            $emailSlug = strtolower(str_replace(' ', '.', $name));
            $employees[] = User::create([
                'employee_id' => 'EMP' . str_pad($i + 3, 3, '0', STR_PAD_LEFT),
                'name' => $name,
                'email' => $emailSlug . '@company.com',
                'password' => Hash::make('password'),
                'department_id' => $departments[$deptName]->id,
                'role' => 'employee',
                'position' => $position,
                'leave_balance' => 25,
                'status' => 'active',
            ]);
        }

        // ===== Additional bulk employees with Indonesian names for volume =====
        $firstNames = [
            'Ahmad', 'Bambang', 'Cahyo', 'Dimas', 'Eka', 'Fitri', 'Gilang', 'Hadi', 'Ika', 'Joni',
            'Kurnia', 'Linda', 'Made', 'Nia', 'Oki', 'Putu', 'Qori', 'Rina', 'Sigit', 'Tono',
            'Umi', 'Vina', 'Wawan', 'Xena', 'Yudi', 'Zaenal', 'Anita', 'Bagus', 'Cinta', 'Doni',
        ];
        $lastNames = [
            'Wijaya', 'Kusuma', 'Saputra', 'Pratama', 'Hidayat', 'Santoso', 'Wibowo', 'Permana',
            'Nugraha', 'Setiawan', 'Halim', 'Susanto', 'Firmansyah', 'Maulana', 'Suryanto',
            'Hartono', 'Gunawan', 'Rahman', 'Iskandar', 'Kuncoro',
        ];

        $deptKeys = array_keys($departments);
        for ($i = 1; $i <= 120; $i++) {
            $first = $firstNames[array_rand($firstNames)];
            $last = $lastNames[array_rand($lastNames)];
            $name = "{$first} {$last}";
            $dept = $deptKeys[array_rand($deptKeys)];

            $employees[] = User::create([
                'employee_id' => 'EMP' . str_pad($i + 32, 3, '0', STR_PAD_LEFT),
                'name' => $name,
                'email' => strtolower($first . '.' . $last . $i) . '@company.com',
                'password' => Hash::make('password'),
                'department_id' => $departments[$dept]->id,
                'role' => 'employee',
                'position' => 'Staff',
                'leave_balance' => 25,
                'status' => $i % 25 === 0 ? 'inactive' : 'active',
            ]);
        }

        // ===== Workflows =====
        $wf1 = Workflow::create(['name' => 'Standard Approval', 'department_id' => $departments['Engineering']->id]);
        WorkflowStep::create(['workflow_id' => $wf1->id, 'approver_role' => 'Supervisor', 'level' => 1]);
        WorkflowStep::create(['workflow_id' => $wf1->id, 'approver_role' => 'Manager', 'level' => 2]);
        WorkflowStep::create(['workflow_id' => $wf1->id, 'approver_role' => 'HR', 'level' => 3]);

        $wf2 = Workflow::create(['name' => 'Quick Approval', 'department_id' => $departments['Sales']->id]);
        WorkflowStep::create(['workflow_id' => $wf2->id, 'approver_role' => 'Manager', 'level' => 1]);
        WorkflowStep::create(['workflow_id' => $wf2->id, 'approver_role' => 'HR', 'level' => 2]);

        // ===== Leave Requests + Approvals + SLA =====
        $leaveTypes = ['Annual Leave', 'Sick Leave', 'Personal Leave'];
        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($employees as $emp) {
            $numRequests = rand(2, 5);
            for ($j = 0; $j < $numRequests; $j++) {
                $start = Carbon::now()->addDays(rand(-30, 30));
                $days = rand(1, 5);
                $status = $statuses[array_rand($statuses)];

                $createdAt = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 28));

                $leave = LeaveRequest::create([
                    'user_id' => $emp->id,
                    'leave_type' => $leaveTypes[array_rand($leaveTypes)],
                    'start_date' => $start,
                    'end_date' => $start->copy()->addDays($days),
                    'total_days' => $days,
                    'reason' => 'Personal reasons',
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $approvedAt = $createdAt->copy()->addHours(rand(1, 48));

                Approval::create([
                    'leave_request_id' => $leave->id,
                    'approver_id' => $admin->id,
                    'status' => $status,
                    'notes' => $status === 'rejected' ? 'Department understaffed during this period' : null,
                    'created_at' => $approvedAt,
                    'updated_at' => $approvedAt,
                ]);

                if ($status === 'pending') {
                    SlaRecord::create([
                        'leave_request_id' => $leave->id,
                        'deadline' => Carbon::now()->addHours(rand(-5, 24)),
                        'breached' => rand(0, 1) === 1,
                    ]);
                }
            }
        }

        // ===== Delegations =====
        Delegation::create([
            'delegator_id' => $admin->id,
            'delegate_id' => $employees[0]->id, // Arya Pradana
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->addDays(3),
            'permissions' => ['approve_leave', 'view_reports'],
        ]);

        Delegation::create([
            'delegator_id' => $manager->id,
            'delegate_id' => $employees[1]->id, // Zaki Zakariyya
            'start_date' => Carbon::now()->addDays(10),
            'end_date' => Carbon::now()->addDays(25),
            'permissions' => ['approve_leave'],
        ]);

        Delegation::create([
            'delegator_id' => $employees[11]->id, // Fajar Nugroho (Engineering Manager)
            'delegate_id' => $employees[2]->id, // Alifito Rabbani
            'start_date' => Carbon::now()->subDays(40),
            'end_date' => Carbon::now()->subDays(30),
            'permissions' => ['approve_leave', 'view_reports', 'manage_team'],
        ]);

        // ===== Notifications =====
        NotificationItem::create([
            'user_id' => $admin->id,
            'title' => 'New leave request',
            'message' => 'Muhammad Arya Pradana submitted a new leave request',
            'is_read' => false,
        ]);

        NotificationItem::create([
            'user_id' => $admin->id,
            'title' => 'SLA breach warning',
            'message' => 'A leave request is approaching SLA breach',
            'is_read' => false,
        ]);

        $this->command->info('Database seeded successfully with Indonesian employee data!');
        $this->command->info('Login: admin@company.com / password');
    }
}
