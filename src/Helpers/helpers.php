<?php

/**
* Gets all fields of a model.
*
* @param Illuminate\Database\Eloquent\Model $relationship The relationship
*
* @return Illuminate\Support\Collection The columns
*/

if (!function_exists('get_model_fields')) {
    function get_model_fields($model, $relationship = null)
    {
        return get_table_fields($model->getTable(), $relationship);
    }
}

const separator = '|';
if (!function_exists('parse_field_name')) {
    function parse_field_name($field)
    {
        if (starts_with($field, 'pivot'.separator)) {
            list($pivot, $relationship, $attribute) = explode(separator, $field);
            $type = 'pivot';
            return compact('type', 'relationship', 'attribute');
        } else {
            $parts = explode(separator, $field);
            if (count($parts) == 2) {
                //Relationship
                list($relationship, $attribute) = $parts;
                $type = 'relationship';
                return compact('type', 'relationship', 'attribute');
            } else if(count($parts) == 1) {
                //Normal field
                $attribute = $field;
                $type = 'attribute';
                return compact('type', 'attribute');
            } //There should be no other cases
            else {
                return $field;
            }
        }
    }
}

if (!function_exists('explode_field_name')) {
    function explode_field_name($input)
    {
        return explode(separator, $input);
    }
}

if (!function_exists('get_field_name')) {
    function get_field_name()
    {
        return implode(separator, func_get_args());
    }
}

if (!function_exists('get_table_fields')) {
    function get_table_fields($table, $relationship = null)
    {
        $fields = collect(
            array_keys(
                \TCG\Voyager\Database\Schema\SchemaManager::describeTable(
                    $table
                )->toArray()
            )
        );

        //Exclude pivot key names
        if (isset($relationship)) {
            $fields = $fields->diff([
                $relationship->getForeignPivotKeyName(),
                $relationship->getRelatedPivotKeyName()
            ]);
        }

        return $fields;
    }
}

if (!function_exists('get_unqualified_class')) {
    function get_unqualified_class($class)
    {
        $class = get_class($class);
        return substr($class, strrpos($class, '\\')+1);
    }
}

if (!function_exists('get_related_bread')) {
    function get_related_bread($relationship)
    {
        return Bread::model('Bread')->where('table_name', $relationship->getRelated()->getTable())->first();
    }
}

if (!function_exists('parse_validation')) {
    function parse_validation($input)
    {
        $rules = [];
        foreach ($input as $rule) {
            $rules[$rule['rule']] = $rule['message'];
        }

        return $rules;
    }
}
