<?php $__env->startSection('title', 'Rekap Absensi per Siswa'); ?>
<?php $__env->startSection('content'); ?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-2" method="GET">
            <?php if(!$isSiswa): ?>
            <div class="col-md-4">
                <label class="form-label">Siswa</label>
                <select name="siswa_id" class="form-select" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php $__currentLoopData = $siswaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id); ?>" <?php if($siswaId == $s->id): echo 'selected'; endif; ?>><?php echo e($s->nama_siswa); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" value="<?php echo e($tanggalMulai); ?>" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="tanggal_selesai" value="<?php echo e($tanggalSelesai); ?>" class="form-control" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<?php if($records->isNotEmpty()): ?>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Tanggal</th><th>Status</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e(\Illuminate\Support\Carbon::parse($r->tanggal)->translatedFormat('d M Y')); ?></td>
                    <td><span class="badge bg-<?php echo e($r->status === 'Hadir' ? 'success' : ($r->status === 'Alpa' ? 'danger' : 'warning')); ?>"><?php echo e($r->status); ?></span></td>
                    <td><?php echo e($r->keterangan); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif($siswaId): ?>
<div class="alert alert-info">Tidak ada data pada rentang tanggal tersebut.</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\web\jurnal\resources\views\absensi\rekap-siswa.blade.php ENDPATH**/ ?>