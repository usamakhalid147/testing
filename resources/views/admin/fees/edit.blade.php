@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.fees") </h4>
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
					<a href="javascript:;"> @lang("admin_messages.fees") </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('admin.fees.update'), 'class' => 'form-horizontal','id'=>'fees_form','method' => "PUT"]) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title">
							{{ $sub_title }}
							<p class="help-block"> @lang('admin_messages.fees_currency_desc') </p>
						</div>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label class="form-label d-block"> @lang('admin_messages.service_fee_type') </label>
							<div class="form-check-inline">
								<input class="form-check-input" type="radio" name="service_fee_type" id="fixed_service" value="fixed" v-model="service_fee_type">
								<label class="form-check-label ms-2" for="fixed_service">
									@lang('admin_messages.fixed')
								</label>
							</div>
							<div class="form-check-inline">
								<input class="form-check-input" type="radio" name="service_fee_type" id="percentage_service" value="percentage" v-model="service_fee_type">
								<label class="form-check-label ms-2" for="percentage_service">
									@lang('admin_messages.percentage')
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="service_fee"> @lang('admin_messages.service_fee') <em class="text-danger">*</em> </label>
							<div class="input-group">
								<div class="input-group-prepend" v-show="service_fee_type == 'fixed'">
									<span class="input-group-text"> {{ global_settings('default_currency') }} </span>
								</div>
								{!! Form::text('service_fee', old('service_fee',fees('service_fee')), ['class' => 'form-control', 'id' => 'service_fee']) !!}
								<div class="input-group-append" v-show="service_fee_type == 'percentage'">
									<span class="input-group-text"> % </span>
								</div>
							</div>
							<span class="text-danger">{{ $errors->first('service_fee') }}</span>
						</div>
						<div class="form-group">
							<label for="min_service_fee"> @lang('admin_messages.min_service_fee') <em class="text-danger">*</em> </label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"> {{ global_settings('default_currency') }} </span>
								</div>
								{!! Form::text('min_service_fee', old('min_service_fee',fees('min_service_fee')), ['class' => 'form-control', 'id' => 'min_service_fee']) !!}
							</div>
							<span class="text-danger">{{ $errors->first('min_service_fee') }}</span>
						</div>
						<div class="form-group">
							<label for="host_fee"> @lang('admin_messages.host_fee') <em class="text-danger">*</em> </label>
							<div class="input-group">
								{!! Form::text('host_fee', old('host_fee',fees('host_fee')), ['class' => 'form-control', 'id' => 'host_fee']) !!}
								<div class="input-group-append">
									<span class="input-group-text"> % </span>
								</div>
							</div>
							<span class="text-danger">{{ $errors->first('host_fee') }}</span>
						</div>
					</div>
					<div class="card-action">
						<a href="{{ route('admin.fees')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
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
	window.vueInitData = {!! json_encode([
		'service_fee_type' => old("service_fee_type",fees('service_fee_type')),
	]) !!}
</script>
@endpush