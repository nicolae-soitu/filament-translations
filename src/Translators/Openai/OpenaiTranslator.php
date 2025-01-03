<?php

namespace NicolaeSoitu\FilamentTranslations\Translators\Openai;

use NicolaeSoitu\FilamentTranslations\Translators\TranslatorInterface;
use NicolaeSoitu\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationHeaderActions;
use NicolaeSoitu\FilamentTranslations\Filament\Resources\TranslationResource\Actions\ManagePageActions;
use NicolaeSoitu\FilamentTranslations\Models\Translation;
use NicolaeSoitu\FilamentTranslations\Jobs\TranslateJob;
use NicolaeSoitu\FilamentTranslations\Translators\TranslatorAbstract;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Collection;
use NicolaeSoitu\FilamentTranslations\Translators\Openai\OpenaiTranslateAction;

class OpenaiTranslator extends TranslatorAbstract implements TranslatorInterface
{
    public static function make()
    {
        // dd('OpenaiTranslator');
        ManagePageActions::register(OpenaiTranslateAction::make());
    }

    public static function dispatchAll($data)
    {
        $translation = Translation::select('id','namespace', 'group', 'key', 'text','context_for_ai')
            ->where('is_allowed', true)
            ->where('allow_automatical_translation', true)
            ->chunk(config('filament-translations.translators.openai.chunk_size', 50), function($rows) use ($data) {
                TranslateJob::dispatch($rows, $data, self::class);
            });
    }
    
    function request($phrases, $data)
    {
        $json = json_encode($phrases);
        $prompt = config('filament-translations.translators.openai.user_prompt', 'Translate the following json object from :from to :language, ensuring you return only the translated content in JSON format without added quotes or any other extraneous details and dont change the keys. Importantly, any word prefixed with the symbol ":" should remain unchanged and should not be translated the key "context" should be used to understand the meaning of the phrase');
        $prompt = str_replace(':language', $data['to'] . " (" . $this->getLocals($data['to']) . ")", $prompt);
        $prompt = str_replace(':from', $data['from'] . " (" . $this->getLocals($data['from']) . ")", $prompt);
        // dump($prompt);
        $result = OpenAI::chat()->create([
            'model' => config('filament-translations.translators.openai.model','gpt-3.5-turbo'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => config('filament-translations.translators.openai.system_prompt','You are a translator. Your job is to translate the following json object to the language specified in the prompt.'),
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
                [
                    'role' => 'user',
                    'content' => $json,
                ],
            ],
            'temperature' => 0.4,
            'n' => 1,
        ]);
        $translationArray = [];
        if ($result->choices && count($result->choices) > 0 && $result->choices[0]->message) {
            $translationArray = json_decode($result->choices[0]->message->content, 1) ?? [];
        }
        return $translationArray;
    }
    function translatePhrases($phrases, $data)
    {
        $makeJsonArray = [];
        foreach ($phrases as $key => $phrase) {
            $isKeySent[$key] = $phrase['isKey'];
            $context = null;
            if(!empty($phrase['context_for_ai'])){
                $context = $phrase['context_for_ai'];
            }
            if(is_null($context)){
                $forTranslate = $phrase['phrase'];
            } else {
                $forTranslate = ['phrase'=>$phrase['phrase'], 'context'=>$context];
            }
            $makeJsonArray[$key] = $forTranslate;
        }
        // dump($makeJsonArray);
        // return;
        $translationArray = $this->request($makeJsonArray, $data);
        // dump($translationArray);
        foreach($translationArray as $key => $value){
            $translationModel = Translation::query()->where('key', $key)->first();
            if ($translationModel && isset($makeJsonArray[$key])) {
                try{
                    $translationModel->setTranslation($data['to'], is_string($value) ? $value : $value['phrase']);
                }catch(\Throwable $e){
                    dump($e->getMessage(),  [$key => $value]);
                }
                if($isKeySent[$key]){
                    $translationModel->need_atention = true;
                }
                $translationModel->save();
            }
        }
        return $translationArray;
    }
    function translateBulk($translations, $data)
    {
        // dd($data);
        $phrases = $this->getWordsForTranslation($translations, $data);
        if(count($phrases) == 0){
            return [];
        }
        return $this->translatePhrases($phrases, $data);
    }
}
