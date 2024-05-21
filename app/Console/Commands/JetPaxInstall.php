<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class JetPaxInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install jetpax admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    private bool $canRun = false;

    private array $bag = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('iotron Admin Installer');
        // waiting for 2 seconds
        $this->warn('iotron: Please wait...');
        sleep(2);

        $this->canRun();

        if ($this->canRun) {
            $this->warn('iotron: Preparation Complete...');
            $this->info('iotron: Starting...');

            $this->setAppKey();
            $this->migrateTable();
            $this->seedTable();
            $this->storageLink();

            // $this->resetCache();
            // $this->composerDumpAutoload();

            // waiting for 2 seconds
            $this->warn('iotron: Please wait...');
            sleep(2);
            $this->warn('iotron: Finishing Installation...');
            sleep(2);
            $this->info('-------------------------------');
            $this->info('Congratulations!');
            $this->info('The installation has been finished and you can now use '.config('app.name'));
            $this->line('Made With <3 @iotron');
        }

        return 1;
    }

    private function canRun()
    {
        if ($this->confirm('iotron: Do you wish to continue?', true)) {
            $this->canRun = true;
            // waiting for 2 seconds
            $this->warn('iotron: Please wait...');
            sleep(2);
            if (App::version() > 10) {
                $this->bag[] = ['Framework', 'Pass ['.App::version().']', 'OK'];
            } else {
                $this->canRun = false;
                $this->bag[] = ['Framework', 'Fail', 'Minimum v.9 And Currently have :'.App::version()];
            }
            if (DB::connection()->getDatabaseName()) {
                $this->bag[] = ['Database', 'Active ['.DB::connection()->getDatabaseName().']', 'OK'];
            } else {
                $this->canRun = false;
                $this->bag[] = ['Database', 'InActive', 'Check Your DB Credentials'];
            }

            //Display Table
            $this->table(
                ['Attribute', 'Status', 'Recommendation'],
                $this->bag
            );
        } else {
            $this->error('iotron: Please Correct The App Version And Database Credentials for installation...');
        }
    }

    private function setAppKey()
    {
        $this->warn('iotron: Please wait...');
        $result = $this->call('key:generate');
        $this->info((string) $result);
    }

    private function migrateTable()
    {
        if ($this->confirm('iotron: Do You Wish To Migrate All Tables', true)) {
            // waiting for 2 seconds
            $this->warn('iotron: Please wait...');
            sleep(2);
            // running `php artisan migrate`
            $this->warn('iotron: Migrating all tables into database...');
            $migrate = $this->call('migrate:fresh');
            $this->info((string) $migrate);
        }
    }

    private function seedTable()
    {
        if ($this->confirm('iotron: Do You Wish To Seed Dummy Records', true)) {
            // waiting for 2 seconds
            $this->warn('iotron: Please wait...');
            sleep(2);
            // running `php artisan db:seed`
            $this->warn('iotron: seeding data for kickstart...');
            $this->info('----------------------------------');
            $result = $this->call('db:seed');
            $this->info((string) $result);
        }
    }

    private function createAdmin()
    {

        if ($this->confirm('iotron: Do You Wish To Create A Super Admin', true)) {
            // waiting for 2 seconds
            $this->warn('iotron: Please wait...');
            sleep(2);

            $this->createSuperAdmin();
        }
    }

    private function storageLink()
    {
        if ($this->confirm('iotron: Do You Wish To Symlink Storage Directory?', true)) {
            // waiting for 2 seconds
            $this->warn('iotron: Please wait...');
            sleep(2);
            // running `php artisan storage:link`
            $this->warn('iotron: Linking Storage directory...');
            // if (is_dir(public_path('storage'))) {
            //     $this->warn('iotron: An existing Storage link exists...');
            //     //rmdir(public_path('storage'));
            //     $filesystem = app(Filesystem::class);
            //     $filesystem->deleteDirectory(public_path('storage'));
            //     $this->warn('iotron: Removing existing Storage link from filesystem...');
            // }
            $result = $this->call('storage:link');
            $this->info((string) $result);
            // $this->info('iotron: New Storage link created successfully');
        }
    }

    private function resetCache()
    {
        if ($this->confirm('iotron: Do You Wish To ReCache New Changes...?', true)) {
            // waiting for 2 seconds
            $this->warn('iotron: Please wait...');
            sleep(2);
            // cached new changes
            $this->warn('iotron: Clearing cache...');
            $this->info('----------------------');
            $clear = $this->call('optimize:clear');
            $this->info((string) $clear);
            $this->newLine();
            $this->warn('iotron: Caching new changes...');
            $this->info('---------------------------');
            $optimize = $this->call('optimize');
            $this->info((string) $optimize);
        }
    }

    private function composerDumpAutoload()
    {
        if ($this->confirm('iotron: Do You Wish To Dump Composer For Auto loading Everything?', true)) {
            // waiting for 2 seconds
            $this->warn('iotron: Please wait...');
            sleep(2);
            // running `composer dump-autoload`
            $this->warn('iotron: Composer Dump Autoload...');
            $result = shell_exec('composer dump-autoload');
            $this->info((string) $result);
        }
    }
}
