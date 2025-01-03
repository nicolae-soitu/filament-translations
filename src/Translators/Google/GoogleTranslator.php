<?php

namespace NicolaeSoitu\FilamentTranslations\Translators\Google; 

use NicolaeSoitu\FilamentTranslations\Translators\TranslatorInterface;
use NicolaeSoitu\FilamentTranslations\Translators\Google\GoogleTranslateAction;
use NicolaeSoitu\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationHeaderActions;
use NicolaeSoitu\FilamentTranslations\Filament\Resources\TranslationResource\Actions\ManagePageActions;
use NicolaeSoitu\FilamentTranslations\Jobs\TranslateJob;
use NicolaeSoitu\FilamentTranslations\Models\Translation;
use Stichoza\GoogleTranslate\GoogleTranslate;
use NicolaeSoitu\FilamentTranslations\Translators\TranslatorAbstract;
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
