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
        \DB::table('bread')->insert(array (
            0 =>
            array (
                'id' => 1,
                'table_name' => 'categories',
                'slug' => 'categories',
                'display_name_singular' => 'Category',
                'display_name_plural' => 'Categories',
                'icon' => 'voyager-categories',
                'model_name' => 'App\\Category',
                'policy_name' => NULL,
                'controller_name' => NULL,
                'browse_list' => 2,
                'read_view' => 1,
                'add_view' => 1,
                'edit_view' => 1,
            ),
            1 =>
            array (
                'id' => 2,
                'table_name' => 'pages',
                'slug' => 'pages',
                'display_name_singular' => 'Page',
                'display_name_plural' => 'Pages',
                'icon' => 'voyager-file-text',
                'model_name' => 'App\\Page',
                'policy_name' => NULL,
                'controller_name' => NULL,
                'browse_list' => 5,
                'read_view' => 4,
                'add_view' => 4,
                'edit_view' => 4,
            ),
            2 =>
            array (
                'id' => 3,
                'table_name' => 'posts',
                'slug' => 'posts',
                'display_name_singular' => 'Post',
                'display_name_plural' => 'Posts',
                'icon' => 'voyager-news',
                'model_name' => 'App\\Post',
                'policy_name' => NULL,
                'controller_name' => NULL,
                'browse_list' => 7,
                'read_view' => 6,
                'add_view' => 6,
                'edit_view' => 6,
            ),
        ));

        \DB::table('bread_views')->insert(array (
            0 =>
            array (
                'id' => 1,
                'bread_id' => 1,
                'view_type' => 'view',
                'name' => 'Default View',
            ),
            1 =>
            array (
                'id' => 2,
                'bread_id' => 1,
                'view_type' => 'list',
                'name' => 'Default List',
            ),
            2 =>
            array (
                'id' => 3,
                'bread_id' => 1,
                'view_type' => 'list',
                'name' => 'Relationship List',
            ),
            3 =>
            array (
                'id' => 4,
                'bread_id' => 2,
                'view_type' => 'view',
                'name' => 'Default View',
            ),
            4 =>
            array (
                'id' => 5,
                'bread_id' => 2,
                'view_type' => 'list',
                'name' => 'Default List',
            ),
            5 =>
            array (
                'id' => 6,
                'bread_id' => 3,
                'view_type' => 'view',
                'name' => 'Default View',
            ),
            6 =>
            array (
                'id' => 7,
                'bread_id' => 3,
                'view_type' => 'list',
                'name' => 'Default List',
            ),
        ));

        \DB::table('bread_rows')->insert(array (
            0 =>
            array (
                'id' => 1,
                'bread_view_id' => 1,
                'field' => 'name',
                'type' => 'text',
                'order' => 0,
                'width' => 6,
                'options' => '{"label":"Name","helptext":"Name of the Category","placeholder":"Name","value":null}',
                'validation_rules' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'bread_view_id' => 1,
                'field' => 'slug',
                'type' => 'text',
                'order' => 1,
                'width' => 6,
                'options' => '{"label":"Slug","helptext":"Slug of the Category","placeholder":"Slug","value":null,"slug":"true","slug_origin":"name"}',
                'validation_rules' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'bread_view_id' => 2,
                'field' => 'name',
                'type' => 'text',
                'order' => 0,
                'width' => NULL,
                'options' => '{"label":"Name","searchable":"true","orderable":"true"}',
                'validation_rules' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'bread_view_id' => 2,
                'field' => 'slug',
                'type' => 'text',
                'order' => 1,
                'width' => NULL,
                'options' => '{"label":"Slug","searchable":"true","orderable":"true"}',
                'validation_rules' => NULL,
            ),
            4 =>
            array (
                'id' => 5,
                'bread_view_id' => 3,
                'field' => 'name',
                'type' => 'text',
                'order' => 0,
                'width' => NULL,
                'options' => '{"label":"Name","searchable":"true","orderable":"true"}',
                'validation_rules' => NULL,
            ),
            5 =>
            array (
                'id' => 6,
                'bread_view_id' => 4,
                'field' => 'title',
                'type' => 'text',
                'order' => 0,
                'width' => 6,
                'options' => '{"label":"Title","helptext":"Title of the Page","placeholder":"Title","value":null}',
                'validation_rules' => NULL,
            ),
            6 =>
            array (
                'id' => 7,
                'bread_view_id' => 4,
                'field' => 'slug',
                'type' => 'text',
                'order' => 1,
                'width' => 6,
                'options' => '{"label":"Slug","helptext":"Slug of the Page","placeholder":"Slug","value":null,"slug":"true","slug_origin":"title"}',
                'validation_rules' => NULL,
            ),
            7 =>
            array (
                'id' => 8,
                'bread_view_id' => 4,
                'field' => 'relationship',
                'type' => 'relationship',
                'order' => 2,
                'width' => 12,
                'options' => '{"label":"Categories","helptext":"Categories of this Page","relationship":"categories","list_id":"3","view_id":"1"}',
                'validation_rules' => NULL,
            ),
            8 =>
            array (
                'id' => 9,
                'bread_view_id' => 5,
                'field' => 'title',
                'type' => 'text',
                'order' => 0,
                'width' => NULL,
                'options' => '{"label":"Title","searchable":"true","orderable":"true"}',
                'validation_rules' => NULL,
            ),
            9 =>
            array (
                'id' => 11,
                'bread_view_id' => 1,
                'field' => 'relationship',
                'type' => 'relationship',
                'order' => 2,
                'width' => 12,
                'options' => '{"label":"Pages","helptext":"Pages with this Category","relationship":"pages","list_id":"5","view_id":"null"}',
                'validation_rules' => NULL,
            ),
            10 =>
            array (
                'id' => 12,
                'bread_view_id' => 6,
                'field' => 'title',
                'type' => 'text',
                'order' => 0,
                'width' => 6,
                'options' => '{"label":"Title","helptext":"Title of the Post","placeholder":"Title","value":null}',
                'validation_rules' => NULL,
            ),
            11 =>
            array (
                'id' => 13,
                'bread_view_id' => 6,
                'field' => 'slug',
                'type' => 'text',
                'order' => 1,
                'width' => 6,
                'options' => '{"label":"Slug","helptext":"Slug of this Post","placeholder":"Slug","value":null}',
                'validation_rules' => NULL,
            ),
            12 =>
            array (
                'id' => 14,
                'bread_view_id' => 6,
                'field' => 'relationship',
                'type' => 'relationship',
                'order' => 2,
                'width' => 12,
                'options' => '{"label":"Categories","helptext":"Categories of this Post","relationship":"categories","list_id":"3","view_id":"1"}',
                'validation_rules' => NULL,
            ),
            13 =>
            array (
                'id' => 15,
                'bread_view_id' => 7,
                'field' => 'title',
                'type' => 'text',
                'order' => 0,
                'width' => NULL,
                'options' => '{"label":"Title","searchable":"true","orderable":"true"}',
                'validation_rules' => NULL,
            ),
            14 =>
            array (
                'id' => 16,
                'bread_view_id' => 1,
                'field' => 'relationship',
                'type' => 'relationship',
                'order' => 3,
                'width' => 12,
                'options' => '{"label":"Posts","helptext":"Posts with this Category","relationship":"posts","list_id":"7","view_id":"null"}',
                'validation_rules' => NULL,
            ),
        ));
    }
}
