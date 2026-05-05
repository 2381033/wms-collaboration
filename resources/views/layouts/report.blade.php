<!DOCTYPE html>
<html>

<head>
    @yield('css')
</head>

<body>
    <div class="page">
        <div class="header">
            <img alt="image" class="mr-3 logo" src="{{ asset('images/logos.png') }}" />
        </div>
        <table class="table-template">
            <thead>
                <tr>
                    <td>
                        <div class="header-space">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="title">
                            <h3 class="title-header">
                                @yield('title')
                            </h3>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            @yield('content')
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <div class="footer-space">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="signature">
                            @yield('signature')
                            <!-- <div class="footer-space">&nbsp;</div> -->
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="footer">
        Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
    </div>
    <script>
        document.title = "{{ $title ?? '' }}"
        // window.print()
    </script>
</body>

</html>
