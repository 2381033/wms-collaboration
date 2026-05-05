<?php
    $permissions = DB::table('auth_group_permission')
        ->select('auth_permission_id')
        ->where('auth_group_id', Auth::user()->auth_group_id)
        ->get()
        ->pluck('auth_permission_id')
        ->toArray();

    $permissions = DB::table('auth_permission')->whereIn('id', $permissions)->get()->pluck('name')->toArray();
?>

<div id="kt_header" class="header header-fixed hide-print">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <div class="header-logo">
                <a href="<?php echo e(url('/')); ?>">
                    <img alt="Logo" src="<?php echo e(url('/')); ?>/images/logos.png" style="width: 150px;" />
                </a>
            </div>
            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                <?php if(Auth::check()): ?>
                    <ul class="menu-nav">
                        <?php if(in_array('cyclecount_header', $permissions)): ?>
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">CYCLE COUNT <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        <?php if(in_array('cyclecount_setup', $permissions)): ?>
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="<?php echo e(url('inventory/cycleCount/setup')); ?>" class="menu-link">
                                                    <span class="menu-text">Setup</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(in_array('cyclecount_proses', $permissions)): ?>
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="<?php echo e(url('inventory/cycleCount/')); ?>" class="menu-link">
                                                    <span class="menu-text">Counting Process</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(in_array('cyclecount_monitoring', $permissions)): ?>
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="<?php echo e(url('inventory/cycleCount/monitoring')); ?>"
                                                    class="menu-link">
                                                    <span class="menu-text">Monitoring</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(in_array('cyclecount_monitoring', $permissions)): ?>
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="<?php echo e(url('inventory/cycleCount/monitoring')); ?>"
                                                    class="menu-link">
                                                    <span class="menu-text">Schedule Email</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vm-cost-checking', 'vm-cost-checking')): ?>
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">VM MASTER<i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="<?php echo e(url('vm-price/priceMaster')); ?>" class="menu-link">
                                                <span class="menu-text">Master Data</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="<?php echo e(url('vm-price/checking-cost')); ?>" class="menu-link">
                                                <span class="menu-text">Checking Cost</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="<?php echo e(url('vm-price/priceActivity')); ?>" class="menu-link">
                                                <span class="menu-text">User Activity</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('spot-order', $permissions)): ?>
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="<?php echo e(url('crossDock')); ?>" class="menu-link">
                                    <span class="menu-text">SPOT ORDER </span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <ul class="menu-nav">
                        <li class="menu-item menu-item-submenu menu-item-rel menu-item-active" data-menu-toggle="click"
                            aria-haspopup="true">
                            <a href="<?php echo e(url('login')); ?>" class="menu-link menu-toggle">
                                <span class="menu-text">Login</span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <div class="topbar">
            <div class="topbar-item">
                <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
                    <a href="<?php echo e(url('/')); ?>">
                        <span class="text-dark font-weight-bolder font-size-base d-none d-md-inline mr-3">
                            HOME
                        </span>
                        <span class="symbol symbol-35 symbol-light-danger">
                            <span class="symbol-label font-size-h5 font-weight-bold">
                                <?php if(Auth::check()): ?>
                                    <i class="fas fa-home text-dark"></i>
                                <?php else: ?>
                                    <i class="far fa-smile text-dark-50"></i>
                                <?php endif; ?>
                            </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/layouts/new/header.blade.php ENDPATH**/ ?>