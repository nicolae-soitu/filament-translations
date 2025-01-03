<?php

namespace TomatoPHP\FilamentTranslations\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use TomatoPHP\FilamentTranslations\Models\Translation;

class SaveScan
{
    private $paths;

    public function __construct()
    {
        $this->paths = config('filament-translations.paths');
    }

    public function save()
    {
        $scanner = app(Scan::class);
        collect($this->paths)->filter(function ($path) {
            return File::exists($path);
        })->each(function ($path) use ($scanner) {
            $scanner->addScannedPath($path);
        });
        

        [$trans, $__, $foundIn] = $scanner->getAllViewFilesWithTranslations();
        
        // /** @var Collection $trans */
        /** @var Collection $__ */
        DB::transaction(function () use ($trans, $__, $foundIn) {
            // Translation::query()
            //     ->whereNull('deleted_at')
            //     ->update([
            //         'deleted_at' => Carbon::now(),
            //     ]);
            
            $trans->each(function ($trans) use ($foundIn) {
                [$group, $key] = explode('.', $trans, 2);
                $namespaceAndGroup = explode('::', $group, 2);
                if (count($namespaceAndGroup) === 1) {
                    $namespace = '*';
                    $group = $namespaceAndGroup[0];
                } else {
                    [$namespace, $group] = $namespaceAndGroup;
                }
                // dd($foundIn[$trans]);
                $this->createOrUpdate($namespace, $group, $key, $trans, $foundIn[$trans]);
            });

            $__->each(function ($default) use ($foundIn) {
                
                $this->createOrUpdate('*', '*', $default, $default, $foundIn[$default]);
            });
        });
    }

    protected function createOrUpdate($namespace, $group, $key, $mainKey = null, $foundIn = []): void
    {
        /** @var Translation $translation */
        $translation = Translation::withTrashed()
            ->where('namespace', $namespace)
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        $defaultLocale = config('app.locale');
        // dd(array_intersect([1,2,3], [1,2,4]), array_intersect([1,2,5], [1,2,3]));
        $foundIn = array_values($foundIn);
        
        $source = $this ->getSource($foundIn);
        if ($translation) {
            // if (! $this->isCurrentTransForTranslationArray($translation, $defaultLocale)) {
            if($translation->deleted_at){
                $translation->restore();
            }
            if(count(array_intersect($foundIn, $translation->found_in??[])) != count($foundIn)){
                $translation->found_in = $foundIn;
            }
            if($translation->source != $source && $translation->source != 'manual'){
                $translation->source = $source;
            }
            if($translation->is_allowed == false){
                $translation->is_allowed = true;
            }
            if($translation->wasChanged()){
                $translation->save();
            }
            return;
        } 
        $locals = config('filament-translations.locals');
        $text = [];
        foreach ($locals as $locale => $lang) {
            $phrase = Lang::get(key: $key, fallback: str($key)->replace('.', ' ')->replace('_', ' ')->title()->toString(), locale: $locale);
            $text[$locale] = is_array($phrase) ? '' : $phrase;
        }
        $translation = Translation::query()->create([
            'namespace' => $namespace,
            'group' => $group,
            'key' => $key,
            'text' => $text,
            'found_in' => $foundIn,
            'source' => $source,
            'is_imported' => true,
            'is_allowed' => true,
        ]);
        if (! $this->isCurrentTransForTranslationArray($translation, $defaultLocale)) {
            $translation->save();
        }
    
    }
    private function getSource($paths){
        
        $values = array_map(fn($path) => current( explode('/',$path )), $paths);
        
        $values = array_values(array_unique($values));
        
        if(count($values) === 1){
            return $values[0];
        }
        return 'mix';
    }
    private function isCurrentTransForTranslationArray(Translation $translation, $locale): bool
    {
        if ($translation->group === '*') {
            return is_array(__($translation->key, [], $locale));
        }

        if ($translation->namespace === '*') {
            return is_array(trans($translation->group . '.' . $translation->key, [], $locale));
        }

        return is_array(trans($translation->namespace . '::' . $translation->group . '.' . $translation->key, [], $locale));
    }
}
