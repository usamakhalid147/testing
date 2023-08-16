<?php

/**
 * Service to CURD Array
 *
 * @package     Hyra
 * @subpackage  Services
 * @category    TranslationService
 * @author      Cron24 Technologies
 * @version     1.4
 * @link        https://cron24.com
 */

namespace App\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Auth;
use Lang;

class TranslationService
{
    function __construct(Filesystem $files)
    {
        $this->translations = new Collection;
        $this->translation_result = new Collection;
        $this->filePath = app()->make('path.lang');
        $this->files = $files;
        $this->locale = global_settings('default_language');
    }

    public function scan($data)
    {
        $this->is_select_all = $data['is_select_all'];
        $this->search_text = $data['search_text'];
        $this->file_name = $data['file'];
        $this->locale = $data['locale'];
        $this->translations = new Collection;

        $this->scanDirectory($this->filePath);

        return [
            'translations' => $this->translations,
            'translation_result' => $this->translation_result,
        ];

    }

    /**
     * Scan a directory.
     *
     * @param string $path to directory to scan
     */
    protected function scanDirectory($path)
    {
        foreach ($this->files->directories($path) as $directory) {
            $this->loadTranslationsInDirectory($directory, $this->getLocaleFromDirectory($directory));
        }
    }

    /**
     * Load all directory file translation (multiple group) into translations collection.
     *
     * @param $directory
     * @param $locale
     */
    private function loadTranslationsInDirectory($directory, $locale)
    {
        if ($this->search_text == '') {
            if (!$this->requestedLocale($this->locale)) {
                return;
            }
        }

        foreach ($this->files->files($directory) as $file) {
            $info = pathinfo($file);
            $group = $info['filename'];
            if ($group == $this->file_name) {
                if ($this->is_select_all) {
                    $this->loadAllTranslations($locale, $group);
                } else {
                    $this->loadTranslations($this->locale, $group);
                }
                if ($this->search_text != '' && !$this->is_select_all) {
                    $this->loadTranslationsBasedOnKey($locale, $group);
                }
            }
        }
    }

    /**
     * Load file translation (group) into translations collection.
     *
     * @param $locale
     * @param $group
     */
    public function loadTranslations($locale, $group)
    {
        $this->translations = Arr::dot(app()->translator->getLoader()->load($locale, $group));
    }

    /**
     * Load file translation (group) into translations collection.
     *
     * @param $locale
     * @param $group
     * @param $key
     */
    public function loadAllTranslations($locale, $group)
    {
        $translations = Arr::dot(app()->translator->getLoader()->load($locale, $group));

        if (!isset($this->translations[$group])) {
            $this->translations[$group] = collect();
        }

        $this->translations[$group]->put($locale,$translations);
    }

    /**
     * Load file translation (group) into translations collection.
     *
     * @param $locale
     * @param $group
     * @param $key
     */
    public function loadTranslationsBasedOnKey($locale, $group)
    {
        $translation_result[$locale] = Arr::dot(app()->translator->getLoader()->load($locale, $group));
        if (isset($this->translation_result[$locale])) {
            $translation_result[$locale] = collect();
        }
        $translation_result = $this->translation_result->put($group,$translation_result);
        $this->translation_result->put($locale,$translation_result[$group][$locale][$this->search_text] ?? '');
    }

    /**
     *  Determine if a found locale is requested for scanning.
     *  If $this->locale is not set, we assume that all the locales were requested.
     *
     * @param string $locale the locale to check
     * @return bool
     */
    private function requestedLocale($locale)
    {
        if (empty($this->locale)) {
            return true;
        }

        return $locale === $this->locale;
    }

    /**
     * Return locale from directory
     *  ie. resources/lang/en -> en.
     *
     * @param $directory
     * @return string
     */
    private function getLocaleFromDirectory($directory)
    {
        return basename($directory);
    }

    public function updateTranslation($file,$items)
    {
        $content = sprintf('<?php%s%sreturn %s;%s', PHP_EOL, PHP_EOL,self::varExport($items), PHP_EOL);

        if (!$this->files->isDirectory($dir = dirname($file))) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        $this->files->put($file, $content);
    }

    public static function varExport($var, $indent = '')
    {
        switch (gettype($var)) {
            case 'string':
                return '"'.addcslashes($var, "\\\$\"\r\n\t\v\f").'"';
            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        .($indexed ? '' : self::varExport($key).' => ')
                        .self::varExport($value, "$indent    ");
                }

                return "[\n".implode(",\n", $r)."\n".$indent.']';
            case 'boolean':
                return $var ? 'true' : 'false';
            default:
                return var_export($var, true);
        }
    }
}