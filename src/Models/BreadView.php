<?php

namespace Bread\Models;

use Illuminate\Database\Eloquent\Model;
use Bread\BreadFacade;

class BreadView extends Model
{
    protected $table = 'bread_views';
    protected $guarded = ['id'];

    public function rows()
    {
        return $this->hasMany(BreadFacade::modelClass('BreadRow'))->orderBy('order');
    }

	public function getVisibleRowsAttribute()
	{
		return $this->rows->filter(function($row)
		{
			return $row->is_visible;
		});
	}

	public function getInvisibleRowsAttribute()
	{
		return $this->rows->filter(function($row)
		{
			return !$row->is_visible;
		});
	}

	public function getSearchableRowsAttribute()
	{
		return $this->rows->filter(function($row)
		{
			return $row->is_searchable;
		});
	}

	public function getNotOrderableRowsAttribute()
	{
		return $this->rows->filter(function($row)
		{
			return !$row->is_orderable;
		});
	}

	public function getFirstOrderableRowAttribute()
	{
		foreach ($this->visible_rows as $key => $row) {
			if ($row->isOrderable) {
				return $key;
			}
		}
	}

    public function getColumnDefinitions($array = false)
    {
        $columns = [];
        foreach ($this->visible_rows as $row) {
            $columns[] = [
                'name'       => $row->field,
                'sortable'   => $row->is_orderable,
                'searchable' => $row->is_searchable,
            ];
        }
        if ($array) {
            return json_encode($columns);
        }
        return str_replace(array('[', ']'), '', json_encode($columns));
    }

    public function getValidationRules($messages = false, $skip_relationships = true, $prefix = '', $suffix = '')
    {
        $rules = [];
        foreach ($this->rows as $row) {
            if ($skip_relationships && $row->type == 'relationship') {
                continue;
            }
            if (isset($row->validation_rules)) {
                if (!$messages) {
                    $rules[$prefix.$row->field.$suffix] = implode('|', array_keys($row->validation_rules));
                } else {
                    foreach ($row->validation_rules as $rule => $message) {
                        if ($message != '') {
                            $trans = __($message);
                            $rules[$prefix.$row->field.'.'.str_before($rule, ':').$suffix] = ($message != $trans) ? $trans : $message;
                        }
                    }
                }
            }
        }
        return $rules;
    }

    public function bread()
    {
        return $this->belongsTo(BreadFacade::modelClass('Bread'));
    }
}
