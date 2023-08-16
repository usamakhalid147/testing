@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="row mt-md-5 mt-sm-2">
		<div class="offset-md-1 col-md-5 col-middle">
			<h1 class="text-jumbo"> @lang('messages.oops') </h1>
			<h2> @lang('messages.we_cant_seem_to_find_page') </h2>
			<h6 class="my-2"> @lang('messages.error_code'): 404</h6>
			<ul class="list-unstyled my-3">
				<li> 
					@lang('messages.here_are_some_helpful_links'):
				</li>
				<li>
					<a href="{{ resolveRoute('home') }}"> @lang('messages.home') </a>
				</li>
				<li>
					<a href="{{ resolveRoute('search') }}"> @lang('messages.search') </a>
				</li>
				<li>
					<a href="{{ resolveRoute('help') }}"> @lang('messages.help') </a>
				</li>
				<li>
					<a href="{{ resolveRoute('blog') }}"> @lang('messages.blogs') </a>
				</li>
				<li>
					<a href="{{ resolveRoute('contact_us') }}"> @lang('messages.contact_us') </a>
				</li>
			</ul>
		</div>
		<div class="col-md-5 d-none d-md-block">
			<img src="{{ asset('svg/404.gif') }}" class="img img-full_screen" alt="Lost in the desert">
		</div>
	</div>
</main>
@endsection
@push('scripts')
<style type="text/css">
	.main-container {
		padding-top: 0px;
	}
	.text-jumbo {
		font-size: 8rem;
		font-weight: 600;
	}
	.img-full_screen {
		height: 100%;
		width: 100%;
	}
</style>
@endpush