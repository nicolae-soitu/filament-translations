<?php

namespace NicolaeSoitu\FilamentTranslations\Console;

use Illuminate\Console\Command;
use Tomatophp\ConsoleHelpers\Traits\RunCommand;

class FilamentTranslationsInstall extends Command
{
    use RunCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'filament-translations:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install package and publish assets';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Publish Vendor Assets');
        $this->artisanCommand(['migrate']);
        $this->info('Scanning for translations');
        $this->artisanCommand(['filament-translations:import']);
        $this->info('Filament Translations Manager installed successfully.');
    }
}
