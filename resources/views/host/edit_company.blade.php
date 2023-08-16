@extends('layouts.hostLayout.app')
@section('content')
<div class="content">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title"> @lang("admin_messages.profile") </h4>
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
                    <a href="#">@lang("admin_messages.edit")</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                {!! Form::open(['url' => route('host.update_company',['id' => $result->id]), 'class' => 'form-horizontal','method' => "POST",'files' => true]) !!}
                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            <h2> @lang('admin_messages.edit_company')</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="company_name">@lang('admin_messages.company_name')</label>
                                {!! Form::text('company_name', old('company_name', $result->company_name), [
                                    'class' => 'form-control', 'id' => 'company_name', 
                                    'disabled' => (old('company_name', $result->company_name) !== '' && old('company_name', $result->company_name) !== null) ? 'disabled' : null
                                ]) !!}
                                <span class="text-danger">{{$errors->first('company_name')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="company_tax_number">@lang('admin_messages.company_tax_number')</label>
                                {!!Form::text('company_tax_number',$result->company_tax_number,[
                                    'class'=>'form-control','id'=>'company_tax_number',
                                    'disabled' => (old('company_tax_number', $result->company_tax_number) !== '' && old('company_tax_number', $result->company_tax_number) !== null) ? 'disabled' : null
                                    ])!!}
                                <span class="text-danger">{{$errors->first('company_tax_number')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="company_tele_phone_number">@lang('admin_messages.company_tele_phone_number')</label>
                                {!!Form::text('company_tele_phone_number',$result->company_tele_phone_number,[
                                    'class'=>'form-control','id'=>'company_tele_phone_number',
                                    'disabled' => (old('company_tele_phone_number', $result->company_tele_phone_number) !== '' && old('company_tele_phone_number', $result->company_tele_phone_number) !== null) ? 'disabled' : null
                                    ])!!}
                                <span class="text-danger">{{$errors->first('company_tele_phone_number')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="company_fax_number">@lang('admin_messages.company_fax_number')</label>
                                {!!Form::text('company_fax_number',$result->company_fax_number,[
                                    'class'=>'form-control','id'=>'company_fax_number',
                                    'disabled' => (old('company_fax_number', $result->company_fax_number) !== '' && old('company_fax_number', $result->company_fax_number) !== null) ? 'disabled' : null
                                    ])!!}
                                <span class="text-danger">{{$errors->first('company_fax_number')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="company_address">@lang('admin_messages.company_address')</label>
                                {!!Form::text('address_line_1',$result->address_line_1,[
                                    'class'=>'form-control','id'=>'address_line_1',
                                    'disabled' => (old('address_line_1', $result->address_line_1) !== '' && old('address_line_1', $result->address_line_1) !== null) ? 'disabled' : null
                                    ])!!}
                                <span class="text-danger">{{$errors->first('address_line_1')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="ward">@lang('admin_messages.company_ward')</label>
                                {!!Form::text('address_line_2',$result->address_line_2,['class'=>'form-control','id'=>'ward',
                                    'disabled' => (old('address_line_2', $result->address_line_2) !== '' && old('address_line_2', $result->address_line_2) !== null) ? 'disabled' : null
                                    ])!!}
                                <span class="text-danger">{{$errors->first('address_line_2')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="state">@lang('admin_messages.cities')</label>
                                {!!Form::text('state',$result->state,['class'=>'form-control','id'=>'state',
                                'disabled' => (old('state', $result->state) !== '' && old('state', $result->state) !== null) ? 'disabled' : null
                                ])!!}
                                <span class="text-danger">{{$errors->first('state')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="country_code"> @lang('admin_messages.company_country_code') </label>
                                {!! Form::select('country_code', $countries ,null, ['class' => 'form-select', 'id' => 'country_code','placeholder' => Lang::get('admin_messages.select'), 'v-model' => 'selected_country', 'v-on:change' => "selected_city = ''",
                                'disabled' => (old('country_code', $result->country_code) !== '' && old('country_code', $result->country_code) !== null) ? 'disabled' : null
                                    ]) !!}
                                <span class="text-danger">{{ $errors->first('country_code')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="city">@lang('admin_messages.province')</label>
                                <select name="company_city" class="form-control" v-model="selected_city" :disabled="isDisabled">
                                    <option value="">@lang('messages.select')</option>
                                    <option :value="city.name" v-for="city in cities" v-show="city.country == selected_country">@{{ city.name }}</option>
                                </select>
                                <span class="text-danger">{{ $errors->first('company_city') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="postal_code">@lang('admin_messages.postal_code')</label>
                                {!!Form::text('postal_code',$result->postal_code,[
                                'class'=>'form-control','id'=>'postal_code',
                                'disabled' => (old('postal_code', $result->postal_code) !== '' && old('postal_code', $result->postal_code) !== null) ? 'disabled' : null
                                ])!!}
                                <span class="text-danger">{{$errors->first('postal_code')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="company_website">@lang('admin_messages.company_website')</label>
                                {!!Form::text('company_website',$result->company_website,[
                                'class'=>'form-control','id'=>'company_website',
                                'disabled' => (old('company_website', $result->company_website) !== '' && old('company_website', $result->company_website) !== null) ? 'disabled' : null
                                ])!!}
                                <span class="text-danger">{{$errors->first('company_website')}}</span>
                            </div>
                            <div class="form-group">
                                <label for="company_email">@lang('admin_messages.company_email')</label>
                                {!!Form::text('company_email',$result->company_email,[
                                'class'=>'form-control','id'=>'company_email',
                                'disabled' => (old('company_email', $result->company_email) !== '' && old('company_email', $result->company_email) !== null) ? 'disabled' : null
                                ])!!}
                                <span class="text-danger">{{$errors->first('company_email')}}</span>
                            </div>
                            <div class="form-group input-file input-file-image">
                                <div class="img-button-div">
                                    <img class="img-upload-preview" src="{{ $result->logo_src ?? asset('images/preview_thumbnail.png') }}">
                                    @if($result->logo_src !== asset('images/preview_thumbnail.png'))
                                        <button id="remove-photo-btn" class="btn btn-danger btn-rounded mb-4" type="button">@lang('admin_messages.remove_photo')</button>
                                    @endif
                                </div>

                                <input type="file" class="form-control form-control-file" id="image" name="logo" accept="image/*" onchange="validateFileSize(this)">
                                <label for="image" class="label-input-file btn btn-default btn-round">
                                    <span class="btn-label"><i class="fa fa-file-image"></i></span>
                                    @lang('admin_messages.choose_file')
                                </label>
                                <span class="text-danger d-block">{{ $errors->first('logo') }}</span>
                            </div>
                            <div class="form-group mt-0 pt-0">
                                <small class="text-muted">Max upload size for Company Logo image is 1mb</small>
                            </div>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('host.edit_company')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
                            <button type="submit" class="btn btn-primary float-end" id="add"> @lang('admin_messages.submit') </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    window.vueInitData = {!! json_encode([
        'cities' => $city_list,
        'selected_country' => old('country_code',$result->country_code),
        'selected_city' => old('city',$result->city),
        'isDisabled' => old('city',$result->city) !== '',
    ]) !!};
    function validateFileSize(inputFile) {
        if (inputFile.files && inputFile.files[0]) {
            var fileSize = inputFile.files[0].size;
            if (fileSize > 1033414) { // 1MB in bytes
                flashMessage("File upload Size is greater than 1mb, try uploading a smaller image", 'danger');
                inputFile.value = ''; // clear the selected file
            }
        }
    }
    $(document).ready(function () {
        $('#remove-photo-btn').click(function() {
            var url = "{{ route('host.delete_company_image') }}";
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    flashMessage("Image Deleted Successfully", 'success');
                    location.reload(true);
                },
                error: function() {
                    alert('Unable to remove photo. Please try again later.');
                }
            });
        });
    });
</script>
@endpush