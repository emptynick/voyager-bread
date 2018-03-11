<?php

namespace Bread\Commands;

use Bread\Bread;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

class DummyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bread:dummydata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs a set of Models and Data to test BREAD';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
        if (!Schema::hasTable('categories') || !Schema::hasTable('posts') || !Schema::hasTable('pages')) {
            $this->info('Please run');
            $this->info('php artisan db:seed --class=VoyagerDummyDatabaseSeeder');
            $this->info('Before running this command!');

            return;
        }

        $this->info('Copying models to /app');
        $files = $filesystem->allFiles(__DIR__.'/../../stub');

        foreach ($files as $file) {
            $name = basename($file);
            $filesystem->copy($file, base_path('app/'.$name));
        }

        $this->info('Creating pivot tables');

        if (!Schema::hasTable('category_post')) {
            Schema::create('category_post', function (Blueprint $table) {
                $table->integer('category_id')->unsigned();
                $table->integer('post_id')->unsigned();
                $table->foreign('category_id')->references('id')->on('categories');
                $table->foreign('post_id')->references('id')->on('posts');
            });
        }

        if (!Schema::hasTable('category_page')) {
            Schema::create('category_page', function (Blueprint $table) {
                $table->integer('category_id')->unsigned();
                $table->integer('page_id')->unsigned();
                $table->foreign('category_id')->references('id')->on('categories');
                $table->foreign('page_id')->references('id')->on('pages');
            });
        }

        $this->info('Seeding data');

        $this->seed('BreadDummySeeder');

        $this->info('Done!');
    }

    public function seed($class)
    {
        if (!class_exists($class)) {
            require_once __DIR__.'/../../resources/database/seeders/'.$class.'.php';
        }
        with(new $class())->run();
    }
}
