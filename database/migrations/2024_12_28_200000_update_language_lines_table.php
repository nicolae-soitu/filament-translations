<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('language_lines', function (Blueprint $table) {
            $table->boolean('is_imported')->default(false)->comment('Is imported from language files')->after('text');
            $table->boolean('is_allowed')->default(true)->comment('Allowd to use in the project')->after('text'); 
            $table->jsonb('found_in')->nullable()->comment('The location where the word was found')->after('text');
            $table->string('source')->default('manual')->comment('Source folder: app, lang, vendor, manual')->after('text');
            $table->index('source');
            $table->boolean('allow_automatical_translation')->default(true)->comment('Allow automatical translation with google translate or gpt')->after('text');
            $table->text('context_for_ai')->nullable()->comment('Context of the translation for AI')->after('text');
            $table->text('description')->nullable()->comment('Brief description of the phrase to help you understand where it is used or for what situations')->after('text');
        });
    }

    public function down()
    {
        Schema::table('language_lines', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'context_for_ai',
                'found_in',
                'allow_automatical_translation',
                'source',
                'is_allowed',
                'is_imported',
            ]);
        });
    }
};
