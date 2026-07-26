
<table>
    <tr><td colspan="5"><strong>ANALISIS KEBUTUHAN GURU</strong></td></tr>
    <tr><td colspan="5"><strong><?php echo e(optional($identitas)->nama_sekolah ?? '-'); ?></strong></td></tr>
    <tr><td colspan="5">NPSN: <?php echo e(optional($identitas)->npsn ?? '-'); ?> | Tahun Ajaran: <?php echo e(optional($tahunAktif)->nama_tahun_ajaran ?? '-'); ?> | Beban Standar: <?php echo e($beban); ?> JP/Minggu</td></tr>
    <tr><td colspan="5"></td></tr>
</table>
<?php echo $__env->make('kurikulum.partials.analisis-guru-tabel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\laragon\www\web\jurnal\resources\views\kurikulum\analisis-guru-excel.blade.php ENDPATH**/ ?>