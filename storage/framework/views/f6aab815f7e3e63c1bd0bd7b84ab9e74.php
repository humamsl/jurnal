<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>

<p class="text-muted">Selamat datang, <strong><?php echo e(auth()->user()->name); ?></strong>.</p>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center py-3">
            <i class="bi bi-person-badge fs-3 text-primary"></i>
            <div class="fs-4 fw-bold"><?php echo e($stats['pegawai']); ?></div>
            <div class="text-muted small">Pegawai</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center py-3">
            <i class="bi bi-people fs-3 text-success"></i>
            <div class="fs-4 fw-bold"><?php echo e($stats['siswa']); ?></div>
            <div class="text-muted small">Siswa</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center py-3">
            <i class="bi bi-journal-bookmark fs-3 text-warning"></i>
            <div class="fs-4 fw-bold"><?php echo e($stats['mata_pelajaran']); ?></div>
            <div class="text-muted small">Mata Pelajaran</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center py-3">
            <i class="bi bi-easel fs-3 text-info"></i>
            <div class="fs-4 fw-bold"><?php echo e($stats['guru_mapel']); ?></div>
            <div class="text-muted small">Penugasan Guru Mapel</div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(!auth()->user()->isAdmin()): ?>
<div class="card shadow-sm">
    <div class="card-header bg-white">Tugas Terbaru</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Judul</th><th>Mata Pelajaran</th><th>Kelas</th><th>Batas Akhir</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $tugasTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($t->judul); ?></td>
                    <td><?php echo e($t->mataPelajaran->nama_mapel ?? '-'); ?></td>
                    <td><?php echo e($t->kelas->nama_rombel ?? '-'); ?></td>
                    <td><?php echo e(optional($t->tanggal_selesai)->translatedFormat('d M Y') ?? '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada tugas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\web\jurnal\resources\views/dashboard/index.blade.php ENDPATH**/ ?>