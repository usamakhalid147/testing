@extends('errors::illustrated-layout')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Unauthorized'))
@section('image')
	<div style="background-image: url({{ asset('/svg/401.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
	</div>
@endsection