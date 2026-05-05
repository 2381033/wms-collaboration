@extends('layouts.new.base')
@section('title', 'Gate In Vehicle')

@push('styles')
    <style>
        .header,
        .aside,
        .footer {
            display: none !important;
        }

        /* BACKGROUND */
        body {
            background: radial-gradient(circle at top, #1a1f35, #0b0f1a);
            color: #e5e7eb;
        }

        /* WRAPPER */
        .wrapper-box {
            max-width: 420px;
            margin: 60px auto;
        }

        /* ICON */
        .truck-icon {
            font-size: 32px;
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            width: 65px;
            height: 65px;
            line-height: 65px;
            border-radius: 50%;
            margin: auto;
            color: #fff;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
        }

        /* CARD */
        .modern-card {
            border: none;
            border-radius: 18px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(14px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        }

        /* FORM GROUP */
        .modern-group {
            margin-bottom: 18px;
        }

        /* INPUT */
        .modern-input {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.07);
            color: #fff;
        }

        .modern-input:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
        }

        /* FIX SELECT (IMPORTANT) */
        select.modern-input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        /* LABEL */
        .modern-label {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 5px;
            display: block;
        }

        /* BUTTON */
        .modern-btn {
            padding: 14px;
            border-radius: 12px;
            font-weight: bold;
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border: none;
        }

        .modern-btn:hover {
            opacity: 0.9;
        }

        /* ALERT */
        .soft-alert {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
            border: none;
        }

        /* LIST */
        .modern-list {
            margin-top: 15px;
        }

        .modern-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* TEXT */
        .text-muted {
            color: #9ca3af !important;
        }

        /* FIX SELECT TEXT */
        .modern-input,
        .modern-input option {
            color: #fff;
            background: #1f2937;
        }

        /* khusus dropdown list */
        select.modern-input option {
            background: #1f2937;
            color: #fff;
        }

        /* hover (beberapa browser support) */
        select.modern-input option:checked {
            background: #ef4444;
            color: #fff;
        }

        input[type="checkbox"] {
            transform: scale(2);
            margin-right: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-4">
        <div class="text-center mb-4">
            <div class="truck-icon mb-2">🚚</div>
            <h3 class="mb-1">Gate In Vehicle</h3>
            <small class="text-muted">DC Warehouse</small>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="modern-card h-100">
                    <div id="successAlert" class="alert text-center soft-alert d-none">
                        ✔ Data has been saved successfully!
                    </div>
                    <form id="formGateIn" method="POST">
                        @csrf
                        <div class="modern-group">
                            <label class="modern-label">Gate In Date & Time</label>
                            <input type="datetime-local" name="gate_in_at" id="gate_in_at" class="modern-input" required
                                autocomplete="off">
                        </div>

                        <div class="modern-group">
                            <label class="modern-label">Vehicle Number</label>
                            <input type="text" name="no_mobil" class="modern-input" autofocus required autocomplete="off"
                                placeholder="e.g. B 1234 ABC">
                        </div>
                        <div class="modern-group">
                            <label class="modern-label">Driver Name</label>
                            <input type="text" name="nama_supir" class="modern-input" required autocomplete="off"
                                placeholder="e.g. John Doe">
                        </div>
                        <div class="modern-group">
                            <label class="modern-label">Transporter Name</label>
                            <input type="text" name="transporter_name" class="modern-input" required autocomplete="off"
                                placeholder="e.g. PT. ABC Transport">
                        </div>
                        <div class="modern-group">
                            <label class="modern-label">Vehicle Type</label>
                            <select name="jenis_mobil" class="modern-input" required autocomplete="off">
                                <option value="" selected disabled>Choose</option>
                                @foreach ($vehicles as $item)
                                    <option value="{{ $item->vehicle }}">{{ $item->vehicle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modern-group">
                            <label class="modern-label">Customer name</label>
                            <select name="principal_name" class="modern-input" required autocomplete="off">
                                <option value="" selected disabled>Choose</option>
                                @foreach (Auth::user()->principal as $item)
                                    <option value="{{ $item->principal_name }}">{{ $item->principal_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="modern-group">
                            <label class="modern-label mb-4">Activity</label>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" style="" name="activity"
                                        id="" value="inbound"><label class="ml-2"> Inbound</label>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" style="" name="activity"
                                        id="" value="outbound"><label class="ml-2"> Outbound</label>
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-block modern-btn text-white mt-2">
                            Save
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-lg-8 col-md-6">
                <div class="modern-card h-100">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>🚚 Inbound</h5>
                            <div id="inboundList"></div>
                        </div>

                        <div class="col-md-6">
                            <h5>📦 Outbound</h5>
                            <div id="outboundList"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var success = new Audio("{{ url('assets/audio/success.mp3') }}");
        var error = new Audio("{{ url('assets/audio/error.mp3') }}");

        const isMakassar = @json($isMakassar);

        function setDefaultDateTime() {
            let now = new Date();

            if (isMakassar) {
                now.setHours(now.getHours() + 1);
            }

            // format ke YYYY-MM-DDTHH:mm
            let year = now.getFullYear();
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let day = String(now.getDate()).padStart(2, '0');
            let hours = String(now.getHours()).padStart(2, '0');
            let minutes = String(now.getMinutes()).padStart(2, '0');

            let formatted = `${year}-${month}-${day}T${hours}:${minutes}`;

            document.getElementById('gate_in_at').value = formatted;
        }

        setDefaultDateTime();
        document.getElementById('gate_in_at').addEventListener('focus', setDefaultDateTime);

        function renderList(data) {
            let html = '';

            data.forEach(item => {
                html += `
        <div class="modern-item d-flex justify-content-between align-items-center">
            <div>
                <strong>${item.vehicle_number}</strong>
                <div class="text-muted small">
                    ${item.driver_name} • ${item.vehicle_type}
                </div>
                <small class="text-muted">${item.gate_in_at}</small>
            </div>
            <button 
                class="btn btn-lg btn-danger btn-delete"
                data-id="${item.id}">
                <i class="fa-solid fa-sign-out-alt"></i>
                Gate Out Now!
            </button>
        </div>
        `;
            });

            return html;
        }


        function loadData() {
            fetch("{{ url('warehouse/gate-in/list') }}")
                .then(res => res.json())
                .then(res => {
                    document.getElementById('inboundList').innerHTML =
                        renderList(res.inbound);
                    document.getElementById('outboundList').innerHTML =
                        renderList(res.outbound);
                    gateOut();
                });
        }

        function validateActivity() {
            let checked = document.querySelectorAll('.form-check-input:checked').length;

            if (checked === 0) {
                alert('Please select at least one activity (Inbound or Outbound).');
                return false;
            }

            return true;
        }


        function gateOut() {
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    if (!confirm('Are you sure you want to mark this vehicle as departed?')) return;
                    fetch(`{{ url('warehouse/gate-in/gate-out') }}/${id}`, {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            success.play();
                            loadData(); // reload list
                        })
                        .catch(err => {
                            console.error(err);
                        });

                });
            });
        }

        $('input[type="checkbox"]').on('change', function() {
            $('input[type="checkbox"]').not(this).prop('checked', false);
        });

        $('#formGateIn').on('submit', function(e) {
            e.preventDefault();
            if (!validateActivity()) {
                return;
            }
            $.ajax({
                url: "{{ url('warehouse/gate-in/store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        $('#formGateIn')[0].reset();
                        $('#successAlert').removeClass('d-none');
                        setTimeout(() => {
                            $('#successAlert').addClass('d-none');
                        }, 2000);
                        success.play();
                        loadData();
                    } else if (response.status == 'exists') {
                        error.play();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        })
                    } else {
                        error.play();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        })
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        })

        loadData();
    </script>
@endpush
