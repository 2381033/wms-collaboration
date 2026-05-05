@extends('layouts.new.base')
@section('title', 'MKT - Berita Acara DC')

@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style>
        .upload-box {
            display: block;
            border: 2px dashed #009ef7;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            background: #f8fbff;
            transition: 0.3s;
        }

        .upload-box:hover {
            background: #e6f4ff;
        }

        .upload-box input[type="file"] {
            display: none;
        }

        .upload-icon {
            font-size: 40px;
            color: #009ef7;
            margin-bottom: 10px;
        }

        .file-info {
            margin-top: 10px;
            font-size: 13px;
            color: #333;
            font-weight: 500;
        }

        /* FLOATING BUTTON */
        .fab-search {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 55px;
            height: 55px;
            background: #3699FF;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 999;
        }

        /* PANEL */
        .search-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 350px;
            height: 100%;
            background: #fff;
            box-shadow: -3px 0 10px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
            z-index: 1000;
        }

        .search-panel.active {
            right: 0;
        }

        /* HEADER */
        .search-header {
            padding: 15px;
            background: #3699FF;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* CLOSE BTN */
        .btn-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        /* BODY */
        .search-body {
            padding: 15px;
        }

        /* RESULT */
        .search-result {
            margin-top: 10px;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card" style="border-radius: 15px;">
            <div class="card-body">
                <div class="form-group mb-8">
                    <div class="alert alert-custom alert-default" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning" style="zoom: 170%"></i></div>
                        <div class="alert-text">
                            FORM BERITA ACARA DC<br>
                            1. Form ini digunakan untuk mencatat kejadian-kejadian yang terjadi di DC yang memerlukan tindak
                            lanjut berupa investigasi, perbaikan, atau tindakan lainnya.<br>
                            2. Isilah form ini dengan lengkap dan jelas, termasuk tanggal kejadian, kategori, sub kategori,
                            kronologis, dan informasi lainnya yang relevan.<br>
                            3. Setelah form diisi, klik tombol "Save" untuk menyimpan data. Data yang tersimpan akan
                            digunakan untuk analisis dan tindak lanjut oleh tim terkait.<br>
                            4. Pastikan untuk selalu mematuhi prosedur keselamatan dan keamanan saat berada di area DC,
                            serta melaporkan setiap kejadian yang terjadi kepada atasan atau tim keamanan.<br>
                            5. Form ini hanya digunakan untuk keperluan internal dan tidak boleh disebarluaskan ke pihak
                            luar tanpa izin dari manajemen.
                        </div>
                    </div>
                </div>
                <form id="formBA" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tanggal Temuan/Laporan<span class="text-danger">*</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="date" name="tanggal_temuan"
                                    class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" id="kategori" class="form-control" required>
                                    <option value="" selected disabled>Pilih Kategori</option>
                                    <option value="Kecelakaan Kerja">Kecelakaan Kerja</option>
                                    <option value="Near-miss">Near-miss</option>
                                    <option value="Kerusakan Product">Kerusakan Product</option>
                                    <option value="Kerusakan Property">Kerusakan Property</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sub Kategori <span class="text-danger">*</span></label>
                                <div id="subKategoriWrapper">
                                    <input type="text" name="sub_kategori" class="form-control"
                                        placeholder="Silahkan isi..">
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No Manual BA <span class="text-muted">(Optional)</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="no_manual"
                                    class="form-control">
                            </div>
                            <div class="form-group">
                                <label>No Document In-Out <span class="text-muted">(Optional)</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="no_doc"
                                    class="form-control">
                            </div>
                            <div class="form-group">
                                <label>No Reff <span class="text-muted">(Optional)</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="no_reff"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Branch <span class="text-danger">*</span></label>
                                <select name="branch" id="branch" class="form-control" required>
                                    <option value="" selected disabled>Pilih Branch</option>
                                    @foreach ($branch as $item)
                                        <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Principal <span class="text-danger">*</span></label>
                                <select name="principal" id="principal" class="form-control" required>
                                    <option value="" selected disabled>Pilih Principal</option>
                                    @foreach ($principal as $item)
                                        <option value="{{ $item->principal_name }}">{{ $item->principal_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tempat Kejadian <span class="text-danger">*</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="tempat_kejadian"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Kronologis <span class="text-danger">*</span></label>
                                <textarea name="kronologis" class="form-control" rows="5" autocomplete="off" placeholder="Silahkan isi.."></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Penanganan Yang Dilakukan <span class="text-danger">*</span></label>
                                <textarea name="solusi" class="form-control" rows="5" autocomplete="off" placeholder="Silahkan isi.."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>
                    <div class="row ttdAutomate">
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Upload Dokumentasi</label>
                                <label class="upload-box">
                                    <input type="file" id="fileInput" name="file[]" multiple
                                        accept=".jpg,.jpeg,.png">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        <h5>Drop files here or click to upload</h5>
                                        <span>Upload multiple files (jpg, png)</span>
                                        <div id="fileInfo" class="file-info">
                                            Belum ada file dipilih
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button type="reset" class="btn btn-secondary">Cancel</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <div id="fabSearch" class="fab-search" style="background-color: red">
        <i class="fas fa-filter text-white"></i>
    </div>

    <div id="searchPanel" class="search-panel">
        <div class="search-header" style="background-color: red">
            <span>Filter Berita Acara</span>
            <button id="closeSearch" class="btn-close">&times;</button>
        </div>

        <div class="search-body">

            <div class="form-group">
                <label>Date From</label>
                <input type="date" id="date_from" class="form-control" value="{{ date('Y-m-01') }}" required>
            </div>

            <div class="form-group">
                <label>Date To</label>
                <input type="date" id="date_to" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label>Branch</label>
                <select id="branch" class="form-control" name="branch_id">
                    <option value="ALL">ALL</option>
                    @foreach ($branch as $item)
                        <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            <button id="btnFilter" class="btn btn-primary btn-block mt-3">
                Filter
            </button>
            <hr>
            <div id="searchResult" class="search-result">
                <p class="text-muted">Belum ada data</p>
            </div>

        </div>
    </div>


@endsection

@push('scripts')
    <script>
        document.getElementById('fileInput').addEventListener('change', function() {
            const count = this.files.length;
            const info = document.getElementById('fileInfo');

            if (count > 0) {
                info.innerHTML = count + ' file dipilih';
            } else {
                info.innerHTML = 'Belum ada file dipilih';
            }
        });

        $('#kategori').on('change', function() {
            let kategori = $(this).val();
            let html = '';
            if (kategori === 'Kecelakaan Kerja') {
                html = `<select name="sub_kategori" class="form-control" required>
                            <option value="">Pilih Sub Kategori</option>
                            <option value="Fatality">Kematian (Fatality)</option>
                            <option value="Lost Time Injury">Lost Time Injury</option>
                            <option value="Medical Treatment Injury">Medical Treatment Injury</option>
                            <option value="First Aid Injury">First Aid Injury</option>
                        </select>`;
            } else if (kategori === 'Lainnya') {
                html = `<input type="text" name="sub_kategori" class="form-control"
                placeholder="Silahkan isi sub kategori..." required>`;
            } else {
                html = `<input type="text" name="sub_kategori" class="form-control"
                value="${kategori}" readonly>`;
            }
            $('#subKategoriWrapper').html(html);
            appendTTD(kategori);
        });

        function appendTTD(params) {
            $('.ttdAutomate').html("");
            if (params == 'Kerusakan Product' || params == 'Kerusakan Property') {
                $('.ttdAutomate').html(` <div class="col-sm-2">
                            <div class="form-group">
                                <label>Di Buat Oleh</label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="created_by"
                                    class="form-control" value="{{ auth()->user()->name }}" readonly required>
                            </div>
                            <input autocomplete="off" type="text" class="form-control" readonly value="Pelapor">
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Saksi <span class="text-danger">*</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi Nama.." type="text"
                                    name="qc" class="form-control" required>
                            </div>
                            <input autocomplete="off" placeholder="Silahkan isi Posisi.." type="text"
                                name="posisi_qc" class="form-control" required>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Di Periksa <span class="text-danger">*</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="mengetahui"
                                    class="form-control" required>
                            </div>
                            <input autocomplete="off" placeholder="Silahkan isi Posisi.." type="text"
                                name="posisi_mengetahui" class="form-control" required>
                        </div>
                          <div class="col-sm-2">
                            <div class="form-group">
                                <label>Mengetahui <span class="text-danger">*</span> </label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="pj"
                                    class="form-control" required>
                            </div>
                            <input autocomplete="off" placeholder="Silahkan isi Posisi.." type="text"
                                name="posisi_pj" class="form-control" required>
                        </div>
                           <div class="col-sm-4">
                            <div class="form-group">
                                <label>Tanda Tangan Pihak Ke-2 <span class="text-muted">(Exp: Principal)</span> </label>
                                <select name="ttd_pihak2" class="form-control">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                        </div>`);
            } else {
                $('.ttdAutomate').html(` <div class="col-sm-2">
                            <div class="form-group">
                                <label>Di Buat Oleh</label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="created_by"
                                    class="form-control" value="{{ auth()->user()->name }}" readonly required>
                            </div>
                            <input autocomplete="off" type="text" class="form-control" readonly value="Pelapor">
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>QC <span class="text-danger">*</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi Nama.." type="text"
                                    name="qc" class="form-control" required>
                            </div>
                            <input autocomplete="off" placeholder="Silahkan isi Posisi.." type="text"
                                name="posisi_qc" class="form-control" required>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Di Ketahui Oleh <span class="text-danger">*</span></label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="mengetahui"
                                    class="form-control" required>
                            </div>
                            <input autocomplete="off" placeholder="Silahkan isi Posisi.." type="text"
                                name="posisi_mengetahui" class="form-control" required>
                        </div>
                          <div class="col-sm-2">
                            <div class="form-group">
                                <label>Bertanggung Jawab <span class="text-danger">*</span> </label>
                                <input autocomplete="off" placeholder="Silahkan isi.." type="text" name="pj"
                                    class="form-control" required>
                            </div>
                            <input autocomplete="off" placeholder="Silahkan isi Posisi.." type="text"
                                name="posisi_pj" class="form-control" required>
                        </div>
                           <div class="col-sm-4">
                            <div class="form-group">
                                <label>Tanda Tangan Pihak Ke-2 <span class="text-muted">(Exp: Principal)</span> </label>
                                <select name="ttd_pihak2" class="form-control">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                        </div>`);
            }
        }


        $('#formBA').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Simpan Berita Acara?',
                icon: 'question',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('storeBADC') }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(res) {
                            Swal.fire('Success', 'Data berhasil disimpan',
                                    'success')
                                .then(() => window.location.href =
                                    "{{ url('/warehouse/ba/print') }}/" + res.id);
                        },
                        error: function(err) {
                            let msg = 'Terjadi kesalahan';
                            if (err.responseJSON) {
                                if (err.responseJSON.error) {
                                    msg = err.responseJSON.error.join('<br>');
                                } else if (err.responseJSON.message) {
                                    msg = err.responseJSON.message;
                                }
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: msg
                            });
                        }
                    });

                }
            });

        });

        $('#fabSearch').on('click', function() {
            $('#searchPanel').addClass('active');
        });

        $('#closeSearch').on('click', function() {
            $('#searchPanel').removeClass('active');
        });

        $('#btnFilter').on('click', function() {
            let date_from = $('#date_from').val();
            let date_to = $('#date_to').val();
            let branch = $('#branch').val();
            $.ajax({
                url: "{{ route('filterBADC') }}",
                method: 'GET',
                data: {
                    date_from: date_from,
                    date_to: date_to,
                    branch: branch
                },
                success: function(res) {

                    let html = '';

                    if (res.length === 0) {
                        html = '<p class="text-muted">Data tidak ditemukan</p>';
                    } else {
                        res.forEach(item => {
                            html += `
                        <a href="{{ url('warehouse/ba/print') }}/${item.id}" class="list-group-item list-group-item-action">
                            <b>${item.no_doc}</b><br>
                            <small>${item.kategori} - ${item.tanggal_temuan}</small>
                            <br><b>${item.principal}</b><br>
                        </a>
                    `;
                        });
                    }

                    $('#searchResult').html(html);
                },
                error: function() {
                    alert('Gagal mengambil data');
                }
            });

        });
    </script>
@endpush
