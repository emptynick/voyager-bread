<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Bread\Models\Bread;
use Bread\Models\BreadView;
use Bread\Models\BreadRow;

class BreadDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('cars') && Schema::hasTable('drivers') && Schema::hasTable('colors') && Schema::hasTable('manufacturers') && Schema::hasTable('car_driver')) {

            DB::table('colors')->insert([
                ['color' => 'Red'],//1
                ['color' => 'Blue'],//2
                ['color' => 'Green'],//3
                ['color' => 'Black'],//4
                ['color' => 'Yellow'],//5
                ['color' => 'White'],//6
                ['color' => 'Grey'],//7
            ]);

            DB::table('drivers')->insert([
                ['name' => 'Chris'],
                ['name' => 'Mark'],
                ['name' => 'John'],
                ['name' => 'Bruno'],
                ['name' => 'Tony'],
                ['name' => 'Eric'],
                ['name' => 'Alex'],
                ['name' => 'Christina'],
                ['name' => 'Isabella'],
                ['name' => 'Vicki'],
                ['name' => 'Gabriel'],
                ['name' => 'Anabelle'],
            ]);

            DB::table('manufacturers')->insert([
                ['name' => 'BMW'],//1
                ['name' => 'Cadillac'],//2
                ['name' => 'Ford'],//3
                ['name' => 'Honda'],//4
                ['name' => 'Mercedes'],//5
                ['name' => 'Fiat'],//6
                ['name' => 'Chevrolet'],//7
                ['name' => 'Mazda'],//8
            ]);

            DB::table('cars')->insert([
                //BMW
                ['model_name' => 'Z4', 'manufacturer_id' => 1, 'color_id' => 7, 'production_year' => 2007],
                ['model_name' => '530d', 'manufacturer_id' => 1, 'color_id' => 4, 'production_year' => 2004],
                ['model_name' => 'M3', 'manufacturer_id' => 1, 'color_id' => 1, 'production_year' => 2010],
                ['model_name' => '118d', 'manufacturer_id' => 1, 'color_id' => 2, 'production_year' => 2012],

                //Cadillac
                ['model_name' => 'Escalade', 'manufacturer_id' => 2, 'color_id' => 4, 'production_year' => 2002],
                ['model_name' => 'CT6', 'manufacturer_id' => 2, 'color_id' => 7, 'production_year' => 2001],
                ['model_name' => 'XT5', 'manufacturer_id' => 2, 'color_id' => 6, 'production_year' => 2011],

                //Ford
                ['model_name' => 'F350', 'manufacturer_id' => 3, 'color_id' => 1, 'production_year' => 1991],
                ['model_name' => 'Focus ST', 'manufacturer_id' => 3, 'color_id' => 3, 'production_year' => 1998],
                ['model_name' => 'Taurus', 'manufacturer_id' => 3, 'color_id' => 2, 'production_year' => 2006],

                //Honda
                ['model_name' => 'Accord', 'manufacturer_id' => 4, 'color_id' => 1, 'production_year' => 2009],
                ['model_name' => 'CR-V', 'manufacturer_id' => 4, 'color_id' => 6, 'production_year' => 2005],

                //Mercedes
                ['model_name' => 'Sprinter', 'manufacturer_id' => 5, 'color_id' => 6, 'production_year' => 2008],
                ['model_name' => 'S350', 'manufacturer_id' => 5, 'color_id' => 7, 'production_year' => 2010],
                ['model_name' => 'C63 AMG', 'manufacturer_id' => 5, 'color_id' => 5, 'production_year' => 2016],

                //Fiat
                ['model_name' => '500', 'manufacturer_id' => 6, 'color_id' => 5, 'production_year' => 2012],
                ['model_name' => 'Panda', 'manufacturer_id' => 6, 'color_id' => 2, 'production_year' => 2006],
                ['model_name' => 'Ducato', 'manufacturer_id' => 6, 'color_id' => 7, 'production_year' => 2010],

                //Chevrolet
                ['model_name' => 'Corvet C7', 'manufacturer_id' => 7, 'color_id' => 1, 'production_year' => 2013],
                ['model_name' => 'Impala', 'manufacturer_id' => 7, 'color_id' => 4, 'production_year' => 1973],
                ['model_name' => 'Caprice', 'manufacturer_id' => 7, 'color_id' => 3, 'production_year' => 1978],

                //Mazda
                ['model_name' => 'CX-3', 'manufacturer_id' => 8, 'color_id' => 7, 'production_year' => 2015],
                ['model_name' => '6', 'manufacturer_id' => 8, 'color_id' => 6, 'production_year' => 2002],
                ['model_name' => 'MX-5', 'manufacturer_id' => 8, 'color_id' => 1, 'production_year' => 2015],
            ]);
        }
    }
}
