@extends('layouts.new.base')
@section('title', 'MKT - Dashboard DO DC')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .card-custom.m-3 {
            transition: all 0.3s ease-in-out;
        }

        .card-custom.m-3:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            border-color: #FDB913;
        }


        .card-custom.m-3 {
            display: block !important;
            opacity: 1 !important;
            transition: all 0.3s ease-in-out;
        }

        .card-custom.m-3.hidden-card {
            display: none !important;
            opacity: 0 !important;
        }

        .card-custom.m-3 {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .card-custom.m-3.hidden-card {
            opacity: 0;
            transform: scale(0.95);
        }

        .dashed-2 {
            border: none;
            height: 1px;
            background: #000;
            background: repeating-linear-gradient(90deg, #000, #000 6px, transparent 6px, transparent 12px);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main-body">
            <div class="card card-custom" style="border-radius: 15px;" id="kt_card_3">
                <div class="card-header">
                    <div class="card-tittle">
                        <h3 class="card-label ml-5 mt-5 mb-5">Dashboard Delivery Order DC</h3>
                    </div>
                    <div class="card-toolbar d-flex align-items-center">
                        <input type="text" id="searchInput" class="form-control form-control-sm mr-2"
                            placeholder="🔍 Search order / customer / vehicle..."
                            style="width: 250px; border-radius: 10px;">
                        <a href="#" class="btn btn-icon btn-circle btn-sm btn-light-success mr-1"
                            data-card-tool="reload">
                            <i class="ki ki-reload icon-nm"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="cardContainer" style="display: none;">
                        <div class="row" id="dataCards"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>

    <script>
        $(document).ready(function() {
            const highlightClass = 'highlight-text';

            $('<style>')
                .prop('type', 'text/css')
                .html(`.${highlightClass} { background-color: yellow; font-weight: bold; border-radius: 4px; }`)
                .appendTo('head');

            $('#searchInput').on('keyup', function() {
                const keyword = $(this).val().toLowerCase().trim();
                const $cards = $('.card-custom.m-3');

                $cards.each(function() {
                    const $card = $(this);
                    const text = $card.text().toLowerCase();

                    $card.find(`.${highlightClass}`).each(function() {
                        $(this).replaceWith($(this).text());
                    });

                    if (keyword === '') {
                        $card.removeClass('hidden-card');
                    } else if (text.indexOf(keyword) > -1) {
                        $card.removeClass('hidden-card');

                        $card.find('*').contents().filter(function() {
                            return this.nodeType === 3 && this.nodeValue.toLowerCase()
                                .includes(keyword);
                        }).each(function() {
                            const regex = new RegExp(`(${keyword})`, 'gi');
                            const newHtml = this.nodeValue.replace(regex,
                                `<span class="${highlightClass}">$1</span>`);
                            $(this).replaceWith(newHtml);
                        });
                    } else {
                        $card.addClass('hidden-card');
                    }
                });

                $('.col-sm-3').each(function() {
                    const visibleCards = $(this).find('.card-custom.m-3:visible').length;
                    const headerCard = $(this).find(
                        '> .card-custom.bg-light-primary, > .card-custom.bg-light-warning, > .card-custom.bg-light-info, > .card-custom.bg-light-success'
                    );

                    if (visibleCards === 0) {
                        headerCard.fadeTo(200, 0.3);
                    } else {
                        headerCard.fadeTo(200, 1);
                    }
                });
            });
        });

        $(document).ready(function() {
            loadDashboardData();
            $('[data-card-tool="reload"]').on('click', function(e) {
                e.preventDefault();
                loadDashboardData();
            });

            function formatUang(subject) {
                rupiah = subject.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
                return `Rp ${rupiah}`;
            }

            function loadDashboardData() {
                $('#cardContainer').hide();

                $.ajax({
                    url: "{{ route('do-dashboard.getData') }}",
                    method: 'GET',
                    success: function(response) {
                        renderCards(response);
                        $('#cardContainer').fadeIn();
                    },
                    error: function() {
                        alert('Error loading data. Please try again.');
                    }
                });
            }

            function renderCards(data) {
                let html = '';
                html += renderSection('flaticon-stopwatch icon-2x', 'PENDING', data.pendingCard, 'light-primary',
                    function(item) {
                        return `
                <div class="card card-custom bg-light-primary m-3" style="border-radius: 20px;">
                    <div class="card-body text-dark">
                        <span class="text-bold"><b>Order No ${item.order_no.toUpperCase()}</b></span><br>
                        <span class="text-bold"><b>${item.customer_name.toUpperCase()}</b></span>
                        <hr class="dashed-2">
                        <p>${formatDate(item.order_date)}</p>
                        <p>Delivery Time ${formatDate(item.due_date)}</p>
                        <p>Note: ${item.description ?? '-'}</p>
                    </div>
                </div>
            `;
                    });

                html += renderSection('flaticon2-box-1 icon-2x', 'FINISH PREPARED', data.finishPreparedCard,
                    'light-warning',
                    function(item) {
                        return `
                <div class="card card-custom bg-light-warning m-3" style="border-radius: 20px;">
                    <div class="card-body text-dark">
                        <div class="row">
                            <div class="col-sm-12 mb-4"><b>Order No. ${item.order_no}</b></div>
                            <div class="col-sm-12 mb-4 text-bold"><b>${formatUang(item.price)}</b></div>
                            <div class="col-sm-12 mb-4 text-bold"><b>${item.customer_name.toUpperCase()}</b></div>
                            <div class="col-sm-12 mb-4 text-bold"><b>${item.drop_type}</b></div>
                            <div class="col-sm-12">
                                <hr class="dashed-2">
                                <p>${formatDate(item.created_at)}</p>
                                <p>Delivery Time ${formatDate(item.etd)}</p>
                                <p>${item.vehicle_no ?? '-'} - ${item.size_name ?? '-'}</p>
                                <p>Note: ${item.description ?? '-'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                    });

                html += renderSection('flaticon-truck icon-2x', 'SHIPPING', data.shippingCard, 'light-info',
                    function(item) {
                        return `
                        <div class="card card-custom bg-light-info m-3" style="border-radius: 20px;">
                            <div class="card-body text-dark">
                                <div class="row">
                                    <div class="col-sm-12 text-bold mb-1"><b>Order No. ${item.order_no}</b></div>
                                    <div class="col-sm-12 text-bold mb-1"><b>${formatUang(item.price)}</b></div>
                                    <div class="col-sm-12 text-bold"><b>${item.customer_name.toUpperCase()}</b></div>
                                    <div class="col-sm-12 text-bold"><b>${item.drop_type}</b></div>
                                    <div class="col-sm-12">
                                        <hr class="dashed-2">
                                        <p>${formatDate(item.created_at)}</p>
                                        <p>Delivery Time ${formatDate(item.etd)}</p>
                                        <p>${item.vehicle_no ?? '-'} - ${item.size_name ?? '-'}</p>
                                        <p>Note: ${item.description ?? '-'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    });

                html += renderSection('flaticon2-checkmark icon-2x', 'DONE', data.doneCard, 'light-success',
                    function(item) {
                        return `
                        <div class="card card-custom bg-light-success m-3" style="border-radius: 20px;">
                            <div class="card-body text-dark">
                                <div class="row">
                                    <div class="col-sm-12 text-bold mb-1"><b>Order No. ${item.order_no}</b></div>
                                    <div class="col-sm-12 text-bold mb-1"><b>${formatUang(item.price)}</b></div>
                                    <div class="col-sm-12 text-bold"><b>${item.customer_name.toUpperCase()}</b></div>
                                    <div class="col-sm-12 text-bold"><b>${item.drop_type}</b></div>
                                    <div class="col-sm-12">
                                        <hr class="dashed-2">
                                        <p>${formatDate(item.created_at)}</p>
                                        <p>Delivery Time ${formatDate(item.etd)}</p>
                                        <p>${item.vehicle_no ?? '-'} - ${item.size_name ?? '-'}</p>
                                        <p>Note: ${item.description ?? '-'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    });

                $('#dataCards').html(html);
            }

            function renderSection(icon, title, list, color, renderItem) {
                list = Array.isArray(list) ? list : (list ? Object.values(list) : []);
                if (!Array.isArray(list)) list = [];
                let section = `
        <div class="col-sm-3">
            <div class="card card-custom bg-${color} mb-4" style="border-radius: 20px;">
                <div class="card-header">
                    <div class="card-title">
                        <span class="card-icon"><i class="${icon} text-dark"></i></span>
                        <h1 class="card-label text-dark text-center">${title} (${list.length})</h1>
                    </div>
                </div>
            </div>
    `;

                if (list.length === 0) {
                    section += `
            <div class="card card-custom m-3" style="border-radius: 20px; min-height: 120px;">
                <div class="card-body text-muted">
                    <p class="mb-0">No data</p>
                </div>
            </div>
        `;
                } else {
                    list.forEach(item => {
                        try {
                            section += renderItem(item || {});
                        } catch (e) {
                            console.error('renderItem error for', item, e);
                        }
                    });
                }

                section += '</div>';
                return section;
            }

            function formatDate(dateStr) {
                if (!dateStr) return '-';
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: '2-digit',
                    year: 'numeric'
                });
            }
        });
    </script>
@endpush
