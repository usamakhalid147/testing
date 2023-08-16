<header>
    <div class="w-100 bg-teal"> 
        <div class="d-flex justify-content-end">
            <ul class="d-flex align-items-center px-2 px-md-5 py-1 mb-0 list-unstyled text-white">
                <li class="flex-shrink-0 nav-item hstack rounded align-items-center langs-currency me-2" title="currency">
                    <div class="col currency-dropdown" v-show="{{ $currency_list->count() > 1 }}">
                        {!! Form::select('currency_code',$currency_list, '', ['id' => 'user-currency','class' => 'form-select','v-model' => 'userCurrency','v-on:change' => "updateUserDefault('currency')"]) !!}
                    </div>
                </li>
                <li class="nav-item hstack rounded align-items-center langs-currency" style="width: 180px" title="language">
                    <div id="conveythis-language">
                    </div>
                    <!-- <div class="col language-dropdown ms-3 me-auto" v-show="{{ $language_list->count() > 1 }}">
                        {!! Form::select('language',$language_list, '', ['id' => 'user-language','class' => 'form-select','v-model' => 'userLanguage','v-on:change' => "updateUserDefault('language')"]) !!}
                        {{--
                            <select onchange="doGTranslate(this);"><option value="">Select Language</option><option value="en|en">English</option><option value="en|ar">Arabic</option><option value="en|zh-CN">Chinese (Simplified)</option><option value="en|fr">French</option><option value="en|de">German</option><option value="en|hi">Hindi</option><option value="en|it">Italian</option><option value="en|ja">Japanese</option><option value="en|pt">Portuguese</option><option value="en|ru">Russian</option><option value="en|es">Spanish</option><option value="en|iw">Hebrew</option><option value="en|vi">Vietnamese</option><option value="en|hy">Armenian</option></select>
                        --}}
                        {{--
                        <select class="form-select" v-model="userLanguage" v-on:change="updateUserDefault('language');">
                            <option value="en">English</option>
                            <option value="ja">Japanese</option>
                            <option value="ko">korean</option>
                            <option value="vi">Vietnamese</option>
                            <option value="zh-CN">Chinese (Simplified)</option>
                            <option value="zh-TW">Chinese (Traditional)</option>
                        </select>
                        --}}
                    </div> -->
                </li>
                <li class="nav-item" title="help">
                    <a class="nav-link icon-img-link m-0 px-2 py-0" href="{{ resolveRoute('help') }}"> 
                        <i class="icon icon-help"></i>
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link active" href="{{ resolveRoute('host.signup') }}">@lang('messages.list_your_property')</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="px-2 px-md-5">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ resolveRoute('home') }}">
                    <img src="{{ $site_logo }}" class="header-logo">
                </a>
                <button class="border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasResponsive" aria-controls="offcanvasResponsive">
                    <span class="icon icon-menu text-muted"></span>
                </button>
                <div class="offcanvas-lg offcanvas-end d-block d-lg-none" tabindex="-1" id="offcanvasResponsive" aria-labelledby="offcanvasResponsiveLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasResponsiveLabel">Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#offcanvasResponsive" aria-label="Close"></button>
                    </div>
                    <div class="h-100 mb-5 offcanvas-body overflow-auto pb-4">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-4">
                            @auth
                                <li class="nav-item dropdown">
                                    <a href="#" class="nav-link py-0" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="{{ auth()->user()->profile_picture_src }}" />
                                    </a>
                                </li>
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
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('account_settings') }}"> @lang('messages.account') </a>
                                </li>
                                @if(global_settings('referral_enabled'))
                                <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('invite') }}"> @lang('messages.refer_and_earn') </a>
                                </li>
                                @endif
                                <li>
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('help') }}"> @lang('messages.help') </a>
                                </li>
                                <li>
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('logout') }}"> @lang('messages.logout') </a>
                                </li>
                            @else
                                <li>
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('signup') }}"> @lang('messages.signup') </a>
                                </li>
                                <li>
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('login') }}"> @lang('messages.login_as_guest') </a>
                                </li>
                                @if(global_settings('referral_enabled'))
                                <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('invite') }}"> @lang('messages.refer_and_earn') </a>
                                </li>
                                @endif
                            @endauth
                        </ul>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-4">
                        <li class="nav-item">
                            <a class="nav-link dropdown-item active" href="{{ resolveRoute('home') }}">@lang('messages.home')</a>
                        </li>
                        @guest
                        <!-- <li class="nav-item">
                            <a class="nav-link dropdown-item" href="{{ resolveRoute('login') }}">@lang('messages.signin')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-item" href="{{ resolveRoute('signup') }}">@lang('messages.register')</a>
                        </li> -->
                        <li class="nav-item dropdown">
                            <a href="" type="button" class="p-2 dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                @lang('messages.member')
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                 <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('login') }}">@lang('messages.signin')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('signup') }}">@lang('messages.register')</a>
                                </li>
                           </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="" type="button" class="p-2 dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                @lang('messages.host')
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                 <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('host.login') }}">@lang('messages.signin')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('host.signup') }}">@lang('messages.register')</a>
                                </li>
                           </ul>
                        </li>
                        @endguest
                        <li class="nav-item">
                            <a class="nav-link dropdown-item" href="{{ resolveRoute('contact_us') }}">@lang('messages.contact_us')</a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link dropdown-item text-decoration-none" href="javascript:void(0);">@lang('messages.hi') {{ Auth::user()->first_name }}!</a>
                        </li>
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
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('update_account_settings',['page' => 'personal-information']) }}"> @lang('messages.profile') </a>
                                </li>
                                <li>
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('bookings') }}"> @lang('messages.bookings') </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('update_account_settings',['page' => 'transactions']) }}"> @lang('messages.transaction_history') </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('reviews') }}"> @lang('messages.reviews') </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('update_account_settings',['page' => 'login-and-security']) }}"> @lang('messages.login_and_security') </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('update_account_settings',['page' => 'site-setting']) }}"> @lang('messages.global_preference') </a>
                                </li>
                                {{--
                                <li> 
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('view_profile',[auth()->id()]) }}"> @lang('messages.profile') </a>
                                </li>
                                <li>
                                    <a class="nav-link dropdown-item" href="{{ resolveRoute('account_settings') }}"> @lang('messages.account') </a>
                                </li>
                                --}}
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
    </div>
</header>
