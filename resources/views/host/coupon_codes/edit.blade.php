@extends('layouts.hostLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> {{ $main_title }} </h4>
			<ul class="breadcrumbs">
				<li class="nav-home">
					<a href="{{ route('host.dashboard') }}">
						<i class="flaticon-home"></i>
					</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="{{ route('host.'.$active_menu) }}"> @lang("admin_messages.".$active_menu) </a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.edit")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('host.'.$active_menu.'.update',['id' => $result->id]), 'class' => 'form-horizontal','method' => "PUT",'files' => true]) !!}
				@include('host.'.$active_menu.'.form')
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        var flatpickrOptions = {
            altInput: true,
            altFormat: flatpickrFormat,
            dateFormat: "Y-m-d",
        };

        flatpickr('#date', flatpickrOptions);
    });
</script>
@endpush