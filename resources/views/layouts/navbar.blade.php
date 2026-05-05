<!-- <section id="topbar" class="d-none d-lg-block">
    <div class="container d-flex">
        <div class="contact-info mr-auto">
            <i class="icofont-envelope"></i><a href="mailto:runclean.id@gmail.com">marketing.mkt@samudera.id</a>
            <i class="icofont-phone"></i> (021) 29088220
        </div>
        <div class="social-links">
            <a href="http://www.facebook.com/samuderaID" target="_blank" class="facebook"><i class="icofont-facebook"></i></a>
            <a href="http://www.twitter.com/samudera_ind" target="_blank" class="twitter"><i class="icofont-twitter"></i></a>
            <a href="http://www.instagram.com/samudera.id" target="_blank" class="instagram"><i class="icofont-instagram"></i></a>
        </div>
    </div>
</section> -->

<header id="header">
    <div class="container d-flex">
        <div class="logo mr-auto">
            <a href="{{ route('home') }}"><img src="{{ asset('images/logos.png') }}" alt=""
                    class="img-fluid"></a>
        </div>

        <nav class="nav-menu d-none d-lg-block">
            <ul>
                <li @if (Request::segment(1) == 'home') class="active" @endif><a href="{{ route('home') }}"><i
                            class="fas fa-home"></i> Home</a></li>
                @if (Auth::guest())
                    <li @if (Request::segment(1) == 'about') class="active" @endif><a
                            href="{{ route('profile.about') }}">About</a></li>
                    <li @if (Request::segment(1) == 'services') class="active" @endif><a
                            href="{{ route('profile.services') }}">Services</a></li>
                    <li @if (Request::segment(1) == 'contact') class="active" @endif><a
                            href="{{ route('profile.contact') }}">Contact</a></li>
                    <li @if (Request::segment(1) == 'login') class="active" @endif><a href="{{ route('login') }}">Login</a>
                    </li>
                @else
                    @if (Auth::user()->is_maintenance == 'No')
                        {!! AksesHelpers::menu() !!}
                    @endif
                    <li>
                        <a href="javascript:void(0)" onclick="logout()">
                            <i class="fas fa-power-off"></i> Logout
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</header>
