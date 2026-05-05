
<?php $__env->startSection('title', 'Tools Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        <div class="card card-custom gutter-b">
            <div class="card-header card-header-tabs-line">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-bold nav-tabs-line">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1_4"
                                onclick="getData('spareparts')">
                                <span class="nav-icon"><i class="flaticon2-gear"></i></span>
                                <span class="nav-text">Spareparts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2_4" onclick="getData('area')">
                                <span class="nav-icon"><i class="flaticon2-map"></i></span>
                                <span class="nav-text">Location Area</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" aria-haspopup="true" aria-expanded="false"
                                onclick="getData('tools')">
                                <span class="nav-icon"><i class="flaticon2-folder"></i></span>
                                <span class="nav-text">Tool</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-toolbar">
                    <div class="dropdown dropdown-inline">
                        <button type="button" class="btn btn-hover-light-primary btn-icon btn-sm" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="ki ki-more-hor"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-sm">
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Separated link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="kt_tab_pane_1_4" role="tabpanel"
                        aria-labelledby="kt_tab_pane_1_4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-bordered" id="table_spareparts">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Branch</th>
                                                    <th>Used To</th>
                                                    <th>Sparepart Name</th>
                                                    <th>Uom</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_2_4" role="tabpanel" aria-labelledby="kt_tab_pane_2_4">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered" id="table_tools">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Branch</th>
                                                <th>Code Name</th>
                                                <th>Tool Name</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kt_tab_pane_3_4" role="tabpanel" aria-labelledby="kt_tab_pane_3_4">
                        ...
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        function getData(type) {
            $.ajax({
                url: "<?php echo e(url('mr/tools-management/master/getMaster')); ?>/" + type,
                method: 'GET',
                success: function(response) {
                    let data = response.data;
                    if (type === 'spareparts') {
                        renderSpareparts(data);
                    } else if (type === 'tools') {
                        renderTools(data);
                    } else if (type === 'area') {
                        renderArea(data);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function renderSpareparts(data) {
            let tbody = $('#table_spareparts tbody');
            tbody.empty();
            data.forEach((item, index) => {
                tbody.append(`
            <tr>
                <td>${index + 1}</td>
                <td>${item.branch_name}</td>
                <td>${item.tool_name ?? '-'}</td>
                <td>${item.name ?? '-'}</td>
                <td>${item.uom ?? '-'}</td>
                <td>${item.active ?? '-'}</td>
            </tr>
        `);
            });
        }

        function renderTools(data) {
            let tbody = $('#table_tools tbody');
            tbody.empty();
            data.forEach((item, index) => {
                tbody.append(`
            <tr>
                <td>${index + 1}</td>
                <td>${item.branch_id}</td>
                <td>${item.code_name ?? '-'}</td>
                <td>${item.tool_name ?? '-'}</td>
                <td>${item.status ?? '-'}</td>
            </tr>
        `);
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.new.base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/new/ToolsManagement/Master/index.blade.php ENDPATH**/ ?>