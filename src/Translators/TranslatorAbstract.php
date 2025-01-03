<?php

namespace TomatoPHP\FilamentTranslations\Translators;

use Illuminate\Support\Collection;
use TomatoPHP\FilamentTranslations\Jobs\TranslateJob;    

abstract class TranslatorAbstract implements TranslatorInterface
{
    public static function dispatch($records, array $data)
    {
        // dd($records, $data, static::class);
        TranslateJob::dispatch($records, $data, static::class);
    }
    public function getPhraseAndLanguageForTranslation( $from, $to, $translation, $translateKey)
    {
        $phrase = $translation->text[$from] ?? '';
        $isKey = false;
        if(empty($phrase)){
            $defaultLocale = config('filament-translations.default_locale_for_translation');
            if(isset($translation->text[$defaultLocale])){
                $from = $defaultLocale;
                $phrase = $translation->text[$defaultLocale];
            }elseif(isset($translation->text['en'])){
                $from = 'en';
                $phrase = $translation->text['en'];
            }else{
                $phrase = $translation['key'];
                $isKey = true;
            }
        }
        // dump($from);
        if($translateKey && empty($phrase)){
            $phrase = $translation->key;
            $isKey = true;
        }
        if( $this->isKey($translation->key, $phrase)){
            $isKey = true;
        }
        if($isKey){ 
            $phrase = str_replace('.', ' ', $phrase);
            $phrase = str_replace('_', ' ', $phrase);
            $phrase = ucfirst($phrase);
        }
        return [
            'phrase'=>$phrase, 
            'from'=>$from,
            'to'=>$to,
            'isKey'=>$isKey,
            'context_for_ai'=>$translation->context_for_ai
        ];
    }
    function isKey($key, $phrase){
        return strpos($phrase, ' ') === false && strpos($phrase, '.') !== false || $key == $phrase;
    }
    function isEmpty($translation, $intoLanguage){
        $phrase = $translation -> getTextByLocale($intoLanguage);
        return empty($phrase) || $this->isKey($translation->key,$phrase) ;
    }
    function getLocals($shortLocale = null){
        $locals = [];
        foreach(config('filament-translations.locals') as $key => $local){
            $locals[$key] = $local['label'];
        }
        if($shortLocale){
            return $locals[$shortLocale]??($locals['en']??'en');
        }
        return $locals;
    }
    function getWordsForTranslation($translations, $data){
        $makeJsonArray = [];
        // $isKeySent = [];
        $phrases = [];
        $from = $data['from'];
        foreach ($translations as $translation) {
            if($translation->allow_automatical_translation == false){  
                continue;
            }
            if($data['witch_words'] == 'only_missing' && !$this->isEmpty($translation, $data['to'])){
                continue;
            }
            $settings = $this->getPhraseAndLanguageForTranslation($data['from'], $data['to'], $translation, ($data['translate_key'] == 'yes'));
            if($data['translate_key'] == 'no' && $settings['isKey']){
                continue;
            }
            if(empty($settings['phrase'])){
                continue;
            }
            
            $phrases[$translation->key] = $settings;
        }
        return $phrases;
    }
}
