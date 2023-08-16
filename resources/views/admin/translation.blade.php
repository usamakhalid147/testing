@if($translatable_languages->count() > 1)
<div class="translation border-top p-4" v-cloak>
	<div class="translation-header">
		<div class="translation-title"> @lang('admin_messages.translations') </div>
	</div>
	<div class="translation-body pt-0">
		<input type="hidden" name="removed_translations" :value="removed_translations.toString()">
		<div class="translation-form my-3 border-top" v-for="(translation,index) in translations">
			<div class="d-flex align-items-end">
				<div class="form-group w-100">
					<label> @lang('admin_messages.language') </label>
					<select class="form-select" disabled>
						<option value=""> @lang('admin_messages.select') </option>
						@foreach($translatable_languages as $key => $value)
							<option value="{{ $key }}" :selected="'{{$key}}' == translation.locale"> {{ $value }} </option>
						@endforeach
					</select>
				</div>
				<div class="form-group ms-auto">
					<button type="button" class="btn btn-danger" v-on:click="removeTranslation(translation.locale,index);"> @lang('admin_messages.remove') </button>
				</div>
			</div>
			<div class="form-group" v-for="(field,parent_index) in translatable_fields" :class="{'required-input': translation[field.key] == '' || translation[field.key] == undefined}">
				<label> @{{ field.title }} <em class="text-danger" v-if="typeof field.rules == 'string' && field.rules.includes('required')">*</em> </label>
				<input type="text" :name="'translations['+translation.locale+']['+field.key+']'" v-model="translation[field.key]" class="form-control @{{ field.class }}" :id="field.id+'_'+parent_index+index" v-if="field.type == 'text'" >
				<textarea :name="'translations['+translation.locale+']['+field.key+']'" v-model="translation[field.key]" class="form-control" :class="field.class" :id="field.id+'_'+parent_index+index" v-if="field.type == 'textarea'"></textarea>
				<span class="text-danger"> @{{ (error_messages['translations.'+translation.locale+'.'+field.key]) ? error_messages['translations.'+translation.locale+'.'+field.key][0] : '' }} </span>
			</div>
		</div>
	</div>
	<div class="translation-footer d-flex align-items-center justify-content-end">
		<select class="form-select w-25" v-model="locale">
			<option value=""> @lang('admin_messages.select') </option>
			@foreach($translatable_languages as $key => $value)
				<option value="{{ $key }}" v-if="canDisplayLanguage('{{$key}}')"> {{ $value }} </option>
			@endforeach
		</select>
		<button type="button" class="btn btn-info text-white ms-2" :disabled="locale == ''" v-on:click="addNewTranslation(locale);"> @lang('admin_messages.add_translation') </button>
	</div>
</div>
@endif
@push('scripts')
<script type="text/javascript">
	window.translationInitData = {!! json_encode([
		'translatable_fields' => $translatable_fields,
		'translations' => formatTranslationData(old('translations',$translations ?? [])),
	]) !!};
</script>
@endpush