<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <!-- <base href="{{ url('/') }}"> -->
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/new/css/fonts.css') }}" />
    <link href="{{ asset('/assets/new/plugins/global/plugins.bundle.css?v=7.0.5') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/new/css/style.bundle.css?v=7.0.5') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/new/css/themes/layout/header/base/light.css?v=7.0.5') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('/assets/new/css/themes/layout/header/menu/light.css?v=7.0.5') }}" rel="stylesheet"
        type="text/css" />
    <link rel="shortcut icon" href="{{ url('/') }}/images/favicon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style type="text/css">
        ::-webkit-scrollbar {
            -webkit-appearance: none;
        }

        ::-webkit-scrollbar:vertical {
            width: 12px;
        }

        ::-webkit-scrollbar:horizontal {
            height: 7px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, .2);
            border-radius: 10px;
            border: 2px solid #ffffff;
        }

        ::-webkit-scrollbar-track {
            border-radius: 10px;
            background-color: #ffffff;
        }

        .hide {
            display: none;
        }

        .table-thin tr td,
        .table-thin tr th {
            padding: 5px !important
        }

        .print-area {
            display: none;
        }

        @media print {
            .hide-print {
                display: none !important;
            }

            .print-area {
                display: block;
                max-width: 100% !important;
                width: 100% !important;
            }
        }

        .loading-overlay {
            display: none;
            background: rgba(255, 255, 255, 0.7);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: 0;
            z-index: 9998;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.is-active {
            display: flex;
        }

        .code {
            font-family: monospace;
            color: #dd4a68;
            background-color: rgb(238, 238, 238);
            padding: 0 3px;
        }

        * {
            padding: 0;
            margin: 0;
        }

        body {
            font-family: Verdana, Geneva, sans-serif;
            font-size: 18px;
            background-color: #CCC;
        }

        .float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            box-shadow: 2px 2px 3px #999;
        }

        .my-float {
            margin-top: 22px;
        }

        @media (max-width:576px) {
            .container h1 {
                font-size: 2rem;
            }

        }

        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            padding: 20px;

        }

        .image-gallery div {
            flex-grow: 1;
            margin: 5px;
            overflow: hidden;
            cursor: pointer;
        }

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all .3s ease-in;
        }

        img:hover {
            transform: scale(1.2);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/new/css/loading.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
</head>

<body id="kt_body" class="header-fixed header-mobile-fixed page-loading">
    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed hide-print"
        style="right: 50px">
        <a href="{{ url('/') }}">
            <img alt="Logo" src="{{ url('/') }}/images/logos.png" style="width: 140px;" />
        </a>
        <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
                <span></span>
            </button>
        </div>
    </div>
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                @include('layouts.new.header')
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid">
                        <div class="loading-overlay justify-content-center">
                            <div class="drawing">
                                <div class="loading-dot"></div>
                            </div>
                        </div>
                        @yield('content')
                    </div>
                </div>
                @include('layouts.new.footer')
            </div>
        </div>
    </div>
    @if (Auth::check())
        <div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
            <!--begin::Header-->
            <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
                <h3 class="font-weight-bold m-0">User Profile</h3>
                <a href="javascript:" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
                    <i class="ki ki-close icon-xs text-muted"></i>
                </a>
            </div>
            <div class="offcanvas-content pr-5 mr-n5">
                @if (Auth::check())
                    <div id="show-profile" style="position: relative">
                        <div class="d-flex mt-5 border rounded p-2">
                            <button onClick="editProfile()" style="position: absolute; top: -12px; right: -5px"
                                class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                data-toggle="tooltip" title="" data-original-title="Edit Profile">
                                <i class="fa fa-pen icon-sm text-muted"></i>
                            </button>
                            <div class="symbol symbol-70 mr-5">
                                <span class="symbol symbol-35 symbol-light-success">
                                    <span
                                        class="symbol-label font-size-h5 font-weight-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </span>
                                <i class="symbol-badge bg-success"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="javascript:"
                                    class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">{{ substr(Auth::user()->name, 0, 20) }}</a>
                                <div class="text-muted">
                                    {{-- @if (Auth::user()->department)
                                        {{ Auth::user()->department->name }}
                                    @endif --}}
                                </div>
                                <div class="navi">
                                    <a href="javascript:" class="navi-item">
                                        <span class="navi-link p-0 pb-2">
                                            <span class="navi-icon mr-1">
                                                <span class="svg-icon svg-icon-lg svg-icon-primary">
                                                    <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Mail-notification.svg-->
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                            <path
                                                                d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z"
                                                                fill="#000000"></path>
                                                            <circle fill="#000000" opacity="0.3" cx="19.5"
                                                                cy="17.5" r="2.5"></circle>
                                                        </g>
                                                    </svg>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </span>
                                            <span
                                                class="navi-text text-muted text-hover-primary">{{ substr(Auth::user()->email, 0, 20) }}</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="navi mt-2">
                        <a href="javascript:"
                            class="btn btn-sm btn-light-danger font-weight-bolder py-2 px-5 logout">Sign Out</a>
                    </div>

                    <div class="separator separator-dashed mt-8 mb-5"></div>

                    <a href="javascript:" onClick="toggleChangePassword()"><i class="flaticon flaticon-lock"></i>
                        Change My Password</a>

                    <div class="rounded border p-5 mt-2" id="change-password-card" style="display: none">
                        <h3>Change Password</h3>
                        <form id="form-change-password">
                            <div class="form-group row">
                                <div class="col-12" id="my_old_password">
                                    <input name="my_old_password"
                                        class="check-validation form-control form-control-lg form-control-solid mb-1"
                                        type="password" placeholder="Current Password">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-12 mt-3" id="my_new_password">
                                    <input name="my_new_password"
                                        class="check-validation form-control form-control-lg form-control-solid mb-1"
                                        type="password" placeholder="New Password">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-12" id="my_verify_password">
                                    <input name="my_verify_password"
                                        class="check-validation form-control form-control-lg form-control-solid mb-1"
                                        type="password" placeholder="Verify Password">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button onClick="toggleChangePassword()" class="btn btn-secondary">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
    <script src="{{ asset('assets/new/plugins/global/plugins.bundle.js?v=7.0.5') }}"></script>
    <script src="{{ asset('assets/new/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.5') }}"></script>
    <script src="{{ asset('assets/new/js/scripts.bundle.js?v=7.0.5') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function kasihNol(data) {
            if (data < 10) {
                return '0' + data;
            } else {
                return data;
            }
        }

        function formatTanggalIndonesia(tanggal) {
            const today = new Date(tanggal);
            return kasihNol(today.getDate()) + '/' + kasihNol((today.getMonth() + 1)) + '/' + kasihNol(today.getFullYear());
        }

        function formatTanggalIndonesia2(tanggal) {
            var formated;
            const today = new Date(tanggal);
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            formated = kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + kasihNol(today.getFullYear());

            if (tanggal == null || tanggal == '') {
                formated = '';
            }

            return formated;
        }

        function formatTanggalWaktuIndonesia2(tanggal) {
            var formated;
            const today = new Date(tanggal);
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            formated = kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + kasihNol(today.getFullYear()) +
                ' ' + kasihNol(today.getHours()) + ':' + kasihNol(today.getMinutes()) + ':' + kasihNol(today.getSeconds());

            if (tanggal == null || tanggal == '') {
                formated = '';
            }

            return formated;
        }

        $(".datepicker-year").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });

        $(document).ajaxSend(function(event, jqxhr, settings) {
            // Start pace
            $('.loading-overlay').addClass('d-flex');
            // $('#clock-loader').fadeIn('normal');
        });

        // $(document).ajaxStop(function (event, jqxhr, settings) {
        //     // Start pace
        //     $('.loading-overlay').removeClass('d-flex');
        //     // $('#clock-loader').fadeIn('normal');
        // });

        $(document).ajaxComplete(function(event, jqxhr, settings) {
            // Stop pace
            $('.loading-overlay').removeClass('d-flex');
            // $('#clock-loader').fadeOut('normal')
        });

        $(document).ajaxError(function(event, jqxhr, settings) {
            // Stop pace
            $('.loading-overlay').removeClass('d-flex');
            // $('#clock-loader').fadeOut('normal')
        });
    </script>
    <script>
        $(".logout").click(function() {
            $.ajax({
                url: "{{ url('/logout') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    window.location.href = "{{ url('/login') }}";
                },
                error: function(error) {
                    console.log(error);
                    window.location.href = "{{ url('/login') }}";
                }
            });
        });
    </script>
    @stack('scripts')
</body>
<!--end::Body-->

</html>
