<footer class="px-5 bg-dark-teal pt-5 mb-5 mt-5 mb-md-0">
	<div class="col-12">
		<div class="row g-4 mb-3">
			<div class="col">
				<img src="{{ $site_logo }}" class="logo">
				<p class="mt-3 text-white">@lang('messages.footer_desc')</p>
				<div class="social-media-links mt-3 mb-3 mb-md-0">
					<div class="d-flex">
						@foreach(resolve("SocialMediaLink")->where('value','!=','') as $media)
						<div class="social-links">
							<a href="{{ $media->value }}" title="{{ ucfirst($media->name) }}" rel="noreferrer">
								<img class="img lazy-image-fadeIn" src="{{ asset('images/email/logo_'.$media->name.'.png') }}" alt="{{ ucfirst($media->name) }}">
							</a>
						</div>
						@endforeach
					</div>
					<div class="d-flex store-section connect-image-wrapper">
						<p class="text-white">@lang('messages.footer_desc_need_help') <a href="mailto:support@duhiviet.com">support@duhiviet.com</a></p>
					</div>
				</div>
			</div>
			@foreach($footer_sections as $name => $footer_section)
			<div class="col text-center">
				<h3 class="px-3 mb-4 text-white border-custom"> {{ $name }} </h3>
				<ul class="d-flex flex-column gap-2 mb-0 list-unstyled text-white">
					@if($loop->index == 1)
					@if(Auth::guard('host')->check())
					<li class="nav-item">
						<a href="{{ resolveRoute('host.hotels.create') }}" class="nav-link"> @lang('messages.list_your_property') </a>
					</li>
					@else
					<li class="nav-item">
						<a href="{{ resolveRoute('host.signup') }}" class="nav-link"> @lang('messages.become_partner') </a>
					</li>
					@endif
					@endif
					@if($loop->index == 2)
					<li class="nav-item">
						<a  href="{{url('https://help.duhiviet.com')}}" class="nav-link"> @lang('messages.help_center') </a>
					</li>
					@endif
					@foreach($footer_section as $section)
					<li class="nav-item">
						<a href="{{ $section->url }}" class="nav-link fw-normal"> {{ $section->name }} </a>
					</li>
					@endforeach
				</ul>
			</div>
			@endforeach
			<div class="col text-center">
				<h3 class="mb-4 text-white border-custom">Contact us</h3>
				<ul class="d-flex flex-column gap-2 mb-0 list-unstyled text-white fs-6">
					<li class="nav-item">
						<p class="mb-0 fw-normal">Du Hi Viet Company Limited</p>
					</li>
					<li class="nav-item">
						<p class="mb-0 fw-normal"> Tax Code: 0402024321</p>
					</li>
					<li class="nav-item">
						<p class="mb-0 fw-normal">16 An Nhon 3, An Hai Bac Ward,</p>
					</li>
					<li class="nav-item">
						<p class="mb-0 fw-normal">Son Tra District,</p>
					</li>
					<li class="nav-item">
						<p class="mb-0 fw-normal">Danang City - Vietnam</p>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- Copyright -->
	<div class="footer-copyright py-3 d-flex justify-content-center flex-wrap text-white align-items-center px-5">
		<div class="col-12 order-2 order-md-1 text-center">
			<a
				href="{{ global_settings('copyright_link') }}"
				class="mb-0 mt-2 mt-0"
				style="font-size: 12px;"
			>
				{!! global_settings('copyright_text') !!}
			</a>
		</div>
	</div>
	<!-- Copyright -->
</footer>

<div class="responsive-footer fixed-bottom bg-white px-3 py-2 text-center d-md-none">
    <div class="row">
    	@guest
        <div class="col logged-links {{ (Route::currentRouteName() == 'home') ? 'active' : '' }}">
            <a href="{{ resolveRoute('home') }}" class="small fw-bold text-decoration-none {{ (Route::currentRouteName() == 'home') ? 'text-grey' : 'text-grey' }}">
            	<p class="h4 m-0"> <i class="icon  icon-home" aria-hidden="true"></i> </p>
                @lang('messages.home')
            </a>
        </div>
        <div class="col logged-links {{ (Route::currentRouteName() == 'wishlists') ? 'active' : '' }}">
            <a href="{{ resolveRoute('wishlists') }}" class="small fw-bold text-decoration-none {{ (Route::currentRouteName() == 'wishlists') ? 'text-grey' : 'text-grey' }}">
                <p class="h4 m-0"> <i class="icon  icon-wishlist" aria-hidden="true"></i> </p>
                @lang('messages.saved')
            </a>
        </div>
        <div class="col logged-links {{ (Route::currentRouteName() == 'profile') ? 'active' : '' }}">
            <a class=" user-profile-link" href="#" class="small fw-bold text-decoration-none {{ (Route::currentRouteName() == 'profile') ? 'text-grey' : 'text-grey' }}">
                <p class="h4 m-0">
                    <i class="icon  icon-outlined icon-profile" aria-hidden="true"></i>
                </p>
                @lang('messages.profile')
            </a>
        </div>
        @else
        <div class="col logged-links {{ (Route::currentRouteName() == 'home') ? 'active' : '' }}">
            <a href="{{ resolveRoute('home') }}" class="small fw-bold text-decoration-none {{ (Route::currentRouteName() == 'home') ? 'text-grey' : 'text-grey' }}">
                <p class="h4 m-0">
                    <i class="icon  icon-home" aria-hidden="true"></i>
                </p>
                @lang('messages.home')
            </a>
        </div>
        <div class="col logged-links {{ (Route::currentRouteName() == 'wishlists') ? 'active' : '' }}">
            <a href="{{ resolveRoute('wishlists') }}" class="small fw-bold text-decoration-none {{ (Route::currentRouteName() == 'wishlists') ? 'text-grey' : 'text-grey' }}">
                <p class="h4 m-0">
                    <i class="icon  icon-wishlist" aria-hidden="true"></i>
                </p>
                @lang('messages.saved')
            </a>
        </div>
        <div class="col logged-links {{ (Route::currentRouteName() == 'trips') ? 'active' : '' }}">
            <a href="{{ resolveRoute('reservations') }}" class="small fw-bold text-decoration-none {{ (Route::currentRouteName() == 'trips') ? 'text-grey' : 'text-grey' }}">
                <p class="h4 m-0">
                    <i class="icon  icon-trips" aria-hidden="true"></i>
                </p>
                @lang('messages.trips')
            </a>
        </div>
        <div class="col logged-links {{ (Route::currentRouteName() == 'inbox') ? 'active' : '' }}">
            <a href="{{ resolveRoute('inbox') }}" class="small fw-bold text-decoration-none {{ (Route::currentRouteName() == 'inbox') ? 'text-grey' : 'text-grey' }}">
                <p class="h4 m-0 position-relative">
                    <i class="icon  icon-outlined icon-inbox" aria-hidden="true"></i>
                	<span class="badge message-count" v-if="inbox_count > 0"> @{{ inbox_count }} </span>
                </p>
                @lang('messages.inbox')
            </a>
        </div>
        <div class="col logged-links {{ (Route::currentRouteName() == 'profile') ? 'active' : '' }}">
            <a class=" user-profile-link" href="#" class="small fw-bold text-decoration-none {{ (Route::currentRouteName() == 'profile') ? 'text-grey' : 'text-grey' }}">
                <p class="h4 m-0">
                    <i class="icon  icon-outlined icon-profile" aria-hidden="true"></i>
                </p>
                @lang('messages.profile')
            </a>
        </div>
        @endguest
    </div>
</div>