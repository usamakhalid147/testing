<div class="sidebar sidebar-style-2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-start me-2">
                    <img src="{{ asset('images/profile.png') }}" class="avatar-img rounded-circle">
                </div>
                <div class="info">
                    <a data-bs-toggle="collapse" href="#adminInformation" aria-expanded="true">
                        <span>
                            {{ getCurrentUser()->username }}
                            <span class="user-level">{{ getCurrentUser()->role_name }}</span>
                            <span class="caret"></span>
                        </span>
                    </a>
                    <div class="clearfix"></div>
                    <div class="collapse in" id="adminInformation">
                        <ul class="nav">
                            @checkPermission('update-admin_users')
                            <li>
                                <a href="{{ route('admin.admin_users.edit',['id' => getCurrentUser()->id]) }}">
                                    <span class="link-collapse"> @lang('admin_messages.edit_profile') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            <li>
                                <a href="{{ route('admin.logout') }}">
                                    <span class="link-collapse text-danger"> @lang('admin_messages.logout') </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <ul class="nav nav-primary" id="nav-primary">
                <li class="nav-item {{ in_array($active_menu,['dashboard']) ? 'active':'' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="la flaticon-analytics"></i>
                        <p> @lang('admin_messages.dashboard') </p>
                    </a>
                </li>
                @checkPermission('*-admin_users|*-roles|*-login_sliders')
                <li class="nav-item {{ in_array($active_menu,['admin_users', 'roles_privilege','login_sliders']) ? 'active':'' }}">
                    <a data-bs-toggle="collapse" href="#AdminUserDropdown" class="collapsed" aria-expanded="{{ in_array($active_menu,['admin_users', 'roles_privilege','login_sliders']) ? 'true':'false' }}">
                        <i class="fas fa-user-shield"></i>
                        <p> @lang('admin_messages.manage_admin') </p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['admin_users', 'roles_privilege','login_sliders']) ? 'show':'hide' }}" id="AdminUserDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkPermission('*-admin_users')
                            <li class="{{ ($active_menu == 'admin_users') ? 'active':'' }}">
                                <a href="{{ route('admin.admin_users') }}">
                                    <span class="sub-item">@lang('admin_messages.admin_agents') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-roles')
                            <li class="{{ ($active_menu == 'roles_privilege') ? 'active':'' }}">
                                <a href="{{ route('admin.roles') }}">
                                    <span class="sub-item">@lang('admin_messages.roles_privilege') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-login_sliders')
                            <li class="{{ ($active_menu == 'login_sliders') ? 'active':'' }}">
                                <a href="{{ route('admin.login_sliders') }}">
                                    <span class="sub-item">@lang('admin_messages.login_sliders') </span>
                                </a>
                            </li>
                            @endcheckPermission
                        </ul>
                    </div>
                </li>
                @endcheckPermission
                @checkPermission('*-users|*-email_to_users')
                <li class="nav-item {{ in_array($active_menu,['users', 'email_to_users','hosts']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#UserDropdown" aria-expanded="{{ in_array($active_menu,['users', 'email_to_users','hosts']) ? 'true':'false' }}">
                        <i class="fas fa-users"></i>
                        <p> @lang('admin_messages.manage_users') </p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['users', 'email_to_users','hosts']) ? 'show':'hide' }}" id="UserDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkPermission('*-users')
                            <li class="{{ ($active_menu == 'users') ? 'active':'' }}">
                                <a href="{{ route('admin.users') }}">
                                    <span class="sub-item"> @lang('admin_messages.users') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-hosts')
                            <li class="{{ ($active_menu == 'hosts') ? 'active':'' }}">
                                <a href="{{ route('admin.hosts') }}">
                                    <span class="sub-item"> @lang('admin_messages.hosts') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            {{--
                            @checkPermission('*-email_to_users')
                            <li class="{{ ($active_menu == 'email_to_users') ? 'active':'' }}">
                                <a href="{{ route('admin.email_to_users') }}">
                                    <span class="sub-item"> @lang('admin_messages.email_to_users') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            --}}
                        </ul>
                    </div>
                </li>
                @endcheckPermission
                
                @checkPermission('*-sliders|*-featured_cities|*-popular_cities|*-popular_localities|*-pre_footers|*-discount_banners')
                <li class="nav-item {{ in_array($active_menu,['sliders', 'featured_cities', 'popular_cities', 'popular_localities', 'pre_footers','discount_banners']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#HomePageDropdown" aria-expanded="{{ in_array($active_menu,['sliders', 'featured_cities', 'popular_cities', 'popular_localities', 'pre_footers','discount_banners']) ? 'true':'false' }}">
                        <i class="fas fa-home"></i>
                        <p> @lang('admin_messages.home_page') </p>
                        <span class="caret"> </span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['sliders', 'featured_cities', 'popular_cities', 'popular_localities', 'pre_footers','discount_banners']) ? 'show':'hide' }}" id="HomePageDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkPermission('*-sliders')
                            <li class="{{ ($active_menu == 'sliders') ? 'active':'' }}">
                                <a href="{{ route('admin.sliders') }}">
                                    <span class="sub-item"> @lang('admin_messages.home_page_sliders') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-featured_cities')
                            <li class="{{ ($active_menu == 'featured_cities') ? 'active':'' }}">
                                <a href="{{ route('admin.featured_cities') }}">
                                    <span class="sub-item"> @lang('admin_messages.featured_cities') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-pre_footers')
                            <li class="{{ ($active_menu == 'pre_footers') ? 'active':'' }}">
                                <a href="{{ route('admin.pre_footers') }}">
                                    <span class="sub-item"> @lang('admin_messages.pre_footers') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-popular_cities')
                            <li class="{{ ($active_menu == 'popular_cities') ? 'active':'' }}">
                                <a href="{{ route('admin.popular_cities') }}">
                                    <span class="sub-item"> @lang('admin_messages.popular_cities') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            {{--                            @checkPermission('*-popular_localities')
                            <li class="{{ ($active_menu == 'popular_localities') ? 'active':'' }}">
                                <a href="{{ route('admin.popular_localities') }}">
                                    <span class="sub-item"> @lang('admin_messages.popular_localities') </span>
                                </a>
                            </li>
                            @endcheckPermission--}}
                            @checkPermission('*-discount_banners')
                            <li class="{{ ($active_menu == 'discount_banners') ? 'active':'' }}">
                                <a href="{{ route('admin.discount_banners') }}">
                                    <span class="sub-item"> @lang('admin_messages.discount_banners') </span>
                                </a>
                            </li>
                            @endcheckPermission
                        </ul>
                    </div>
                </li>
                @endcheckPermission

                @checkPermission('*-reservations|*-payouts|*-penalties|*-reviews')
                <li class="nav-item {{ in_array($active_menu,['reservations', 'payouts', 'penalties', 'reviews']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#bookingDropdown" aria-expanded="{{ in_array($active_menu,['reservations', 'payouts', 'penalties', 'reviews']) ? 'true':'false' }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <p> @lang('admin_messages.reservation_management') </p>
                        <span class="caret"> </span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['reservations', 'payouts', 'penalties', 'reviews']) ? 'show':'hide' }}" id="bookingDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkPermission('*-reservations')
                            <li class="{{ ($active_menu == 'reservations') ? 'active':'' }}">
                                <a href="{{ route('admin.reservations') }}">
                                    <span class="sub-item"> @lang('admin_messages.reservations') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-payouts')
                            <li class="{{ ($active_menu == 'payouts') ? 'active':'' }}">
                                <a href="{{ route('admin.payouts') }}">
                                    <span class="sub-item"> @lang('admin_messages.payouts') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-penalties')
                            <li class="{{ ($active_menu == 'penalties') ? 'active':'' }}">
                                <a href="{{ route('admin.penalties') }}">
                                    <span class="sub-item"> @lang('admin_messages.penalties') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-reviews')
                            <li class="{{ ($active_menu == 'reviews') ? 'active':'' }}">
                                <a href="{{ route('admin.reviews') }}">
                                    <span class="sub-item"> @lang('admin_messages.reviews') </span>
                                </a>
                            </li>
                            @endcheckPermission
                        </ul>
                    </div>
                </li>
                @endcheckPermission

                @checkPermission('*-hotels|*-rooms|*-property_types|*-room_types|*-amenity_types|*-hotel_amenities|*-room_amenities|*-bed_types|*-guest_accesses|*-hotel_rules|*-meal_plans')
                <li class="nav-item {{ in_array($active_menu,['hotels','rooms', 'property_types', 'room_types', 'amenity_types', 'hotel_amenities','room_amenities', 'bed_types', 'guest_accesses','hotel_rules','meal_plans']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#hotelManagementDropdown" aria-expanded="{{ in_array($active_menu,['hotels','rooms', 'property_types', 'room_types', 'amenity_types', 'room_amenities','hotel_amenities', 'bed_types', 'guest_accesses','hotel_rules','meal_plans']) ? 'true':'false' }}">
                        <i class="fas fa-building"></i>
                        <p> @lang('admin_messages.hotel') </p>
                        <span class="caret"> </span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['hotels', 'rooms', 'property_types', 'room_types', 'amenity_types', 'hotel_amenities','room_amenities', 'bed_types', 'guest_accesses','hotel_rules','meal_plans']) ? 'show':'hide' }}" id="hotelManagementDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkPermission('*-hotels')
                            <li class="{{ ($active_menu == 'hotels') ? 'active':'' }}">
                                <a href="{{ route('admin.hotels') }}">
                                    <span class="sub-item"> @lang('admin_messages.hotel_management') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-rooms')
                            <li class="{{ ($active_menu == 'rooms') ? 'active':'' }}">
                                <a href="{{ route('admin.rooms') }}">
                                    <span class="sub-item"> @lang('messages.room') @lang('admin_messages.management') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-property_types')
                            <li class="{{ ($active_menu == 'property_types') ? 'active':'' }}">
                                <a href="{{ route('admin.property_types') }}">
                                    <span class="sub-item"> @lang('admin_messages.property_types') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-room_types')
                            <li class="{{ ($active_menu == 'room_types') ? 'active':'' }}">
                                <a href="{{ route('admin.room_types') }}">
                                    <span class="sub-item"> @lang('admin_messages.room_types') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-amenity_types')
                            <li class="{{ ($active_menu == 'amenity_types') ? 'active':'' }}">
                                <a href="{{ route('admin.amenity_types') }}">
                                    <span class="sub-item"> @lang('admin_messages.amenity_types') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-hotel_amenities')
                            <li class="{{ ($active_menu == 'hotel_amenities') ? 'active':'' }}">
                                <a href="{{ route('admin.hotel_amenities') }}">
                                    <span class="sub-item"> @lang('admin_messages.hotel_amenities') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-room_amenities')
                            <li class="{{ ($active_menu == 'room_amenities') ? 'active':'' }}">
                                <a href="{{ route('admin.room_amenities') }}">
                                    <span class="sub-item"> @lang('admin_messages.room_amenities') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-bed_types')
                            <li class="{{ ($active_menu == 'bed_types') ? 'active':'' }}">
                                <a href="{{ route('admin.bed_types') }}">
                                    <span class="sub-item"> @lang('admin_messages.bed_types') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-guest_accesses')
                            <li class="{{ ($active_menu == 'guest_accesses') ? 'active':'' }}">
                                <a href="{{ route('admin.guest_accesses') }}">
                                    <span class="sub-item"> @lang('admin_messages.guest_accesses') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-hotel_rules')
                            <li class="{{ ($active_menu == 'hotel_rules') ? 'active':'' }}">
                                <a href="{{ route('admin.hotel_rules') }}">
                                    <span class="sub-item"> @lang('admin_messages.hotel_rules') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-meal_plans')
                            <li class="{{ ($active_menu == 'meal_plans') ? 'active':'' }}">
                                <a href="{{ route('admin.meal_plans') }}">
                                    <span class="sub-item"> @lang('admin_messages.meal_plans') </span>
                                </a>
                            </li>
                            @endcheckPermission
                        </ul>
                    </div>
                </li>
                @endcheckPermission
                @checkPermission('*-api_credentials|*-payment_gateways|*-email_configurations')
                <li class="nav-item {{ in_array($active_menu,['api_credentials', 'payment_gateways', 'email_configurations']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#CredentialsDropdown" aria-expanded="{{ in_array($active_menu,['api_credentials', 'payment_gateways', 'email_configurations']) ? 'true':'false' }}">
                        <i class="fas fa-key"></i>
                        <p> @lang('admin_messages.credentials') </p>
                        <span class="caret"> </span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['api_credentials', 'payment_gateways', 'email_configurations']) ? 'show':'hide' }}" id="CredentialsDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkPermission('*-api_credentials')
                            <li class="{{($active_menu == 'api_credentials') ? 'active':'' }}">
                                <a href="{{ route('admin.api_credentials') }}">
                                    <span class="sub-item"> @lang('admin_messages.api_credentials') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-payment_gateways')
                            <li class="{{($active_menu == 'payment_gateways') ? 'active':'' }}">
                                <a href="{{ route('admin.payment_gateways') }}">
                                    <span class="sub-item"> @lang('admin_messages.payment_gateways') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-email_configurations')
                            <li class="{{($active_menu == 'email_configurations') ? 'active':'' }}">
                                <a href="{{ route('admin.email_configurations') }}">
                                    <span class="sub-item"> @lang('admin_messages.email_configurations') </span>
                                </a>
                            </li>
                            @endcheckPermission
                        </ul>
                    </div>
                </li>
                @endcheckPermission
                @checkPermission('*-global_settings|*-theme_settings|*-social_media_links|*-metas', 'fees','referral_settings')
                <li class="nav-item {{ in_array($active_menu,['global_settings', 'theme_settings', 'social_media_links', 'metas', 'fees','referral_settings']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#siteDropdown" aria-expanded="{{ in_array($active_menu,['global_settings', 'theme_settings', 'social_media_links', 'metas', 'fees','referral_settings']) ? 'true':'false' }}">
                        <i class="fas fa-sliders-h"></i>
                        <p> @lang('admin_messages.site_management') </p>
                        <span class="caret"> </span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['global_settings', 'theme_settings', 'social_media_links', 'metas', 'fees','referral_settings']) ? 'show':'hide' }}" id="siteDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkPermission('*-global_settings')
                            <li class="{{ ($active_menu == 'global_settings') ? 'active':'' }}">
                                <a href="{{ route('admin.global_settings') }}">
                                    <span class="sub-item"> @lang('admin_messages.global_settings') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-theme_settings')
                            <li class="{{ ($active_menu == 'theme_settings') ? 'active':'' }}">
                                <a href="{{ route('admin.theme_settings') }}">
                                    <span class="sub-item"> @lang('admin_messages.theme_settings') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-social_media_links')
                            <li class="{{ ($active_menu == 'social_media_links') ? 'active':'' }}">
                                <a href="{{ route('admin.social_media_links') }}">
                                    <span class="sub-item"> @lang('admin_messages.social_media_links') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-metas')
                            <li class="{{ ($active_menu == 'metas') ? 'active':'' }}">
                                <a href="{{ route('admin.metas') }}">
                                    <span class="sub-item"> @lang('admin_messages.metas') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-fees')
                            <li class="{{ ($active_menu == 'fees') ? 'active':'' }}">
                                <a href="{{ route('admin.fees') }}">
                                    <span class="sub-item"> @lang('admin_messages.fees') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-referral_settings')
                            <li class="{{ ($active_menu == 'referral_settings') ? 'active':'' }}">
                                <a href="{{ route('admin.referral_settings') }}">
                                    <span class="sub-item"> @lang('admin_messages.referral_settings') </span>
                                </a>
                            </li>
                            @endcheckPermission
                        </ul>
                    </div>
                </li>
                @endcheckPermission
                @checkPermission('*-reports')
                <li class="nav-item {{ ($active_menu == 'reports')?'active':'' }}">
                    <a class="nav-link" href="{{ route('admin.reports') }}">
                        <i class="fas fa-chart-bar"></i>
                        <p> @lang('admin_messages.reports')  </p>
                    </a>
                </li>
                @endcheckPermission
                @checkPermission('*-transactions')
                <li class="nav-item {{ ($active_menu == 'transactions') ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('admin.transactions') }}">
                        <i class="fas fa-receipt"></i>
                        <p> @lang('admin_messages.transactions') </p>
                    </a>
                </li>
                @endcheckPermission
                @checkPermission('*-coupon_codes')
                <li class="nav-item {{ ($active_menu == 'coupon_codes')?'active':'' }}">
                    <a class="nav-link" href="{{ route('admin.coupon_codes') }}">
                        <i class="fas fa-money-check-alt"></i>
                        <p> @lang('admin_messages.coupon_codes') @lang('admin_messages.settings')  </p>
                    </a>
                </li>
                @endcheckPermission
                @checkPermission('*-countries')
                <li class="nav-item {{ ($active_menu == 'countries')?'active':'' }}">
                    <a class="nav-link" href="{{ route('admin.countries') }}">
                        <i class="fas fa-globe-asia"></i>
                        <p> @lang('admin_messages.countries')  </p>
                    </a>
                </li>
                @endcheckPermission
                @checkPermission('*-cities')
                <li class="nav-item {{ ($active_menu == 'cities')?'active':'' }}">
                    <a class="nav-link" href="{{ route('admin.cities') }}">
                        <i class="fas fa-globe-asia"></i>
                        <p> @lang('admin_messages.cities')  </p>
                    </a>
                </li>
                @endcheckPermission
                @checkPermission('*-currencies')
                <li class="nav-item {{ ($active_menu == 'currencies')?'active':'' }}">
                    <a class="nav-link" href="{{ route('admin.currencies') }}">
                        <i class="fas fa-dollar-sign"></i>
                        <p> @lang('admin_messages.currencies') </p>
                    </a>
                </li>
                @endcheckPermission
                @checkPermission('*-languages')
                <li class="nav-item {{ ($active_menu == 'languages')?'active':'' }}">
                    <a class="nav-link" href="{{ route('admin.languages') }}">
                        <i class="fas fa-language"></i>
                        <p> @lang('admin_messages.languages') </p>
                    </a>
                </li>
                @endcheckPermission
                @checkPermission('*-static_pages|*-static_page_header')
                <li class="nav-item {{ in_array($active_menu,['static_pages','static_page_header']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#staticPageDropdown" aria-expanded="{{ in_array($active_menu,['static_pages','static_page_header']) ? 'true':'false' }}">
                        <i class="fas fa-newspaper"></i>
                        <p> @lang('admin_messages.static_pages') </p>
                        <span class="caret"> </span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['static_pages','static_page_header']) ? 'show':'hide' }}" id="staticPageDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkPermission('*-static_page_header')
                            <li class="{{ ($active_menu == 'static_page_header') ? 'active':'' }}">
                                <a href="{{ route('admin.static_page_header') }}">
                                    <span class="sub-item"> @lang('admin_messages.static_page_header') </span>
                                </a>
                            </li>
                            @endcheckPermission
                            @checkPermission('*-static_pages')
                            <li class="{{ ($active_menu == 'static_pages') ? 'active':'' }}">
                                <a href="{{ route('admin.static_pages') }}">
                                    <span class="sub-item"> @lang('admin_messages.static_pages') </span>
                                </a>
                            </li>
                            @endcheckPermission
                        </ul>
                    </div>
                </li>
                @endcheckPermission
                @checkPermission('*-translations')
                <li class="nav-item {{ ($active_menu == 'translations')?'active':'' }}">
                    <a class="nav-link" href="{{ route('admin.translations') }}">
                        <i class="fa fa-language"></i>
                        <p> @lang('admin_messages.translations') </p>
                    </a>
                </li>
                @endcheckPermission
            </ul>
        </div>
    </div>
</div>