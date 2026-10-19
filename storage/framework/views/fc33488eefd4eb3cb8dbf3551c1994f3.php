<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('content'); ?>
<?php
    $lampiranUrl = $tugas->file_lampiran ? Storage::url($tugas->file_lampiran) : null;
    $dibuka = $tugas->masihDibuka();
?>

<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="<?php echo e(route($routeName.'.index')); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <?php if($canManage): ?>
    <a href="<?php echo e(route($routeName.'.edit', $tugas->id)); ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Ubah Tugas</a>
    <?php endif; ?>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>


<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
            <h2 class="h5 fw-bold mb-0"><?php echo e($tugas->judul); ?></h2>
            <?php if($dibuka): ?>
                <span class="badge bg-success align-self-start">Masih dibuka</span>
            <?php else: ?>
                <span class="badge bg-secondary align-self-start">Batas waktu lewat</span>
            <?php endif; ?>
        </div>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge bg-primary"><?php echo e(optional($tugas->mataPelajaran)->nama_mapel ?? '-'); ?></span>
            <span class="badge text-bg-light border"><?php echo e(optional($tugas->kelas)->nama_rombel ?? '-'); ?></span>
        </div>
        <div class="row g-2 small text-muted mb-3">
            <div class="col-sm-4"><i class="bi bi-person me-1"></i><?php echo e(optional($tugas->pegawai)->nama_ptk ?? '-'); ?></div>
            <div class="col-sm-4"><i class="bi bi-calendar-event me-1"></i>Mulai: <?php echo e($tugas->tanggal_mulai?->format('d/m/Y') ?? '-'); ?></div>
            <div class="col-sm-4"><i class="bi bi-alarm me-1"></i>Batas: <?php echo e($tugas->tanggal_selesai?->format('d/m/Y') ?? 'Tanpa batas'); ?></div>
        </div>

        <h3 class="h6 fw-semibold">Instruksi</h3>
        <?php if($tugas->deskripsi): ?>
            <div class="mb-3"><?php echo \App\Support\TeksKaya::html($tugas->deskripsi); ?></div>
        <?php else: ?>
            <div class="text-muted fst-italic mb-3">Tidak ada instruksi tertulis.</div>
        <?php endif; ?>

        <?php if($lampiranUrl): ?>
            <a href="<?php echo e($lampiranUrl); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-paperclip me-1"></i>Unduh Lampiran</a>
        <?php endif; ?>
    </div>
</div>

<?php if(auth()->user()->isSiswa()): ?>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <span><i class="bi bi-upload me-2"></i>Pengumpulan Saya</span>
        <?php if(! $pengumpulan): ?>
            <span class="badge bg-warning text-dark">Belum mengumpulkan</span>
        <?php elseif($pengumpulan->sudahDinilai()): ?>
            <span class="badge bg-success">Dinilai</span>
        <?php elseif($pengumpulan->terlambat()): ?>
            <span class="badge bg-danger">Terlambat</span>
        <?php else: ?>
            <span class="badge bg-info text-dark">Sudah dikumpulkan</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if($pengumpulan): ?>
            <div class="small text-muted mb-3">
                Dikumpulkan: <?php echo e($pengumpulan->dikumpulkan_at?->format('d/m/Y H:i')); ?>

            </div>
        <?php endif; ?>

        <?php if($pengumpulan && $pengumpulan->sudahDinilai()): ?>
            <div class="p-3 rounded mb-3" style="background: var(--brand-50);">
                <div class="small text-muted">Nilai</div>
                <div class="display-6 fw-bold text-primary"><?php echo e(rtrim(rtrim(number_format($pengumpulan->nilai, 2, ',', ''), '0'), ',')); ?></div>
                <?php if($pengumpulan->catatan_guru): ?>
                    <div class="mt-2"><span class="small text-muted d-block">Catatan guru</span><?php echo \App\Support\TeksKaya::html($pengumpulan->catatan_guru); ?></div>
                <?php endif; ?>
            </div>
            <?php if($pengumpulan->jawaban): ?>
                <h3 class="h6 fw-semibold">Jawaban</h3>
                <div class="mb-3"><?php echo \App\Support\TeksKaya::html($pengumpulan->jawaban); ?></div>
            <?php endif; ?>
            <?php if($pengumpulan->file): ?>
                <a href="<?php echo e(Storage::url($pengumpulan->file)); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark me-1"></i>File Jawaban</a>
            <?php endif; ?>
        <?php else: ?>
            <?php if (! ($dibuka)): ?>
                <div class="alert alert-warning py-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>Batas waktu sudah lewat. Tugas masih bisa dikumpulkan, tetapi akan ditandai <strong>terlambat</strong>.
                </div>
            <?php endif; ?>
            <form action="<?php echo e(route($routeName.'.kumpulkan', $tugas->id)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Jawaban</label>
                    <textarea name="jawaban" rows="6" class="form-control" placeholder="Tulis jawaban Anda di sini..."><?php echo e(old('jawaban', $pengumpulan?->jawaban)); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">File Jawaban <span class="text-muted small">(opsional, maks. 10 MB)</span></label>
                    <input type="file" name="file" class="form-control"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar">
                    <?php if($pengumpulan?->file): ?>
                        <div class="form-text">
                            File saat ini: <a href="<?php echo e(Storage::url($pengumpulan->file)); ?>" target="_blank">lihat file</a>. Unggah file baru untuk menggantinya.
                        </div>
                    <?php endif; ?>
                </div>
                <button class="btn btn-primary">
                    <i class="bi bi-send me-1"></i><?php echo e($pengumpulan ? 'Perbarui Jawaban' : 'Kumpulkan Tugas'); ?>

                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <span><i class="bi bi-people me-2"></i>Pengumpulan Siswa</span>
        <span class="badge bg-primary"><?php echo e($jumlahMengumpulkan); ?> / <?php echo e($daftar->count()); ?> mengumpulkan</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Nama Siswa</th>
                    <th>Status</th>
                    <th>Jawaban</th>
                    <th style="min-width:280px;">Nilai &amp; Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $siswa = $row['siswa'];
                        $p = $row['pengumpulan'];
                    ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td>
                            <div class="fw-semibold"><?php echo e($siswa->nama_siswa); ?></div>
                            <div class="small text-muted"><?php echo e($siswa->nisn ?: '-'); ?></div>
                        </td>
                        <td class="text-nowrap">
                            <?php if(! $p): ?>
                                <span class="badge bg-warning text-dark">Belum</span>
                            <?php else: ?>
                                <?php if($p->sudahDinilai()): ?>
                                    <span class="badge bg-success">Dinilai</span>
                                <?php elseif($p->terlambat()): ?>
                                    <span class="badge bg-danger">Terlambat</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">Dikumpulkan</span>
                                <?php endif; ?>
                                <div class="small text-muted"><?php echo e($p->dikumpulkan_at?->format('d/m/Y H:i')); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($p): ?>
                                <?php if($p->jawaban): ?>
                                    <details>
                                        <summary class="small"><?php echo e(\App\Support\TeksKaya::ringkas($p->jawaban, 50)); ?></summary>
                                        <div class="small mt-1"><?php echo \App\Support\TeksKaya::html($p->jawaban); ?></div>
                                    </details>
                                <?php endif; ?>
                                <?php if($p->file): ?>
                                    <a href="<?php echo e(Storage::url($p->file)); ?>" target="_blank" class="small"><i class="bi bi-paperclip"></i>File jawaban</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($p): ?>
                                <form action="<?php echo e(route($routeName.'.nilai', [$tugas->id, $p->id])); ?>" method="POST" class="d-flex flex-wrap gap-1">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <input type="number" name="nilai" min="0" max="100" step="0.01" required
                                           value="<?php echo e($p->nilai !== null ? rtrim(rtrim($p->nilai, '0'), '.') : ''); ?>"
                                           class="form-control form-control-sm" style="width:80px;" placeholder="0-100">
                                    <input type="text" name="catatan_guru" value="<?php echo e($p->catatan_guru); ?>" maxlength="2000"
                                           class="form-control form-control-sm flex-grow-1" style="min-width:120px; width:auto;" placeholder="Catatan (opsional)">
                                    <button class="btn btn-sm btn-primary" title="Simpan nilai"><i class="bi bi-check-lg"></i></button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">Menunggu pengumpulan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada siswa terdaftar di kelas ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\web\jurnal\resources\views/ruangbelajar/tugas-show.blade.php ENDPATH**/ ?>