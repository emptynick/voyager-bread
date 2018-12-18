<?php

if (!function_exists('get_table_names')) {
    function get_table_names()
    {
        return \DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }
}

if (!function_exists('get_table_fields')) {
    function get_table_fields($table)
    {
        return \DB::connection()->getSchemaBuilder()->getColumnListing($table);
    }
}

if (!function_exists('get_model_accessors')) {
    function get_model_accessors($model)
    {
        return $model->accessors ?? [];
    }
}

if (!function_exists('get_model_relationships')) {
    function get_model_relationships($model)
    {
        return $model->relationships ?? [];
    }
}

if (!function_exists('get_model_translatable')) {
    function get_model_translatable($model)
    {
        return $model->translatable ?? [];
    }
}
