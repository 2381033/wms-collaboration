@extends('layouts.new.base')
@section('title', 'MKT - OUTSTANDING DO DC')

@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style>
        .card-custom {
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform .2s ease-in-out;
        }

        .card-custom:hover {
            transform: scale(1.02);
        }


        .card-body {
            background-color: #f8f9fa;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main-body">
            <div class="card card-custom" style="border-radius: 15px;" id="kt_card_3">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label ml-5 mt-5 mb-5">Outstanding Delivery Order DC</h3>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <input type="text" id="searchInput" class="form-control w-50"
                            placeholder="Cari berdasarkan No Mobil, Order No, Customer, atau Description...">
                        <button id="btnRefresh" class="btn btn-outline-primary ml-2">
                            <i class="fa fa-sync"></i> Refresh
                        </button>
                    </div>
                    <div id="outstanding-container" class="row"></div>
                    <div id="error-message" class="text-center text-danger mt-3" style="display: none;">
                        Failed to load data. Please try again.
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Input Harga -->
        <div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Input Harga</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="priceForm">
                            <div class="form-group">
                                <label>Total Price (Rp)</label>
                                <input type="number" id="inputPrice" class="form-control" min="0" required>
                            </div>
                            <input type="hidden" id="priceJobNos">
                            <button type="submit" class="btn btn-success btn-block">Simpan & Mark Done</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>

    <script>
        let allData = [];

        $(document).ready(function() {
            loadOutstandingData();

            $('#searchInput').on('keyup', function() {
                const keyword = $(this).val().toLowerCase();
                const filtered = allData.filter(item =>
                    (item.vehicle_no || '').toLowerCase().includes(keyword) ||
                    (item.order_no || '').toLowerCase().includes(keyword) ||
                    (item.customer_name || '').toLowerCase().includes(keyword) ||
                    (item.description || '').toLowerCase().includes(keyword)
                );
                renderOutstandingCards(filtered);
            });

            $('#btnRefresh').on('click', function() {
                $('#searchInput').val('');
                loadOutstandingData();
            });
        });

        function loadOutstandingData() {
            $('#outstanding-container').html(
                '<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading...</div>'
            );

            $.ajax({
                url: "{{ route('getListOutstanding') }}",
                type: "GET",
                success: function(response) {
                    allData = response || [];

                    if (allData.length === 0) {
                        $('#outstanding-container').html(
                            '<div class="alert alert-info text-center">No outstanding data found.</div>'
                        );
                        return;
                    }

                    renderOutstandingCards(allData);
                },
                error: function() {
                    $('#outstanding-container').html(
                        '<div class="alert alert-danger text-center">Failed to load data.</div>'
                    );
                }
            });
        }

        function renderOutstandingCards(data) {
            let html = '';

            data.forEach(item => {
                const jobNos = Array.isArray(item.job_nos) ? item.job_nos : [];
                const jobNosStr = JSON.stringify(jobNos);

                html += `
            <div class="col-md-4 mb-4">
                <div class="card card-custom border-primary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><strong>${item.vehicle_no || '-'}</strong></span>
                        <span class="badge badge-warning">${item.status || '-'}</span>
                    </div>
                    <div class="card-body">
                        <p><strong>Description:</strong> ${item.description || '-'}</p>
                        <p><strong>Customer:</strong> ${item.customer_name || '-'}</p>
                        <p><strong>Order No:</strong> ${item.order_no || '-'}</p>
                        <p><strong>Drop Type:</strong> ${item.drop_type || '-'}</p>
                        <p><strong>Total Price:</strong> Rp ${item.total_price || '0'}</p>
                        <p><strong>Size:</strong> ${item.size_name || '-'}</p>
                        <button class="btn btn-success btn-sm mark-done-btn mt-2" 
                                data-jobnos='${jobNosStr}'
                                data-price='${item.total_price || 0}'>
                            <i class="fa fa-check"></i> Mark as Done
                        </button>
                    </div>
                </div>
            </div>
        `;
            });

            $('#outstanding-container').html(html);
        }

        $(document).on('click', '.mark-done-btn', function() {
            const jobNos = JSON.parse($(this).attr('data-jobnos'));
            const btn = $(this);
            let priceRaw = $(this).attr('data-price') || '0';

            let price = parseFloat(priceRaw.replace(/[.,]/g, ''));

            if (!price || price === 0) {
                $('#priceJobNos').val(JSON.stringify(jobNos));
                $('#inputPrice').val('');
                $('#priceModal').modal('show');
                return;
            }
            markAsDone(jobNos, null, btn);
        });

        $('#priceForm').on('submit', function(e) {
            e.preventDefault();
            const jobNos = JSON.parse($('#priceJobNos').val());
            const price = parseFloat($('#inputPrice').val()) || 0;

            $('#priceModal').modal('hide');
            markAsDone(jobNos, price, null);
        });

        function markAsDone(jobNos, price = null, btn = null) {
            if (btn) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
            }

            $.ajax({
                url: "{{ route('outstanding.markDone') }}",
                type: "POST",
                data: {
                    job_nos: jobNos,
                    price: price,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.success ? 'Done!' : 'Oops!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadOutstandingData();
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update data.', 'error');
                },
                complete: function() {
                    if (btn) btn.prop('disabled', false).html('<i class="fa fa-check"></i> Mark as Done');
                }
            });
        }
    </script>
@endpush
