<?php

/**
 * Trait for Translatable
 *
 * @package     HyraHotel
 * @subpackage  Traits
 * @category    Translatable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Traits;

trait Translatable
{
	/**
    * Get Validation Rules and attributes based on current model fields
    *
    * @param Array $locales
    * @return Array $rules and $attributes
    */
	public function getTranslationValidation($locales)
	{
		$rules = $attributes = [];

		foreach ($locales as $locale) {
			foreach ($this->translatable_fields as $key => $field) {
				if(isset($field['rules']) && $field['rules'] != '') {
					$rules['translations.'.$locale.'.'.$field['key']] = $field['rules'];
					$attributes['translations.'.$locale.'.'.$field['key']] = $field['title'];
				}
			}
		}

		return compact('rules','attributes');
	}

	/**
    * Get All The Translations except Default Language
    *
    */
	public function getTranslationsExceptDefault($model)
	{
		$formatted_translations = [];
		$translations = $model->getTranslations();
		foreach($translations as $key => $translation) {
			foreach($translation as $locale => $value) {
				$formatted_translations[$locale][$key] = $value;
			}
		}
		unset($formatted_translations[global_settings('default_language')]);
		return $formatted_translations;
	}

	/**
    * Create or Update All The Translation to Database
    *
    */
	public function updateTranslation($model, $translations)
	{
		if(count($translations)) {
			foreach($translations as $locale => $translation) {
				$model->setLocale($locale);
				foreach($translation as $key => $value) {
					$model->$key = $value;
				}
				$model->save();
			}
		}
	}

	/**
    * delete Translations for given locales
    *
    */
	public function deleteTranslations($model, $locales)
	{
		$locales = array_filter(explode(',',$locales));
		if(count($locales)) {
			foreach($locales as $locale) {
				$model->setLocale($locale);
				foreach($model->translatable as $field) {
					// $model->forgetTranslation($field, $locale);
					$model->$field = null;
				}
				$model->save();
			}
		}
	}
}