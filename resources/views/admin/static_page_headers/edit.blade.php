@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> {{ $main_title }} </h4>
			<ul class="breadcrumbs">
				<li class="nav-home">
					<a href="{{ route('admin.dashboard') }}">
						<i class="flaticon-home"></i>
					</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="{{ route('admin.static_page_header') }}">@lang("admin_messages.static_page_header")</a>
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
				@{{ error_messages['title.2'] }}
				{!! Form::open(['url' => route('admin.static_page_header.update'), 'class' => 'form-horizontal','method' => "PUT"]) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						<label for="title"> @lang('admin_messages.static_page_header_title') <em class="text-danger">*</em> </label>
						<div class="form-group" v-for="(header,key) in static_page_headers">
							<input type="text" name="title[]" class="form-control" v-model="header.display_name">
							<span class="text-danger">@{{ (error_messages['title.'+key]) ? error_messages['title.'+key][0] : '' }}</span>
						</div>
					</div>
					<div class="card-action">
						<a href="{{ route('admin.static_page_header')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
						<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	window.translationInitData = {!! json_encode([
		'static_page_headers' => $static_page_headers,
	]) !!};
</script>
@endpush