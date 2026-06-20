# LeaveSphere — HR Leave Management System

Sistem manajemen cuti karyawan berbasis web, dibangun dengan Laravel 12 + Tailwind CSS + Alpine.js.
Dibuat oleh **Kelompok 7** — Telkom University.

Project ini adalah **starter kit lengkap** untuk panel **Admin/Manager (Web)**. Versi mobile untuk karyawan akan dibangun terpisah (Flutter).

---

## ✅ Fitur yang Sudah Tersedia

| Halaman | Fitur |
|---|---|
| **Dashboard** | Stats cards, Pending Approvals widget, SLA Risk Alerts, Leave Spike Prediction chart, Department Load chart |
| **Leave Approvals** | Filter (status/dept/type), search, tabel approval, modal approve & reject |
| **Smart Recommendations** | AI insights (spike prediction, conflict detection, risk scoring, pattern analysis) |
| **Team Calendar** | FullCalendar bulan/minggu/hari, color-coded by leave type, conflict detection |
| **Delegation Management** | Tabel delegasi, permission checklist, audit log, modal create |
| **SLA Monitoring** | SLA table dengan status warna (safe/warning/breached), department performance chart |
| **Analytics & Insights** | KPI cards, monthly trends, department comparison, leave type pie chart, export PDF/Excel |
| **Workflows** | Visual approval flow builder per departemen |
| **Users** | CRUD user, import/export, reset password, role management |
| **Settings** | General, notifications, leave policy, SLA config, security |
| **Auth** | Login page dengan dark glassmorphism design |

UI mengikuti referensi desain pada laporan (LeaveSphere HR Analytics — clean, modern, dark sidebar, light content area).

---

## 🚀 Cara Menjalankan Project

### 1. Requirement
- PHP >= 8.2
- Composer
- MySQL / MariaDB (atau SQLite untuk testing cepat)
- Node.js (opsional, untuk asset build — saat ini pakai CDN Tailwind/Alpine/Chart.js/FullCalendar)

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`, sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leavesphere
DB_USERNAME=root
DB_PASSWORD=
```

> **Alternatif cepat (tanpa setup MySQL):** gunakan SQLite.
> ```bash
> touch database/database.sqlite
> ```
> lalu ubah `.env`:
> ```env
> DB_CONNECTION=sqlite
> ```

### 4. Buat Database

```sql
CREATE DATABASE leavesphere;
```

### 5. Migrate & Seed

```bash
php artisan migrate --seed
```

Ini akan membuat seluruh tabel + 150 dummy users, leave requests, approvals, delegations, workflows, dan SLA records.

### 6. Jalankan Server

```bash
php artisan serve
```

Buka **http://localhost:8000**

### 7. Login

| Role | Email | Password |
|---|---|---|
| Super Admin | `admin@company.com` | `password` |
| Manager | `manager@company.com` | `password` |

---

## 📦 Struktur Project

```
leavesphere/
├── app/
│   ├── Http/
│   │   ├── Controllers/      → 11 controllers (Dashboard, Approval, Recommendation, dll)
│   │   └── Middleware/       → CheckRole (role-based access)
│   └── Models/                → User, Department, LeaveRequest, Approval, Delegation,
│                                 Workflow, WorkflowStep, SlaRecord, NotificationItem
├── database/
│   ├── migrations/            → 9 migration files (sesuai skema di laporan)
│   └── seeders/               → DatabaseSeeder.php (dummy data lengkap)
├── resources/views/
│   ├── layouts/app.blade.php  → Sidebar + Header utama
│   ├── auth/login.blade.php
│   ├── dashboard/
│   ├── approvals/
│   ├── recommendations/
│   ├── calendar/
│   ├── delegation/
│   ├── sla/
│   ├── analytics/
│   ├── workflows/
│   ├── users/
│   └── settings/
└── routes/web.php             → Semua route terdaftar dengan named routes
```

---

## 🔧 Teknologi

- **Backend:** Laravel 12, Eloquent ORM
- **Frontend:** Blade + Tailwind CSS (CDN) + Alpine.js (CDN)
- **Charts:** Chart.js (CDN)
- **Calendar:** FullCalendar (CDN)
- **Icons:** Lucide Icons (CDN)

> Catatan: Untuk production, sebaiknya pindahkan Tailwind/Alpine dari CDN ke Vite build process (`npm install && npm run build`) demi performa lebih baik.

---

## 📝 Yang Masih Perlu Dikembangkan (TODO)

1. **Export PDF/Excel sungguhan** — saat ini placeholder. Install:
   ```bash
   composer require barryvdh/laravel-dompdf
   composer require maatwebsite/excel
   ```
2. **Import CSV/Excel user sungguhan** — gunakan `maatwebsite/excel` Import classes.
3. **Spatie Laravel Permission** — untuk role/permission lebih granular (sudah ada di composer.json, tinggal `php artisan vendor:publish` + migrate).
4. **Real-time notification** — bisa pakai Laravel Echo + Pusher/Reverb.
5. **AI Smart Recommendations engine** — saat ini UI statis, perlu logic backend (analisis pattern dari `leave_requests` table).
6. **Mobile app (Flutter)** — untuk role Employee, terpisah dari project ini.

---

## 👥 Tim Pengembang — Kelompok 7

| Role | Nama | NIM |
|---|---|---|
| Project Manager | Dafah Nilamanta D. | 103012300031 |
| System Analyst | Alfian Riffat A. | 103012300491 |
| Backend Dev | Alifito Rabbani C. | 103012300477 |
| Frontend Web Dev | Alifito Rabbani C. | 103012300477 |
| Mobile Developer | Alifito Rabbani C. | 103012300477 |
| AI Engineer | Alifito Rabbani C. | 103012300477 |
| DevOps Engineer | Muhammad Arya P. | 103012300367 |
| Technical Writer | Muhammad Zaki Z. | 103012300476 |
| QA Engineer | Hanif Ghasanof | 103012300236 |
