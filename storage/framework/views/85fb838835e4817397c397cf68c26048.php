<div class="topbar bg-white shadow-sm py-2 px-4 d-flex justify-content-end align-items-center">
    <div class="user-info d-flex align-items-center gap-3">
        <div class="notification position-relative">
            <i class="fas fa-bell"></i>
            <div class="badge bg-danger position-absolute top-0 start-100 translate-middle">3</div>
        </div>
        <?php if(Auth::check()): ?>
        <div class="user-details text-end">
            <h4 class="mb-0"><?php echo e(Auth::user()->name); ?></h4>
            <p class="mb-0"><?php echo e(ucfirst(Auth::user()->role)); ?></p>
            <p class="mb-0 small text-muted"><?php echo e(Auth::user()->company_name ?? ''); ?></p>
        </div>
        <img src="<?php echo e(Auth::user()->photo_url ? asset('storage/' . Auth::user()->photo_url) : 'https://randomuser.me/api/portraits/men/32.jpg'); ?>" alt="User" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
        <?php else: ?>
        <div class="user-details text-end">
            <h4 class="mb-0 text-muted">Guest</h4>
            <p class="mb-0 text-muted">Not logged in</p>
        </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH C:\Users\User\Desktop\TransiTrack System\BusOperator\resources\views/layouts/topbar.blade.php ENDPATH**/ ?>