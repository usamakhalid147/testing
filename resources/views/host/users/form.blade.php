@if($result->id != '')
<div class="form-group">
    <label for="agent_id"> @lang('admin_messages.agent_id') <em class="text-danger"> * </em> </label>
    {!! Form::text('agent_id', $result->id, ['class' => 'form-control', 'id' => 'agent_id','readonly' => true]) !!}
    <span class="text-danger">{{ $errors->first('agent_id') }}</span>
</div>
@endif
<div class="form-group">
    <label for="full_name"> @lang('admin_messages.full_name') <em class="text-danger"> * </em> </label>
    {!! Form::text('full_name', $result->full_name, ['class' => 'form-control', 'id' => 'full_name']) !!}
    <span class="text-danger">{{ $errors->first('full_name') }}</span>
</div>
<div class="form-group">
    <label for="host_roles"> @lang('admin_messages.host_roles') <em class="text-danger"> * </em> </label>
    {!! Form::select('role_id', $roles, $result->role_id, ['class' => 'form-select', 'id' => 'host_roles', 'placeholder' => Lang::get('admin_messages.select')]) !!}
    <span class="text-danger">{{ $errors->first('role_id') }}</span>
</div>
<div class="form-group">
    <label for="email"> @lang('admin_messages.agent') @lang('admin_messages.email') <em class="text-danger"> * </em> </label>
    {!! Form::text('email', $result->email, ['class' => 'form-control', 'id' => 'email']) !!}
    <span class="text-danger">{{ $errors->first('email') }}</span>
</div>
<div class="form-group">
    <label for="password"> @lang('admin_messages.password') <em class="text-danger"> * </em> </label>
    {!! Form::text('password', '', ['class' => 'form-control', 'id' => 'password']) !!}
    <span class="text-danger">{{ $errors->first('password') }}</span>
</div>
<div class="form-group">
    <label for="telephone_number"> @lang('admin_messages.telephone_number') </label>
    {!! Form::text('telephone_number', $result->telephone_number, ['class' => 'form-control', 'id' => 'telephone_number']) !!}
    <span class="text-danger">{{ $errors->first('telephone_number') }}</span>
</div>
<div class="form-group">
    <label for="phone_number"> @lang('admin_messages.agent') @lang('admin_messages.mobile_number') <em class="text-danger"> * </em> </label>
    {!! Form::text('phone_number',$result->phone_number, ['class' => 'form-control','id' => 'phone_number']) !!}
    <span class="text-danger">{{ $errors->first('phone_number') }}</span>
</div>
<div class="form-group">
    <label for="country_code"> @lang('admin_messages.country_code') <em class="text-danger"> *</em></label>
    {!! Form::select('country_code', $countries ,null, ['class' => 'form-select', 'id' => 'country_code','placeholder' => Lang::get('admin_messages.select'), 'v-model' => 'selected_country', 'v-on:change' => "selected_city = ''"]) !!}
    <span class="text-danger">{{ $errors->first('country_code')}}</span>
</div>
<div class="form-group">
    <label for="city"> @lang('messages.city') <em class="text-danger"> *</em></label>
    <select name="city" class="form-select" v-model="selected_city">
        <option value="">@lang('messages.select')</option>
        <option :value="city.name" v-for="city in cities" v-show="city.country == selected_country">@{{city.name}}</option>
    </select>
    <span class="text-danger">{{ $errors->first('city')}}</span>
</div>
<div class="form-group">
    <label for="dob"> @lang('admin_messages.dob')</label>
    {!! Form::text('dob', optional($result->user_information)->dob, ['class' => 'form-control', 'id' => 'dob']) !!}
    <span class="text-danger">{{ $errors->first('dob') }}</span>
</div>
<div class="form-group">
    <label for="gender"> @lang('admin_messages.gender')</label>
    {!! Form::select('gender', ["Male"=> Lang::get('messages.male'),"Female"=> Lang::get('messages.female'),"Other"=> Lang::get('messages.others')],optional($result->user_information)->gender, ['class' => 'form-select', 'id' => 'gender']) !!}
</div>
@if($result->id != '')
<div class="form-group input-file input-file-image">
    <div class="img-button-div">
        <img class="img-upload-preview" src="{{ $result->profile_picture_src }}">
        @if($result->src != '')
            <button id="remove-photo-btn" class="btn btn-danger btn-rounded mb-4" type="button" data-id="{{ $result->id }}">@lang('admin_messages.remove_photo')</button>
        @endif
    </div>
    <input type="file" class="form-control form-control-file" id="profile_picture" name="profile_picture" accept="image/*" onchange="validateFileSize(this)">
    <label for="profile_picture" class="label-input-file btn btn-default btn-round">
        <span class="btn-label"><i class="fa fa-file-image"></i></span>
        @lang('admin_messages.choose_file')
    </label>
    <span class="text-danger d-block">{{ $errors->first('image') }}</span>
</div>
@else
<div class="form-group input-file input-file-image">
    <img class="img-upload-preview">
    <input type="file" class="form-control form-control-file" id="profile_picture" name="profile_picture" accept="image/*" onchange="validateFileSize(this)">
    <label for="profile_picture" class="label-input-file btn btn-default btn-round">
        <span class="btn-label"><i class="fa fa-file-image"></i></span>
        @lang('admin_messages.choose_file')
    </label>
    <span class="text-danger d-block">{{ $errors->first('image') }}</span>
</div>
@endif
<div class="form-group mt-0 pt-0">
    <small class="text-muted">Max upload size for logo image is 1mb</small>
</div>
<div class="form-group">
    <label for="status"> @lang('admin_messages.status') <em class="text-danger"> * </em> </label>
    {!! Form::select('status', array('active' => 'Active', 'inactive' => 'Inactive'), $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get('admin_messages.select')]) !!}
    <span class="text-danger">{{ $errors->first('status') }}</span>
</div>
@push('scripts')
<script type="text/javascript">
    window.vueInitData = {!! json_encode([
        'verification_status' => old('verification_status',$result->verification_status),
        'cities' => $city_list,
        'selected_country' => old('country_code',$result->country_code ?? ''),
        'selected_city' => old('city',$result->city ?? ''),
    ]) !!}
    $(document).ready(function() {
        var flatpickrOptions = {
            altInput: true,
            maxDate: 'today',
            altFormat: flatpickrFormat,
            dateFormat: "Y-m-d"
        };

        flatpickr('#dob', flatpickrOptions);
    });
    function validateFileSize(inputFile) {
        if (inputFile.files && inputFile.files[0]) {
            var fileSize = inputFile.files[0].size;
            if (fileSize > 1033414) { // 5MB in bytes
                flashMessage("File upload Size is greater than 1mb, try uploading a smaller image", 'danger');
                inputFile.value = ''; // clear the selected file
            }
        }
    }
    // AJAX script to remove photo
    $(document).ready(function () {
        $('#remove-photo-btn').click(function() {
            var id = $(this).data('id');
            var url = "{{ route('host.delete_agent_profile_image', ['id' => ':id']) }}";
            url = url.replace(':id', id);
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