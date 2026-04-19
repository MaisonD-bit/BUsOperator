<!-- topbar.blade.php -->
<div class="topbar bg-white shadow-sm py-2 px-4 d-flex justify-content-end align-items-center">
    <div class="user-info d-flex align-items-center gap-3">
        <!-- Keep the existing notification icon with a dynamic badge -->
        <div class="notification position-relative">
            <i class="fas fa-bell"></i>
            <!-- The badge will be updated by JavaScript -->
            <div id="notificationBadge" class="badge bg-danger position-absolute top-0 start-100 translate-middle">3</div>
        </div>
        
        <?php if(Auth::check()): ?>
            <div class="user-details text-end">
                <h4 class="mb-0"><?php echo e(Auth::user()->name); ?></h4>
                <p class="mb-0"><?php echo e(ucfirst(Auth::user()->role)); ?></p>
                <p class="mb-0 small text-muted"><?php echo e(Auth::user()->company_name ?? ''); ?></p>
            </div>
        <?php endif; ?>

        <img src="<?php echo e(Auth::user()->photo_url ? asset('storage/' . Auth::user()->photo_url) : 'https://randomuser.me/api/portraits/men/32.jpg'); ?>" alt="User" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">

    </div>
</div>

<!-- Add this script at the bottom of the file or in a separate JS file -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to update the notification badge
    function updateNotificationBadge() {
        fetch('/notifications/unread-count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching notification count:', error);
            // Optionally hide the badge on error
            document.getElementById('notificationBadge').style.display = 'none';
        });
    }

    // Call it on page load and then every 30 seconds
    updateNotificationBadge();
    setInterval(updateNotificationBadge, 30000); // Update every 30 seconds
});
</script><?php /**PATH C:\Users\kylecb\Desktop\Capstone\Testing\BUsOperator\resources\views/layouts/topbar.blade.php ENDPATH**/ ?>