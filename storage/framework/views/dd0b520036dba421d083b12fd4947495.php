<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>TransiTrack - <?php echo $__env->yieldContent('title'); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.1/mapbox-gl-directions.js"></script>
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.1/mapbox-gl-directions.css" type="text/css">
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="d-flex flex-row min-vh-100">
            <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="main-content flex-grow-1">
                <?php echo $__env->make('layouts.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
    
    <script>
        mapboxgl.accessToken = 'pk.eyJ1Ijoic2Vlam83IiwiYSI6ImNtY3ZqcWJ1czBic3QycHEycnM0d2xtaXEifQ.DdQ8QFpf5LlgTDtejDgJSA';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\kylecb\Desktop\Capstone\Testing\BUsOperator\resources\views/layouts/app.blade.php ENDPATH**/ ?>