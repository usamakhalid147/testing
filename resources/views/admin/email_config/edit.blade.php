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
					<a href="javascript:;"> @lang("admin_messages.email_configurations") </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-2 m-2">
									<ul class="nav nav-pills nav-pills-icons flex-column navigation-links" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" href="#default" role="tab" data-bs-toggle="tab">
												Default
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#member" role="tab" data-bs-toggle="tab">
												Member
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#hotelier" role="tab" data-bs-toggle="tab">
												Hotelier
											</a>
										</li>
									</ul>
								</div>
								<div class="col-md-8">
										<div class="tab-content">
											<div class="tab-pane active" id="default">
												{!! Form::open(['url' => route('admin.email_configurations.update'), 'class' => 'form-horizontal','method' => "PUT"]) !!}
													@include('admin.email_config.default_email_form_fields', ['site_value' => 'EmailConfig'])
												{!! Form::close() !!}
											</div>
											<div class="tab-pane" id="member">
												{!! Form::open(['url' => route('admin.email_configurations.update'), 'class' => 'form-horizontal','method' => "PUT"]) !!}
													@include('admin.email_config.member_email_form_fields', ['site_value' => 'MemberEmailConfig'])
												{!! Form::close() !!}
											</div>
											<div class="tab-pane" id="hotelier">
												{!! Form::open(['url' => route('admin.email_configurations.update'), 'class' => 'form-horizontal','method' => "PUT"]) !!}
													@include('admin.email_config.hotelier_email_form_fields', ['site_value' => 'HotelierEmailConfig'])
												{!! Form::close() !!}
											</div>
										</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
