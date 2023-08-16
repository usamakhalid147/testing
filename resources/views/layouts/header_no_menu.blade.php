<header class="header-container" v-if ="currentRouteName != 'host.signup'">
    <!-- v-if ="currentRouteName != 'host.signup'" -->
    <nav class="navbar navbar-sm navbar-expand-lg px-5 py-2">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ resolveRoute('home') }}">
                <img src="{{ $site_logo }}" class="header-logo">
            </a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-4">
                    @isset($exit_url)
                    <li>
                        <a href="{{ $exit_url }}" class="nav-link border text-white btn btn-primary px-3 me-3 d-flex align-items-center">
                            <i class="icon icon-exit " area-hidden="true"></i>
                            <span class="d-none d-md-block ms-2"> @lang('messages.exit') </span>
                        </a>
                    </li>
                    @endisset
                    <li class="nav-item">
                        <div style="width: 180px;" id="conveythis-language"></div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dropdown-item active" href="{{ resolveRoute('home') }}">@lang('messages.home')</a>
                    </li>
                    @guest
                    <li class="nav-item">
                        <a class="nav-link dropdown-item" href="{{ resolveRoute('login') }}">@lang('messages.signin')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dropdown-item" href="{{ resolveRoute('host.login') }}">@lang('messages.login_as_host')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dropdown-item" href="{{ resolveRoute('signup') }}">@lang('messages.register')</a>
                    </li>
                    @endguest
                    <li class="nav-item">
                        <a class="nav-link dropdown-item" href="{{ resolveRoute('contact_us') }}">@lang('messages.contact_us')</a>
                    </li>
                    @auth
                    <li class="nav-item dropdown log-menu">
                        <button type="button" class="dropdown-toggle d-flex align-items-lg-center" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                            <div class="me-2">
                                <span class="icon icon-menu fs-5" aria-hidden="true"></span>
                            </div>
                            <img src="{{ Auth::user()->profile_picture_src }}" class="icon-sm img-fluid" alt="{{ Auth::user()->first_name }}">
                            <span class="badge bg-primary message-count-pro" v-if="inbox_count > 0"> &nbsp; </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('dashboard') }}"> @lang('messages.dashboard') </a>
                            </li>
                            <li>
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('bookings') }}"> @lang('messages.bookings') </a>
                            </li>
                            <li> 
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('view_profile',[auth()->id()]) }}"> @lang('messages.profile') </a>
                            </li>
                            <li>
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('wishlists') }}"> @lang('messages.wishlists') </a>
                            </li>
                            <li>
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('account_settings') }}"> @lang('messages.account') </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @if(global_settings('referral_enabled'))
                            <li class="nav-item">
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('invite') }}"> @lang('messages.refer_and_earn') </a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('help') }}"> @lang('messages.help') </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('logout') }}"> @lang('messages.logout') </a>
                            </li>
                       </ul>
                    </li>
                    @endauth

                    {{--
                    <li class="nav-item dropdown log-menu">
                        <button type="button" class="dropdown-toggle d-flex align-items-lg-center" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                            <div class="me-2">
                                <span class="icon icon-menu fs-5" aria-hidden="true"></span>
                            </div>
                            <img src="{{ asset('images/profile_picture.png') }}" class="icon-sm img-fluid" alt="Default User">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="nav-link dropdown-item" href="{{ route('signup') }}"> @lang('messages.signup') </a>
                            </li>
                            <li> 
                                <a class="nav-link  dropdown-item" href="{{ resolveRoute('login') }}"> @lang('messages.login_as_guest') </a>
                            </li>
                            <li>
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('host.login') }}"> @lang('messages.login_as_host') </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @if(global_settings('referral_enabled'))
                            <li class="nav-item">
                                <a class="nav-link dropdown-item" href="{{ resolveRoute('invite') }}"> @lang('messages.refer_and_earn') </a>
                            </li>
                            @endif
                       </ul>
                    </li>
                    --}}
                </ul>
            </div>
        </div>
    </nav>
</header>