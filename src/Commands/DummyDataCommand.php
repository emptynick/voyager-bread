<?php

namespace Bread\Commands;

use Illuminate\Console\Command;
use Bread\Bread;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        $this->info('Publishing assets');
        $this->call('vendor:publish', ['--provider' => BreadServiceProvider::class, '--force' => '']);

        $this->info('Copying models to /app');
		$files = $filesystem->allFiles(__DIR__.'/../../stub');

		foreach ($files as $file) {
			$name = basename($file);
			$filesystem->copy($file, base_path('app/'.$name));
		}

		$this->info('Creating tables');

		if (!Schema::hasTable('colors')) {
			Schema::create('colors', function (Blueprint $table) {
	            $table->increments('id');
	            $table->string('color');
	        });
		}

		if (!Schema::hasTable('drivers')) {
			Schema::create('drivers', function (Blueprint $table) {
	            $table->increments('id');
	            $table->string('name');
	        });
		}

		if (!Schema::hasTable('manufacturers')) {
			Schema::create('manufacturers', function (Blueprint $table) {
				$table->increments('id');
	            $table->string('name');
	        });
		}

		if (!Schema::hasTable('cars')) {
			Schema::create('cars', function (Blueprint $table) {
	            $table->increments('id');
	            $table->string('model_name');
				$table->integer('manufacturer_id')->nullable()->unsigned();
				$table->integer('color_id')->nullable()->unsigned();
				$table->integer('production_year')->nullable()->unsigned();

				$table->foreign('manufacturer_id')->references('id')->on('manufacturers');
				$table->foreign('color_id')->references('id')->on('colors');
	        });
		}

		if (!Schema::hasTable('car_driver')) {
			Schema::create('car_driver', function (Blueprint $table) {
				$table->integer('car_id')->unsigned();
				$table->integer('driver_id')->unsigned();
				$table->dateTime('date_from')->nullable();
				$table->dateTime('date_to')->nullable();

				$table->foreign('car_id')->references('id')->on('cars');
				$table->foreign('driver_id')->references('id')->on('drivers');
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
