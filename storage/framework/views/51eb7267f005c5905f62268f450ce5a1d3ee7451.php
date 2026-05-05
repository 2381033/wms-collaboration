

<?php $__env->startSection('title'); ?>
    Start Putaway
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Inbound</h2>
                <ol>
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li>Inbound</li>
                    <li>Start Putaway</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>SKU</th>
                                    <th>BATCH NO</th>
                                    <th>EXP DATE</th>
                                    <th>QTY</th>
                                    <th>PALLET TAG</th>
                                    <th>LOCATION</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="listPutaway">
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tableLocation">
                            <thead>
                                <tr>
                                    <th>Location Available</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-submit-pallet-tag" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <form action="<?php echo e(route('inbound-scan_pallet_tag')); ?>" method="post" id="form-scan-pallet">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <input type="hidden" id="qrcodeValue" name="qrcode">
                    <input type="hidden" id="skuValue" name="product_code">
                    <input type="hidden" class="inbound_id" name="inbound_id">
                    <input type="hidden" class="id_per_pallet" name="id_per_pallet">
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-scan-pallet" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" style="align-content: center;">
                            <input type="text" class="form-control" id="inputPalletTag" autocomplete="off" autofocus="on"
                                placeholder="Scan here..">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal-edit-location">
        <div class="modal-dialog modal-lg" role="document">
            <form action="<?php echo e(route('inbound-edit_location_putaway')); ?>" method="post" id="form-scan-pallet">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <input type="hidden" class="id_per_pallet_edit" name="id_per_pallet">
                    <div class="modal-header">
                        <div class="modal-title">Form Edit Location</div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <select id="locationCodeEdit" class="form-control" name="" style="width: 100%;"
                                        onchange="damageAreaEdit(this.value)">
                                        <?php $__currentLoopData = $location; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option
                                                value="<?php echo e($item->status_code . ':' . $item->location_code . ':' . $item->id); ?>">
                                                <?php echo e($item->location_code); ?> - <?php echo e($item->site_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <input type="hidden" name="location_code" id="locationCodeValueEdit">
                                <input type="hidden" name="location_id" id="locationIDValueEdit">
                                <div class="appendDamageEdit">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm"> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal-submit-scan-lokasi">
        <div class="modal-dialog modal-lg" role="document">
            <form action="<?php echo e(route('inbound-scan_lokasi')); ?>" method="post" id="form-scan-lokasi">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title">MAPPING LOKASI SKU <b class="skuText"></b></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input type="hidden" id="id_list" name="id">
                    <input type="hidden" id="productCodeValue" name="product_code">
                    <div class="modal-body">
                        <div class="row">
                            <div class="resultAlert" style="margin-left: auto; margin-right: auto; display: block;">

                            </div>
                            <hr>
                            <div class="kontenLocation mt-4">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal-scan-lokasi">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">MAPPING LOKASI SKU <b class="skuText"></b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="my-input">QTY</label>
                                <input id="qtyValueScan" class="form-control" type="text" readonly name="qty">
                            </div>
                            <hr>
                            <input type="text" class="form-control" id="inputLocationCode" autofocus="on"
                                autocomplete="off" placeholder="Scan Location Here..">
                        </div>
                        <div class="kontenLocation mt-4">

                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>

    
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        var success = new Audio("<?php echo e(url('assets/audio/success.mp3')); ?>");
        var error = new Audio("<?php echo e(url('assets/audio/error.mp3')); ?>");

        function editLocation(id_per_pallet) {
            $('#modal-edit-location').modal('show');
            $('.id_per_pallet_edit').val(id_per_pallet);
        }

        $('#modal-scan-pallet').on('shown.bs.modal', function() {
            $('#inputPalletTag').focus();
        });

        $('#modal-scan-lokasi').on('shown.bs.modal', function() {
            $('#inputLocationCode').focus();
        });
        generateTablePutaway();

        function generateTablePutaway() {
            $.ajax({
                url: "<?php echo e(url('warehouse/inbound/startPutaway/getListPutaway')); ?>/" + '<?php echo e($picking_id); ?>',
                type: "GET",
                dataType: 'json',
                success: function(response) {
                    $("#listPutaway").html("");
                    $.each(response.data, function(key, value) {
                        var batch = ""
                        var exp = ""
                        var btnScanPallet = ""
                        var btnScanLocation = ""
                        var btnEditLocation = ""
                        if (value.detail.lot_no == null) {
                            var batch = '-'
                        } else {
                            var batch = value.detail.lot_no
                        }
                        if (value.detail.exp_date == null) {
                            var exp = '-'
                        } else {
                            var exp = value.detail.exp_date
                        }
                        if (value.scan_pallet_tag == 'No') {
                            var btnScanPallet =
                                `<a class="btn btn-sm btn-danger text-white" onclick="scanPalletTag('${value.qrcode}', '${value.id} ', '${value.product_code}')"><i class="fas fa-qrcode"></i> SCAN</a>`;
                        } else {
                            var btnScanPallet =
                                `<a class="btn btn-sm btn-success"><i class="fa fa-check text-white"> Done</i></a>`;
                        }
                        if (value.location_code == null && value.scan_pallet_tag == 'Yes') {
                            var btnScanLocation =
                                `<a class="btn btn-sm btn-info text-white m-2" onclick="scanLocation('${value.id} ', '${value.product_code}', '${value.qty_per_pallet}')"><i class="fas fa-camera"></i> SCAN</a>`;
                        } else {
                            var btnScanLocation = value.location_code
                        }
                        if (value.location_code != null && value.qrcode != null) {
                            var btnEditLocation =
                                `<a class="btn btn-sm btn-dark text-white" onclick="editLocation('${value.id}')"><i class="fas fa-edit"></i> Edit Location</a>`;
                        } else {
                            var btnEditLocation = "-"
                        }

                        $('#listPutaway').append(`
                        <tr>
                            <td>${value.product_code}</td> 
                            <td>${batch}</td> 
                            <td>${exp}</td> 
                            <td>${value.qty_per_pallet}</td> 
                            <td>${btnScanPallet}</td> 
                            <td>${btnScanLocation}</td> 
                            <td>${btnEditLocation}</td> 
                        </tr>`)
                    });
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        $('#tableLocation').DataTable({
            "dom": '<"toolbar">frtip',
            processing: true,
            ordering: false,
            scroller: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(url('warehouse/inbound/startPutaway/getLocationAvail')); ?>/" + '<?php echo e($inbound_id); ?>',
                type: "GET",
            },
            columns: [{
                    data: 'location',
                    name: 'location'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return 'Available';
                    },
                }
            ],
        });

        function damageAreaEdit(value) {
            var str = value;
            var value = str.split(":");
            var status_location = value[0];
            var location_code = value[1];
            $('#locationIDValueEdit').val(value[2]);
            if (status_location == 'B') {
                $('#locationCodeValueEdit').val(location_code)
                $('.appendDamageEdit').append(`<div class="form-group">
                <textarea id="my-textarea" class="form-control" name="remarks_damage" required rows="3"
                placeholder="Remarks Damage.."></textarea>
            </div>
            `);
            } else {
                $('#locationCodeValueEdit').val(location_code)
                $('.appendDamageEdit').html('');
            }
        }

        $('#locationCodeEdit').select2({
            'placeholder': 'Select a location'
        });

        $('#locationCode').select2({
            'placeholder': 'Select a location'
        });

        function damageArea(value) {
            var str = value;
            var value = str.split(":");
            var status_location = value[0];
            var location_code = value[1];
            //parsing location_id to controller
            $('#locationIDValue').val(value[2]);

            if (status_location == 'B') {
                $('#locationCodeValue').val(location_code)
                $('.appendDamage').append(`<div class="form-group">
                <textarea id="my-textarea" class="form-control" name="remarks_damage" required rows="3"
                placeholder="Remarks Damage.."></textarea>
            </div>
            `);
            } else {
                $('#locationCodeValue').val(location_code)
                $('.appendDamage').html('');
            }
        }

        function scanPalletTag(qrcode, id, product_code) {
            $('#modal-scan-pallet').modal('show');
            var parsingPalletTag = qrcode + '||' + id + '||' + product_code;
            sessionStorage.setItem('parsingPalletTag', parsingPalletTag);
        }

        $('#inputPalletTag').on('keydown', function(event) {
            var value = $('#inputPalletTag').val();
            var session = sessionStorage.getItem('parsingPalletTag');
            const myArray = session.split("||");
            var qrMaster = myArray[0];
            if (event.keyCode === 13) {
                event.preventDefault();
                if (value != qrMaster) {
                    error.play();
                    swal({
                        icon: "warning",
                        text: "Pallet Tag Tidak Sesuai!"
                    });
                    $('#inputPalletTag').val("");
                } else {
                    doScanPallet();
                }
            }
        });

        function doScanPallet() {
            var params = sessionStorage.getItem('parsingPalletTag');
            const myArray = params.split("||");
            var qrcode = myArray[0];
            var id_per_pallet = myArray[1];
            var product_code = myArray[2];
            $.ajax({
                url: "<?php echo e(url('warehouse/inbound/scanPalletTag')); ?>/" + qrcode + '/' + id_per_pallet + '/' +
                    product_code,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response.status);
                    $('#qrcodeValue').val(response.data.qrcode)
                    $('#skuValue').val(response.data.product_code)
                    $('.inbound_id').val(response.data.inbound_id)
                    $('.id_per_pallet').val(response.id_per_pallet)
                    if (response.status == 'ok') {
                        $('#form-scan-pallet').submit();
                    } else {
                        alert('Error, please refresh the page');
                    }
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..')
                }
            });
        }

        $('#form-scan-pallet').on('submit', function(e) {
            e.preventDefault();
            $('.btn-scan-pallet').hide();
            $.ajax({
                url: "<?php echo e(route('inbound-scan_pallet_tag')); ?>",
                data: $('#form-scan-pallet').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    if (response == 'success') {
                        success.play();
                        swal({
                            icon: "success",
                            text: "Good Job!"
                        });
                        generateTablePutaway()
                        $('#modal-scan-pallet').modal('hide');
                        $('#inputPalletTag').val("");
                    } else {
                        alert('Internal Server Error, Please refresh the page and try again..');
                        error.play();
                    }
                },
                error: function(error) {
                    alert('Internal Server Error, Please refresh the page and try again..');
                    error.play();
                }
            });
        });

        function scanLocation(id, product_code, qty) {
            // var params = id + '||' + product_code + '||' + qty;
            // sessionStorage.setItem('parsingLocationCode', params);
            $('#modal-scan-lokasi').modal('show');
            $('.skuText').text(product_code);
            $('#qtyValueScan').val(qty);
            $('#id_list').val(id);
            $('#productCodeValue').val(product_code);
        }

        $('#inputLocationCode').on('keydown', function(event) {
            var value = $('#inputLocationCode').val();
            // var sessionLoc = sessionStorage.getItem('parsingLocationCode');
            if (event.keyCode === 13) {
                event.preventDefault();
                doScanLocation(value);
            }
        });

        function doScanLocation(paramsLocation) {
            $.ajax({
                url: "<?php echo e(url('warehouse/inbound/scanLokasi')); ?>/" + paramsLocation,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data == null) {
                        swal({
                            icon: "warning",
                            title: "Location not found!",
                        });
                        $('#inputLocationCode').val("");
                        error.play();
                    } else {
                        $('.cameraScanLocation').hide();
                        $('.btn-scan-lokasi').removeClass('hide')
                        $('.kontenLocation').html('')
                        $('.kontenLocation').append(`
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="hidden" name="location_code" class="form-control mt-3" value="${response.data.location_code}">
                                <input type="hidden" class="form-control mt-3" value="${response.data.id}" name="location_id">
                            </div>
                        </div>
                        `)
                        $('#form-scan-lokasi').submit();
                    }
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..')
                    error.play();
                }
            });
        }

        function manualLocation(id, product_code, qty) {
            $('#modal-manual-lokasi').modal('show');
            $('.skuText').text(product_code);
            $('#qtyValueScan').val(qty);
            $('#id_list').val(id);
            $('#productCodeValue').val(product_code);
        }

        $('#form-scan-lokasi').on('submit', function(e) {
            e.preventDefault();
            $('.btn-scan-lokasi').hide();
            $.ajax({
                url: "<?php echo e(route('inbound-scan_lokasi')); ?>",
                data: $('#form-scan-lokasi').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    if (response == 'ok') {
                        success.play();
                        swal({
                            icon: "success",
                            title: "Good Job!",
                        });
                        generateTablePutaway()
                        $('#modal-scan-lokasi').modal('hide');
                        $('#inputLocationCode').val("");
                    } else {
                        swal({
                            icon: "error",
                            title: "Double Location",
                            text: "Please choose another location"
                        });
                        $('#modal-scan-lokasi').modal('hide');
                        error.play();
                    }
                },
                error: function(error) {
                    alert('Internal Server Error, Please refresh page and try again..')
                    error.play();
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/transaction/inbound/start_putaway.blade.php ENDPATH**/ ?>