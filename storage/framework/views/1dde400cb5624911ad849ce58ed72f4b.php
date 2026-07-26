<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('content'); ?>
<?php
    $hasFile = collect($fields)->contains(fn($f) => $f['type'] === 'file');
?>
<div class="card shadow-sm" style="max-width:760px;">
    <div class="card-header bg-white">
        <?php echo e($item ? 'Ubah' : 'Tambah'); ?> <?php echo e($title); ?>

    </div>
    <div class="card-body">
        <form method="POST"
              action="<?php echo e($item ? route($routeName.'.update', $item->id) : route($routeName.'.store')); ?>"
              <?php if($hasFile): ?> enctype="multipart/form-data" <?php endif; ?>>
            <?php echo csrf_field(); ?>
            <?php if($item): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $name = $field['name']; $old = old($name, $item->{$name} ?? null); ?>
                <div class="mb-3">
                    <label class="form-label"><?php echo e($field['label']); ?></label>

                    <?php if($field['type'] === 'textarea'): ?>
                        <textarea name="<?php echo e($name); ?>" rows="3" class="form-control <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"><?php echo e($old); ?></textarea>

                    <?php elseif($field['type'] === 'select'): ?>
                        <select name="<?php echo e($name); ?>" class="form-select <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Pilih <?php echo e($field['label']); ?> --</option>
                            <?php $__currentLoopData = ($field['options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if((string) $old === (string) $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                    <?php elseif($field['type'] === 'checkbox'): ?>
                        <div class="form-check">
                            <input type="hidden" name="<?php echo e($name); ?>" value="0">
                            <input type="checkbox" name="<?php echo e($name); ?>" value="1" class="form-check-input" <?php if($old): echo 'checked'; endif; ?>>
                        </div>

                    <?php elseif($field['type'] === 'file'): ?>
                        <input type="file" name="<?php echo e($name); ?>" class="form-control <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if($item && $item->{$name}): ?>
                            <div class="form-text">File saat ini: <a href="<?php echo e(Storage::url($item->{$name})); ?>" target="_blank">lihat</a></div>
                        <?php endif; ?>

                    <?php elseif($field['type'] === 'password'): ?>
                        <input type="password" name="<?php echo e($name); ?>" class="form-control <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e($item ? 'Kosongkan jika tidak diubah' : ''); ?>">

                    <?php else: ?>
                        <input type="<?php echo e($field['type']); ?>" name="<?php echo e($name); ?>" value="<?php echo e($old); ?>" class="form-control <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e($field['placeholder'] ?? ''); ?>">
                    <?php endif; ?>

                    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="<?php echo e(route($routeName.'.index')); ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\web\jurnal\resources\views\crud\form.blade.php ENDPATH**/ ?>