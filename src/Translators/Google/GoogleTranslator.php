<?php

namespace TomatoPHP\FilamentTranslations\Translators\Google; 

use TomatoPHP\FilamentTranslations\Translators\TranslatorInterface;
use TomatoPHP\FilamentTranslations\Translators\Google\GoogleTranslateAction;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationHeaderActions;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Actions\ManagePageActions;
use TomatoPHP\FilamentTranslations\Jobs\TranslateJob;
use TomatoPHP\FilamentTranslations\Models\Translation;
use Stichoza\GoogleTranslate\GoogleTranslate;
use TomatoPHP\FilamentTranslations\Translators\TranslatorAbstract;
class GoogleTranslator extends TranslatorAbstract implements TranslatorInterface
{
    public static function make()
    {
        // dd('GoogleTranslator');
        ManagePageActions::register(GoogleTranslateAction::make());
    }

    public static function dispatchAll($data)
    {

        $translation = Translation::select('id','namespace', 'group', 'key', 'text')->where('is_allowed', true)
            ->where('allow_automatical_translation', true)
            ->chunk(config('filament-translations.translators.google.chunk_size', 100), function($rows) use ($data) {
                TranslateJob::dispatch($rows, $data, self::class);
            });
            
    }
    function translateBulk($translations, $data)
    {
        $phrases = $this->getWordsForTranslation($translations, $data);
        if(count($phrases) == 0){
            return [];
        }
        return $this->translatePhrases($phrases, $data);
    }
    function translatePhrases($phrases, $data)
    {
        // dump($phrases);
        // dump($data);
        $translator = new GoogleTranslate($data['to']);
        $translationArray = [];
        foreach ($phrases as $key => $phrase){
            $translationModel = Translation::query()->where('key', $key)->first();
            if($translationModel){  
                $translator->setSource($phrase['from']);
                $translationArray[$key] = $translator->translate($phrase['phrase']);
                $translationModel->setTranslation($data['to'], $translationArray[$key]);
                if($phrase['isKey']){
                    $translationModel->need_atention = true;
                }
                $translationModel->save();
            }       
        }
        return $translationArray;
    }

}
