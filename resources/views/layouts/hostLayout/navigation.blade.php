<div class="sidebar sidebar-style-2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-start mt-2">
                    <img src="{{ getCurrentUser()->profile_picture_src }}" class="avatar-img rounded-circle">
                </div>
                <div class="info">
                    <a class="nav-link" data-bs-toggle="collapse" href="#adminInformation" aria-expanded="true">
                        <span class="px-2">
                            <span class="primary">{{ getCurrentUser()->full_name }}</span>
                            <span class="user-level info text-truncate">{{ getCurrentUser()->email }}</span>
                            <span class="caret"></span>
                        </span>
                    </a>
                    <div class="clearfix"></div>
                    <div class="collapse in" id="adminInformation">
                        <ul class="nav">
                            @checkHostPermission('update-edit_profile')
                            <li>
                                <a class="nav-link" href="{{ route('host.edit') }}">
                                    <span class="link-collapse"> @lang('messages.edit_manager') </span>
                                </a>
                            </li>
                            @endcheckHostPermission
                            <li>
                                <a class="nav-link" href="{{ route('host.logout') }}">
                                    <span class="link-collapse text-danger"> @lang('admin_messages.logout') </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <ul class="nav nav-info" id="nav-primary">
                <li class="nav-item {{ in_array($active_menu,['dashboard']) ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('host.dashboard') }}">
                        <i class="la flaticon-analytics"></i>
                        <p> @lang('admin_messages.dashboard') </p>
                    </a>
                </li>
                @checkHostPermission('*-edit_profile')
                <li class="nav-item {{ in_array($active_menu,['edit_profile']) ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('host.edit') }}">
                        <i class="fas fa-user-edit"></i>
                        <p> @lang('admin_messages.management_profile') </p>
                    </a>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-edit_company')
                <li class="nav-item {{ in_array($active_menu,['edit_company']) ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('host.edit_company') }}">
                        <i class="fas fa-user-edit"></i>
                        <p> @lang('admin_messages.company_profile') </p>
                    </a>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-host_users|*-host_roles')
                <li class="nav-item {{ in_array($active_menu,['host_users','roles_privilege']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#manageUserDropdown" aria-expanded="{{ in_array($active_menu,['host_users','roles_privilege']) ? 'true':'false' }}">
                        <i class="fas fa-users-cog"></i>
                        <p> @lang('admin_messages.manage_agents') </p>
                        <span class="caret"> </span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['host_users','roles_privilege']) ? 'show':'hide' }}" id="manageUserDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkHostPermission('*-host_users')
                            <li class="{{ ($active_menu == 'host_users') ? 'active':'' }}">
                                <a class="nav-link" href="{{ route('host.users') }}">
                                    <span class="sub-item"> @lang('admin_messages.agents') </span>
                                </a>
                            </li>
                            @endcheckHostPermission
                            @checkHostPermission('*-host_roles')
                            <li class="{{ ($active_menu == 'roles_privilege') ? 'active':'' }}">
                                <a class="nav-link" href="{{ route('host.roles') }}">
                                    <span class="sub-item"> @lang('admin_messages.roles_privilege') </span>
                                </a>
                            </li>
                            @endcheckHostPermission
                        </ul>
                    </div>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-host_hotels')
                <li class="nav-item {{ in_array($active_menu,['hotels']) ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('host.hotels') }}">
                        <i class="fas fa-hotel"></i>
                        <p> @lang('admin_messages.hotels') </p>
                    </a>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-host_rooms')
                <li class="nav-item {{ in_array($active_menu,['rooms']) ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('host.rooms') }}">
                        <i class="fas fa-bed"></i>
                        <p> @lang('admin_messages.room_management') </p>
                    </a>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-host_reservations|*-host_payouts')
                <li class="nav-item {{ in_array($active_menu,['reservations','payouts']) ? 'active':'' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#bookingDropdown" aria-expanded="{{ in_array($active_menu,['reservations','payouts']) ? 'true':'false' }}">
                        <i class="fas fa-handshake"></i>
                        <p> @lang('admin_messages.reservation_management') </p>
                        <span class="caret"> </span>
                    </a>
                    <div class="collapse {{ in_array($active_menu,['reservations','payouts']) ? 'show':'hide' }}" id="bookingDropdown" data-bs-parent="#nav-primary">
                        <ul class="nav nav-collapse">
                            @checkHostPermission('*-host_reservations')
                            <li class="{{ ($active_menu == 'reservations') ? 'active':'' }}">
                                <a class="nav-link" href="{{ route('host.reservations',['type' => 'all']) }}">
                                    <span class="sub-item"> @lang('admin_messages.reservation') </span>
                                </a>
                            </li>
                            @endcheckHostPermission
                            @checkHostPermission('*-host_payouts')
                            <li class="{{ ($active_menu == 'payouts') ? 'active':'' }}">
                                <a class="nav-link" href="{{ route('host.payouts',['type' => 'future']) }}">
                                    <span class="sub-item"> @lang('admin_messages.payouts') </span>
                                </a>
                            </li>
                            @endcheckHostPermission
                        </ul>
                    </div>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-inbox')
                <li class="nav-item {{ in_array($active_menu,['messages']) ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('host.messages',['type' => 'current']) }}">
                        <i class="fas fa-comments"></i>
                        <p> @lang('admin_messages.messages') </p>
                        @if($messages_count)
                        <span class="badge badge-primary">{{ $messages_count }}</span>
                        @endif
                    </a>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-host_reviews')
                <li class="nav-item {{ in_array($active_menu,['reviews']) ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('host.reviews',['type' => 'current']) }}">
                        <i class="fas fa-trophy"></i>
                        <p> @lang('admin_messages.reviews') </p>
                        @if($reviews_count)
                        <span class="badge badge-primary">{{ $reviews_count }}</span>
                        @endif
                    </a>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-host_coupon_codes')
                <li class="nav-item {{ in_array($active_menu,['coupon_codes']) ? 'active':'' }}">
                    <a class="nav-link" href="{{ route('host.coupon_codes') }}">
                        <i class="fas fa-money-check-alt"></i>
                        <p> @lang('admin_messages.coupon_codes') </p>
                    </a>
                </li>
                @endcheckHostPermission
                @checkHostPermission('*-host_reports')
                <li class="nav-item {{ ($active_menu == 'reports')?'active':'' }}">
                    <a class="nav-link" href="{{ route('host.reports') }}">
                        <i class="fas fa-chart-bar"></i>
                        <p> @lang('admin_messages.reports')  </p>
                    </a>
                </li>
                @endcheckHostPermission
            </ul>
        </div>
    </div>
</div>