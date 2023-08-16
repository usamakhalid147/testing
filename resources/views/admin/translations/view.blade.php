@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner" v-cloak>
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.translations") </h4>
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
					<a href="javascript:;"> @lang("admin_messages.translations") </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header d-flex">
						<div class="card-title"> {{ $sub_title }} </div>
						<div class="ms-auto">
							<select class="form-select" name="file" id="file" v-model="file" v-on:change="getTranslationData()">
								<option value="admin_messages"> @lang('admin_messages.admin_messages') </option>
								<option value="messages"> @lang('admin_messages.messages') </option>
								<option value="experiences"> @lang('admin_messages.experiences') </option>
							</select>
						</div>
					</div>
					<div class="card-body">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive">
										<table id="translation-table" class="display table table-responsive">
											<thead>
												<tr>
													<th> @lang('admin_messages.language') </th>
													<th> @lang('admin_messages.trans_key') </th>
													<th> @lang('admin_messages.trans_value') </th>
													<th> @lang('admin_messages.action') </th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="(value,key) in translation_data">
													<td> @{{ language }} </td>
													<td> @{{ key }} </td>
													<td> @{{ value }} </td>
													<td> 
														<a href="javascript:void(0);" class="h3" v-on:click="getTranslationData(key)"> <i class="fas fa-edit"></i> </a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="TranslationModal" tabindex="-1" aria-labelledby="TranslationModal" aria-hidden="true">
				<div class="modal-dialog modal-lg modal-dialog-centered">
					<div class="modal-content">
					{!! Form::open(['url' => route('admin.update_translations'), 'class' => 'form-horizontal','method' => "POST"]) !!}
						<div class="modal-header justify-content-center border-bottom">
							<h3> @lang('admin_messages.translations') </h3>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body py-3">
							<div class="py-2 text-wrap table-responsive">
								<h4 class="text-black text-header"> @lang("admin_messages.trans_key") </h4>
								<input type="hidden" name="file" v-model="file">
								<input type="hidden" name="language" v-model="language">
								<input type="hidden" name="search_text" v-model="search_text">
								<input type="text" name="search_text" class="form-control mb-2" v-model="search_text" readonly>
								<div v-for="(translation,key) in translation_result" v-show="key != 'admin_messages' && key != 'messages' && key != 'experiences'">
									<div class="form-group input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text h-100">@{{ key }}</span>
									  	</div>
									  	<input type="text" :name="'translation_result['+key+']'" class="form-control" :id="key" :value="translation">
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-success">
								@lang('messages.submit')
							</button>
						</div>
					{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	window.addEventListener('load', () => {
		$('#translation-table').DataTable({});
	});
	window.vueInitData = {!! json_encode([
		'language' => global_settings('default_language'),
		'translation_data' => $translation_data,
	]) !!}
</script>
<script type="text/javascript" src="{{ asset('admin_assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin_assets/js/plugin/datatables/buttons.server-side.js') }}"></script>
@endpush