<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BreadTableUnseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Skip if table does not exists.
        if (!Schema::hasTable('bread')) {
            return;
        }

        /*DB::table('bread')
            ->whereIn('name', ['foo', 'bar', 'baz'])
            ->delete();*/
    }
}
