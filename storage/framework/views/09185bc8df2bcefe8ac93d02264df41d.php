<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - <?php echo e(config('app.name')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           Tema mengikuti gaya project Datacenter/CBT:
           brand teal, sidebar biru pekat, kartu membulat shadow lembut.
           Markup halaman tetap Bootstrap — hanya kulitnya yang diubah.
           ============================================================ */
        :root {
            --brand: #0d9488;
            --brand-50: #f0fdfa;
            --brand-100: #ccfbf1;
            --brand-500: #14b8a6;
            --brand-600: #0d9488;
            --brand-700: #0f766e;
            --sidebar-bg: #062275;
            --ink-500: #64748b;
            --ink-900: #0f172a;
            --shadow-soft: 0 2px 10px rgba(15, 23, 42, .06);
            --shadow-card: 0 1px 3px rgba(15, 23, 42, .05), 0 8px 24px -12px rgba(15, 23, 42, .12);
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #1e293b;
            background-color: #f4fbfa;
            background-image:
                radial-gradient(at 0% 0%, rgba(64,184,166,0.07) 0px, transparent 45%),
                radial-gradient(at 100% 0%, rgba(16,185,129,0.06) 0px, transparent 42%),
                radial-gradient(at 100% 100%, rgba(56,189,248,0.05) 0px, transparent 45%);
            background-attachment: fixed;
        }
        ::selection { background: rgba(20,184,166,.22); }
        @media (min-width: 768px) {
            ::-webkit-scrollbar { width: 8px; height: 8px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #b8e3dd; border-radius: 999px; }
            ::-webkit-scrollbar-thumb:hover { background: #5eead4; }
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 260px; height: 100vh; position: fixed; top: 0; left: 0;
            overflow-y: auto; background: var(--sidebar-bg); color: #fff;
            border-right: 1px solid rgba(0,0,0,.05); z-index: 1030;
        }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); }
        .sidebar a { color: #fff; text-decoration: none; }
        .sidebar .brand {
            display: flex; align-items: center; gap: .75rem;
            color: #fff; font-weight: 700; padding: 0 1.25rem; height: 4rem;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .sidebar .brand .brand-logo {
            width: 2.5rem; height: 2.5rem; border-radius: .75rem; flex-shrink: 0;
            background: rgba(255,255,255,.2); box-shadow: 0 0 0 2px rgba(255,255,255,.4);
            display: grid; place-items: center; font-weight: 800;
        }
        .sidebar .brand small { display: block; font-size: 11px; font-weight: 400; opacity: .85; }
        .sidebar nav { padding: .75rem; }
        .sidebar .nav-link {
            display: flex; align-items: center; gap: .35rem;
            border-radius: .75rem; padding: .625rem .75rem; margin-bottom: 2px;
            font-size: .875rem; font-weight: 500; color: #fff; position: relative;
            transition: background-color .15s;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.15); color: #fff; }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.2); color: #fff; font-weight: 600;
            box-shadow: inset 3px 0 0 rgba(255,255,255,.9);
        }
        .sidebar .nav-link.active::after {
            content: ''; position: absolute; right: .75rem;
            width: 6px; height: 6px; border-radius: 999px; background: #fff;
        }
        .menu-group-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .12em; color: #fff; opacity: .9;
            padding: 1.25rem .75rem .4rem;
        }
        /* Grup menu yang bisa dilipat (accordion) */
        .menu-toggle { width: 100%; text-align: left; background: transparent; border: 0; color: #fff; }
        .menu-toggle .chev { transition: transform .2s; font-size: .7rem; }
        .menu-toggle.collapsed .chev { transform: rotate(-90deg); }
        .menu-sub .nav-link { padding-left: 2.25rem; font-size: .84rem; }
        .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.45); z-index: 1039; }
        .sidebar-backdrop.show { display: block; }
        .content-wrapper { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .content-wrapper > .page-body { flex: 1; }
        @media (max-width: 991.98px) {
            .sidebar { left: -260px; transition: left .2s; z-index: 1040; }
            .sidebar.show { left: 0; }
            .content-wrapper { margin-left: 0; }
        }

        /* ===== Topbar ===== */
        .topbar {
            position: sticky; top: 0; z-index: 1020; height: 4rem;
            background: rgba(255,255,255,.95); backdrop-filter: blur(8px);
            border-bottom: 1px solid #f1f5f9;
        }
        .topbar .page-eyebrow { font-size: .75rem; color: var(--ink-500); display: flex; align-items: center; gap: .4rem; }
        .topbar .page-eyebrow::before { content: ''; width: 4px; height: 4px; border-radius: 999px; background: var(--brand-500); }
        .topbar .page-title { font-size: 1.05rem; font-weight: 600; color: var(--ink-900); line-height: 1.2; margin: 0; }
        .avatar-initials {
            width: 36px; height: 36px; border-radius: 999px; flex-shrink: 0;
            background: var(--brand-600); color: #fff;
            display: grid; place-items: center; font-size: .75rem; font-weight: 700;
            box-shadow: 0 0 0 2px #fff, var(--shadow-soft);
        }
        .badge-role {
            font-size: 10px; font-weight: 700; letter-spacing: .08em;
            background: #fff1f2 !important; color: #be123c !important;
            box-shadow: inset 0 0 0 1px #fecdd3;
        }

        /* ===== Kartu ===== */
        .card {
            border: 1px solid #f1f5f9; border-radius: 1rem;
            box-shadow: var(--shadow-card);
            transition: box-shadow .25s, transform .25s, border-color .25s;
        }
        .card-header {
            background: transparent; border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.25rem; font-weight: 600;
        }
        .card-footer { background: transparent; border-top: 1px solid #f1f5f9; }

        /* ===== Tombol ===== */
        .btn { border-radius: .75rem; font-weight: 600; font-size: .875rem; }
        .btn:active { transform: scale(.98); }
        .btn-primary {
            border: 0; color: #fff;
            background: linear-gradient(135deg, var(--brand-500) 0%, #047857 100%);
            background-size: 200% 200%;
            box-shadow: var(--shadow-soft);
            transition: background-position .35s, transform .15s, box-shadow .25s;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-position: 100% 0; transform: translateY(-1px);
            background-image: linear-gradient(135deg, var(--brand-500) 0%, #047857 100%);
            box-shadow: 0 4px 16px rgba(20,184,166,.35);
        }
        .btn-outline-primary { color: var(--brand-700); border-color: var(--brand-500); }
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background: var(--brand-600); border-color: var(--brand-600); color: #fff;
        }
        .btn-danger { border: 0; background: linear-gradient(135deg, #fb7185, #be123c); }
        .btn-danger:hover { transform: translateY(-1px); }
        .btn-success { border: 0; background: linear-gradient(135deg, #10b981, #047857); }
        .btn-light, .btn-outline-secondary { border-radius: .75rem; }
        .text-primary { color: var(--brand-600) !important; }
        .bg-primary { background-color: var(--brand-600) !important; }
        a { color: var(--brand-700); }

        /* ===== Form ===== */
        .form-control, .form-select {
            border-radius: .75rem; border-color: #e2e8f0; font-size: .875rem;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-500);
            box-shadow: 0 0 0 .25rem rgba(20,184,166,.12);
        }
        .form-label { font-weight: 500; font-size: .875rem; color: #334155; }
        .select2-container--bootstrap-5 .select2-selection { border-radius: .75rem !important; border-color: #e2e8f0 !important; }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: var(--brand-500) !important;
            box-shadow: 0 0 0 .25rem rgba(20,184,166,.12) !important;
        }

        /* ===== Tabel ===== */
        .table thead th {
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
            color: var(--ink-500);
            background: linear-gradient(to bottom, rgba(240,253,250,.7), #f8fafc);
            border-bottom: 1px solid #e2e8f0;
        }
        .table tbody td { border-color: #f1f5f9; vertical-align: middle; }
        .table-hover tbody tr:hover, .table tbody tr:hover { background: rgba(240,253,250,.6); }

        /* ===== Badge & alert ===== */
        .badge { border-radius: 999px; font-weight: 600; }
        .alert { border-radius: 1rem; box-shadow: var(--shadow-soft); }
        .alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
        .alert-danger { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }

        .pagination .page-link { color: var(--brand-700); border-radius: .5rem; margin: 0 2px; border-color: #e2e8f0; }
        .pagination .page-item.active .page-link {
            background: var(--brand-600); border-color: var(--brand-600); color: #fff;
        }

        .app-footer {
            padding: 1.25rem 1.5rem; font-size: .75rem; color: var(--ink-500);
            border-top: 1px solid #f1f5f9; background: #fff;
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-logo"><i class="bi bi-mortarboard-fill"></i></div>
        <div>
            SIM Kurikulum
            <small>Jurnal &amp; Kurikulum Digital</small>
        </div>
        <button type="button" class="btn btn-sm text-white d-lg-none p-0 ms-auto" onclick="tutupSidebar()" aria-label="Tutup menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <nav class="nav flex-column" id="sidebarNav">
        <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
        <?php ($open = request()->routeIs('setting.*')); ?>
        <button class="menu-toggle nav-link d-flex align-items-center <?php echo e($open ? '' : 'collapsed'); ?>" data-bs-toggle="collapse" data-bs-target="#grpSetting">
            <i class="bi bi-gear me-2"></i>Setting Sekolah <i class="bi bi-chevron-down ms-auto chev"></i>
        </button>
        <div class="collapse menu-sub <?php echo e($open ? 'show' : ''); ?>" id="grpSetting" data-bs-parent="#sidebarNav">
            <a class="nav-link <?php echo e(request()->routeIs('setting.identitas*') ? 'active':''); ?>" href="<?php echo e(route('setting.identitas.edit')); ?>"><i class="bi bi-building me-2"></i>Identitas Sekolah</a>
            <a class="nav-link <?php echo e(request()->routeIs('setting.jam-mengajar*') ? 'active':''); ?>" href="<?php echo e(route('setting.jam-mengajar.index')); ?>"><i class="bi bi-clock me-2"></i>Jam Mengajar</a>
            <a class="nav-link <?php echo e(request()->routeIs('setting.mata-pelajaran*') ? 'active':''); ?>" href="<?php echo e(route('setting.mata-pelajaran.index')); ?>"><i class="bi bi-journal-bookmark me-2"></i>Mata Pelajaran</a>
            <a class="nav-link <?php echo e(request()->routeIs('setting.jenis-eskul*') ? 'active':''); ?>" href="<?php echo e(route('setting.jenis-eskul.index')); ?>"><i class="bi bi-trophy me-2"></i>Jenis Eskul</a>
            <a class="nav-link <?php echo e(request()->routeIs('setting.tingkat-kelas*') ? 'active':''); ?>" href="<?php echo e(route('setting.tingkat-kelas.index')); ?>"><i class="bi bi-stack me-2"></i>Tingkat Kelas</a>
            <a class="nav-link <?php echo e(request()->routeIs('setting.jurusan*') ? 'active':''); ?>" href="<?php echo e(route('setting.jurusan.index')); ?>"><i class="bi bi-diagram-3 me-2"></i>Jurusan</a>
            <a class="nav-link <?php echo e(request()->routeIs('setting.tahun-ajaran*') ? 'active':''); ?>" href="<?php echo e(route('setting.tahun-ajaran.index')); ?>"><i class="bi bi-calendar-range me-2"></i>Tahun Ajaran</a>
            <a class="nav-link <?php echo e(request()->routeIs('setting.kelas*') ? 'active':''); ?>" href="<?php echo e(route('setting.kelas.index')); ?>"><i class="bi bi-door-open me-2"></i>Kelas / Rombel</a>
        </div>

        <?php ($open = request()->routeIs('kepegawaian.*')); ?>
        <button class="menu-toggle nav-link d-flex align-items-center <?php echo e($open ? '' : 'collapsed'); ?>" data-bs-toggle="collapse" data-bs-target="#grpKepegawaian">
            <i class="bi bi-person-badge me-2"></i>Kepegawaian <i class="bi bi-chevron-down ms-auto chev"></i>
        </button>
        <div class="collapse menu-sub <?php echo e($open ? 'show' : ''); ?>" id="grpKepegawaian" data-bs-parent="#sidebarNav">
            <a class="nav-link <?php echo e(request()->routeIs('kepegawaian.pegawai*') ? 'active':''); ?>" href="<?php echo e(route('kepegawaian.pegawai.index')); ?>"><i class="bi bi-person-badge me-2"></i>Data Pegawai</a>
            <a class="nav-link <?php echo e(request()->routeIs('kepegawaian.wali-kelas*') ? 'active':''); ?>" href="<?php echo e(route('kepegawaian.wali-kelas.index')); ?>"><i class="bi bi-person-check me-2"></i>Data Wali Kelas</a>
            <a class="nav-link <?php echo e(request()->routeIs('kepegawaian.guru-mapel*') ? 'active':''); ?>" href="<?php echo e(route('kepegawaian.guru-mapel.index')); ?>"><i class="bi bi-easel me-2"></i>Data Guru Mata Pelajaran</a>
        </div>

        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['admin','siswa'])): ?>
        <?php ($open = request()->routeIs('kesiswaan.*')); ?>
        <button class="menu-toggle nav-link d-flex align-items-center <?php echo e($open ? '' : 'collapsed'); ?>" data-bs-toggle="collapse" data-bs-target="#grpKesiswaan">
            <i class="bi bi-people me-2"></i>Kesiswaan <i class="bi bi-chevron-down ms-auto chev"></i>
        </button>
        <div class="collapse menu-sub <?php echo e($open ? 'show' : ''); ?>" id="grpKesiswaan" data-bs-parent="#sidebarNav">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
            <a class="nav-link <?php echo e(request()->routeIs('kesiswaan.siswa*') ? 'active':''); ?>" href="<?php echo e(route('kesiswaan.siswa.index')); ?>"><i class="bi bi-people me-2"></i>Data Siswa</a>
            <?php endif; ?>
            <a class="nav-link <?php echo e(request()->routeIs('kesiswaan.orang-tua*') ? 'active':''); ?>" href="<?php echo e(route('kesiswaan.orang-tua.index')); ?>"><i class="bi bi-person-hearts me-2"></i>Data Orang Tua</a>
        </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['admin','guru'])): ?>
        <?php ($open = request()->routeIs('kurikulum.*')); ?>
        <button class="menu-toggle nav-link d-flex align-items-center <?php echo e($open ? '' : 'collapsed'); ?>" data-bs-toggle="collapse" data-bs-target="#grpKurikulum">
            <i class="bi bi-journal-bookmark me-2"></i>Kurikulum <i class="bi bi-chevron-down ms-auto chev"></i>
        </button>
        <div class="collapse menu-sub <?php echo e($open ? 'show' : ''); ?>" id="grpKurikulum" data-bs-parent="#sidebarNav">
            <a class="nav-link <?php echo e(request()->routeIs('kurikulum.jadwal*') ? 'active':''); ?>" href="<?php echo e(route('kurikulum.jadwal.index')); ?>"><i class="bi bi-calendar-week me-2"></i>Jadwal Mengajar Guru</a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
            <a class="nav-link <?php echo e(request()->routeIs('kurikulum.agenda*') ? 'active':''); ?>" href="<?php echo e(route('kurikulum.agenda.index')); ?>"><i class="bi bi-journal-check me-2"></i>Agenda Mengajar</a>
            <?php endif; ?>
            <a class="nav-link <?php echo e(request()->routeIs('kurikulum.cp-tp-atp*') ? 'active':''); ?>" href="<?php echo e(route('kurikulum.cp-tp-atp.index')); ?>"><i class="bi bi-diagram-2 me-2"></i>Pemetaan CP-TP-ATP</a>
            <a class="nav-link <?php echo e(request()->routeIs('kurikulum.modul-ajar*') ? 'active':''); ?>" href="<?php echo e(route('kurikulum.modul-ajar.index')); ?>"><i class="bi bi-file-earmark-text me-2"></i>Modul Ajar Digital</a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
            <a class="nav-link <?php echo e(request()->routeIs('kurikulum.alokasi-jam*') ? 'active':''); ?>" href="<?php echo e(route('kurikulum.alokasi-jam.index')); ?>"><i class="bi bi-hourglass-split me-2"></i>Alokasi Jam Mapel</a>
            <a class="nav-link <?php echo e(request()->routeIs('kurikulum.analisis-guru*') ? 'active':''); ?>" href="<?php echo e(route('kurikulum.analisis-guru.index')); ?>"><i class="bi bi-bar-chart me-2"></i>Analisis Kebutuhan Guru</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php ($open = request()->routeIs('ruangbelajar.*')); ?>
        <button class="menu-toggle nav-link d-flex align-items-center <?php echo e($open ? '' : 'collapsed'); ?>" data-bs-toggle="collapse" data-bs-target="#grpRuangBelajar">
            <i class="bi bi-easel me-2"></i>Ruang Belajar <i class="bi bi-chevron-down ms-auto chev"></i>
        </button>
        <div class="collapse menu-sub <?php echo e($open ? 'show' : ''); ?>" id="grpRuangBelajar" data-bs-parent="#sidebarNav">
            <a class="nav-link <?php echo e(request()->routeIs('ruangbelajar.materi*') ? 'active':''); ?>" href="<?php echo e(route('ruangbelajar.materi.index')); ?>"><i class="bi bi-play-btn me-2"></i>Materi Online</a>
            <a class="nav-link <?php echo e(request()->routeIs('ruangbelajar.tugas*') ? 'active':''); ?>" href="<?php echo e(route('ruangbelajar.tugas.index')); ?>"><i class="bi bi-clipboard-check me-2"></i>Tugas</a>
        </div>

        <?php if (! (auth()->user()->isGuru())): ?>
        <?php ($open = request()->routeIs('absensi.*')); ?>
        <button class="menu-toggle nav-link d-flex align-items-center <?php echo e($open ? '' : 'collapsed'); ?>" data-bs-toggle="collapse" data-bs-target="#grpAbsensi">
            <i class="bi bi-clipboard2-check me-2"></i>Absensi <i class="bi bi-chevron-down ms-auto chev"></i>
        </button>
        <div class="collapse menu-sub <?php echo e($open ? 'show' : ''); ?>" id="grpAbsensi" data-bs-parent="#sidebarNav">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
            <a class="nav-link <?php echo e(request()->routeIs('absensi.rekap-kelas*') ? 'active':''); ?>" href="<?php echo e(route('absensi.rekap-kelas.index')); ?>"><i class="bi bi-table me-2"></i>Rekap per Kelas</a>
            <?php endif; ?>
            <a class="nav-link <?php echo e(request()->routeIs('absensi.rekap-siswa*') ? 'active':''); ?>" href="<?php echo e(route('absensi.rekap-siswa.index')); ?>"><i class="bi bi-person-lines-fill me-2"></i>Rekap per Siswa</a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
            <a class="nav-link <?php echo e(request()->routeIs('absensi.koreksi*') ? 'active':''); ?>" href="<?php echo e(route('absensi.koreksi.index')); ?>"><i class="bi bi-pencil-square me-2"></i>Koreksi Absensi</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
        <?php ($open = request()->routeIs('users.*')); ?>
        <button class="menu-toggle nav-link d-flex align-items-center <?php echo e($open ? '' : 'collapsed'); ?>" data-bs-toggle="collapse" data-bs-target="#grpUsers">
            <i class="bi bi-shield-lock me-2"></i>Manajemen User <i class="bi bi-chevron-down ms-auto chev"></i>
        </button>
        <div class="collapse menu-sub <?php echo e($open ? 'show' : ''); ?>" id="grpUsers" data-bs-parent="#sidebarNav">
            <a class="nav-link <?php echo e(request()->routeIs('users.admin*') ? 'active':''); ?>" href="<?php echo e(route('users.admin.index')); ?>"><i class="bi bi-shield-lock me-2"></i>User Admin</a>
            <a class="nav-link <?php echo e(request()->routeIs('users.siswa*') ? 'active':''); ?>" href="<?php echo e(route('users.siswa.index')); ?>"><i class="bi bi-person-vcard me-2"></i>User Siswa</a>
            <a class="nav-link <?php echo e(request()->routeIs('users.guru*') ? 'active':''); ?>" href="<?php echo e(route('users.guru.index')); ?>"><i class="bi bi-person-workspace me-2"></i>User Guru</a>
        </div>
        <?php endif; ?>
    </nav>
</div>

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="tutupSidebar()"></div>


<?php ($userName = auth()->user()->name); ?>
<?php ($userInitials = collect(preg_split('/\s+/', trim($userName)))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('')); ?>

<div class="content-wrapper">
    <div class="topbar d-flex align-items-center justify-content-between px-3 px-md-4">
        <div class="d-flex align-items-center gap-2 min-w-0">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="bukaTutupSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="min-w-0">
                <div class="page-eyebrow">Beranda</div>
                <h1 class="page-title text-truncate"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <span class="avatar-initials"><?php echo e($userInitials); ?></span>
                <span class="d-none d-sm-inline text-start">
                    <span class="d-block fw-semibold text-dark" style="font-size:.875rem; line-height:1.2;"><?php echo e($userName); ?></span>
                    <span class="badge badge-role text-uppercase"><?php echo e(auth()->user()->role); ?></span>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:1rem;">
                <li>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <div class="page-body p-3 p-md-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <footer class="app-footer">
        &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?> &mdash; SIM Kurikulum.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Buka/tutup sidebar di layar kecil (HP/tablet) + backdrop untuk menutup
    // dengan tap di luar menu.
    function bukaTutupSidebar() {
        var terbuka = document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarBackdrop').classList.toggle('show', terbuka);
    }
    function tutupSidebar() {
        document.getElementById('sidebar').classList.remove('show');
        document.getElementById('sidebarBackdrop').classList.remove('show');
    }

    // Semua elemen <select class="form-select"> di seluruh aplikasi otomatis
    // dijadikan Select2 (dropdown dengan pencarian), termasuk yang di-load
    // lewat halaman baru maupun ditambahkan lewat AJAX/partial di kemudian hari.
    function initSelect2(context) {
        $(context || document).find('select.form-select').each(function () {
            var $sel = $(this);
            if ($sel.hasClass('select2-hidden-accessible')) {
                return; // sudah diinisialisasi
            }
            var placeholderText = $sel.find('option[value=""]').first().text() || 'Pilih...';
            $sel.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholderText,
                allowClear: !$sel.prop('required'),
                language: {
                    noResults: function () { return 'Tidak ada hasil ditemukan'; },
                    searching: function () { return 'Mencari...'; }
                }
            });
        });
    }

    $(function () {
        initSelect2(document);
    });
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\laragon\www\web\jurnal\resources\views/layouts/app.blade.php ENDPATH**/ ?>