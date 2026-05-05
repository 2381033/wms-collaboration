@extends('layouts.main')

@section('title')
    Stock Take Scan
@endsection


@push('styles')
    <style>
        .scan-section {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #222;
            background-color: #f9f9f9;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 50px;
        }

        .title {
            text-align: center;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .scan-input {
            width: 100%;
            padding: 12px 16px;
            font-size: 1.2rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
            outline-offset: 2px;
        }

        .scan-input:focus {
            border-color: #4A90E2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.6);
        }

        .scan-result-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .scan-result-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .scan-card {
            background: linear-gradient(135deg, #fdfdfd, #f7f9fc);
            border: 1px solid #e2e8f0;
            border-left: 5px solid #4A90E2;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .scan-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
        }

        .scan-card-header {
            font-size: 1.3rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.8rem;
            letter-spacing: 0.5px;
        }

        .scan-card-body {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .card-row {
            font-size: 0.95rem;
            color: #444;
        }

        .card-row span {
            font-weight: 600;
            color: #1a202c;
            display: inline-block;
            min-width: 90px;
        }

        .btn-variance {
            margin-top: 12px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #FF6B6B, #FF3D3D);
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
            transition: background 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
            user-select: none;
        }

        .btn-variance:hover {
            background: linear-gradient(135deg, #FF4C4C, #FF1A1A);
            box-shadow: 0 6px 16px rgba(255, 77, 77, 0.6);
        }

        .btn-variance:active {
            transform: scale(0.98);
        }


        .btn-variance.active,
        .btn-variance:disabled {
            background: #28a745 !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4) !important;
            cursor: default !important;
        }
    </style>
@endpush

@section('content')
    <section id="scan" class="scan-section" style="padding: 2rem;">
        <div class="container" style="max-width: 600px; margin: 0 auto;">

            <h1 class="title">STO SCAN</h1>

            <input type="text" id="scanInput" placeholder="Scan here..." autofocus autocomplete="off" class="scan-input" />

            <div id="resultContainer" class="scan-result-container" style="margin-top: 2rem;"></div>
        </div>
    </section>
@endsection

@section('modal')
@endsection

@push('scripts')
    <script>
        var success = new Audio("{{ url('assets/audio/success.mp3') }}");
        var error = new Audio("{{ url('assets/audio/error.mp3') }}");
        $(document).ready(function() {
            $('#scanInput').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let value = $(this).val().trim();
                    if (value === '') return;
                    doScan(value);
                    $(this).val(''); // Clear input
                }
            });
        });

        function doScan(value) {
            $('#resultContainer').html("");
            $.ajax({
                url: "{{ url('inventory/stock-take/doScan') }}",
                type: "POST", // ubah ke POST kalau sesuai
                data: {
                    data: value
                }, // kirim value input ke backend
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Laravel
                },
                success: function(data) {
                    success.play();
                    let item = data.data;
                    if (item.variance == 'Yes') {
                        var button =
                            `<button class="btn-dark btn-block mt-3" type="button">📢 VARIANCE‼️</button>`;
                    } else {
                        var button = ` <button class="btn-variance" type="button">Mark Variance</button>`;
                    }
                    let cardHTML = `
                        <div class="scan-card" data-id="${item.id}">
                            <div class="scan-card-header"><strong>${item.product_code}</strong></div>
                            <div class="scan-card-body">
                                <div class="card-row"><span>📍 Location:</span> ${item.location_code}</div>
                                <div class="card-row"><span>📦 SOH:</span> ${item.qty}</div>
                                <div class="card-row"><span>📦 SOA:</span> ${item.soa ?? '-'} </div>
                                <div class="card-row"><span>📋 SOB:</span> ${item.sob ?? '-'} </div>
                            </div>
                            ${button}
                        </div>`;
                    $('#resultContainer').append(cardHTML);
                },
                error: function(xhr) {
                    error.play();
                    let msg = 'Error processing data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    swal({
                        icon: "error",
                        text: msg,
                        timer: 1000,
                        buttons: false
                    });
                    $('#scanInput').val('').focus();
                }
            });
        }

        $(document).on('click', '.btn-variance', function() {
            let card = $(this).closest('.scan-card');
            let id = card.data('id');
            let button = $(this);
            $.ajax({
                url: "{{ url('inventory/stock-take/variance') }}",
                type: "POST",
                data: {
                    id: id
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    button.prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    swal({
                        icon: 'success',
                        text: 'success',
                        timer: 1000,
                        buttons: false
                    });
                    $('#scanInput').val('').focus();
                    $('#resultContainer').html("");
                },
                error: function(xhr) {
                    let msg = 'Failed to mark variance.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    swal({
                        icon: 'error',
                        text: msg
                    });
                    button.prop('disabled', false).text('Variance');
                }
            });
        });
    </script>
@endpush
