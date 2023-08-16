@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container mt-4 pt-4">
		<div class="row">
			<div class="col-12">
				{{--
				<h6 class="d-block">
				<a class="common-link" href="{{ resolveRoute('account_settings') }}"> @lang('messages.account_settings') </a>
				<i class="icon icon-arrow-right" area-hidden="true"></i>
				<a class="site-color" href="#"> @lang('messages.'.$page) </a>
				</h6>
				--}}
				<h3 class="text-dark-gray fw-bold"> @lang('messages.'.$page) </h3>
			</div>
		</div>
		@include('user.'.$page)
	</div>
</main>
@endsection
@push('scripts')
<script>
    window.vueInitData = {!! json_encode([
        'user'	=> $user,
        'currentPage'	=> $page,
        'original_user'	=> $user,
        'cities' => $city_list,
        'user_birthday' => [
        	'month' => $user->user_information->dob->format('n'),
	        'year' => $user->user_information->dob->format('Y'),
	        'day'	=> $user->user_information->dob->format('j'),
        ],
    ]) !!};
</script>
@endpush