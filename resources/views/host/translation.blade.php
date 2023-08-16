<div class="translation border-top border-bottom">
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
					<button type="button" class="btn btn-danger" v-on:click="removeTranslation(translation.locale,index);"> <span class="fas fa-trash-alt"></span></button>
				</div>
			</div>
			<div class="form-group" v-for="(field,parent_index) in translatable_fields">
				<label> @{{ field.title }} <em class="text-danger" v-if="field.rules == 'required'"> *</em></label>
				<input type="text" :name="'translations['+translation.locale+']['+field.key+']'" v-model="translation[field.key]" class="form-control @{{ field.class }}" :id="field.id+'_'+parent_index+index" v-if="field.type == 'text'" :placeholder="field.title">
				<textarea :name="'translations['+translation.locale+']['+field.key+']'" v-model="translation[field.key]" rows="3" class="form-control @{{ field.class }}" :id="field.id+'_'+parent_index+index" v-if="field.type == 'textarea'" :placeholder="field.title"></textarea>
				<span class="text-danger" v-if="error_messages['translations.'+translation.locale+'.'+field.key]"> @{{ error_messages['translations.'+translation.locale+'.'+field.key][0] }}</span>
			</div>
		</div>
	</div>
	<div class="translation-footer d-flex align-items-center justify-content-end" v-show="!translations.length == {{ $translatable_languages->count() - 1 }}">
		<select class="form-select w-25" v-model="locale">
			<option value=""> @lang('admin_messages.select') </option>
			@foreach($translatable_languages as $key => $value)
				<option value="{{ $key }}" v-if="canDisplayLanguage('{{$key}}')"> {{ $value }} </option>
			@endforeach
		</select>
		<button type="button" class="btn btn-info ms-2" :disabled="locale == ''" v-on:click="addNewTranslation(locale);"> @lang('admin_messages.add_translation') </button>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		'translatable_fields' => $translatable_fields ?? [],
		'removed_translations' => [],
		'translations' => formatTranslationData(old('translations',$translations)),
		'locale' => '',
		]);
	!!}
</script>
@endpush