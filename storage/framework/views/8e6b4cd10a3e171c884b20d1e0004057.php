<div class="sidebar">
    <div class="logo">
        <img src="<?php echo e(asset('images/transitrack_logo.png')); ?>" alt="TransiTrack Logo" class="logo-img" style="width: 40px; height: 40px; margin-right: 10px; object-fit: contain;">
        <h1>TransiTrack</h1>
    </div>
    <div class="nav-links">
        <a href="<?php echo e(route('operator.panel')); ?>" class="nav-item <?php echo e(request()->routeIs('operator.panel') ? 'active' : ''); ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Operator Panel</span>
        </a>
        <a href="<?php echo e(route('notifications.panel')); ?>" class="nav-item <?php echo e(request()->routeIs('notifications.panel') ? 'active' : ''); ?>">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
        </a>
        <a href="<?php echo e(route('schedule.panel')); ?>" class="nav-item <?php echo e(request()->routeIs('schedule.panel') ? 'active' : ''); ?>">
            <i class="fas fa-calendar-alt"></i>
            <span>Schedule</span>
        </a>
        <a href="<?php echo e(route('drivers.panel')); ?>" class="nav-item <?php echo e(request()->routeIs('drivers.panel') ? 'active' : ''); ?>">
            <i class="fas fa-users"></i>
            <span>Drivers</span>
        </a>
        <a href="<?php echo e(route('routes.panel')); ?>" class="nav-item <?php echo e(request()->routeIs('routes.*') ? 'active' : ''); ?>">
            <i class="fas fa-route"></i>
            <span>Routes</span>
        </a>
        <a href="<?php echo e(route('buses.panel')); ?>" class="nav-item <?php echo e(request()->routeIs('buses.*') ? 'active' : ''); ?>">
            <i class="fas fa-bus-alt"></i>
            <span>Buses</span>
        </a>
        <a href="<?php echo e(route('terminal.panel')); ?>" class="nav-item <?php echo e(request()->routeIs('terminal.*') ? 'active' : ''); ?>">
            <i class="fas fa-building"></i>
            <span>Terminal</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        <a href="#" class="nav-item d-flex align-items-center gap-2 mt-4" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
    </div>
</div>

<script>
    if (window.history && window.history.pushState) {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function () {
            window.location.replace("<?php echo e(route('login')); ?>");
        };
    }
</script><?php /**PATH C:\Users\User\Desktop\TransiTrack System\BusOperator\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>