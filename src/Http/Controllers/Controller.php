<?php
namespace Bread\Http\Controllers;

use Illuminate\Http\Request;
use Bread\BreadFacade;

class Controller
{
	public function validate($input, $rules, $messages)
	{
		return \Validator::make($input, $rules, $messages)->validate();
	}

	public function fillModel($request, $view, $model, $skip_relationships = true)
	{
		foreach ($view->rows as $row) {
			if ($row->type == 'relationship' && $skip_relationships) {
				continue;
			}
			$field = $row->field;
			if ($request->has($field)) {
				$model->$field = $this->getContentBasedOnType(
					$row->type,
					$request->input($field),
					$row->options
				);
			}
		}

		return $model;
	}

	public function saveRelationship($relationship, $input, $oldKeyValue, $newKeyValue, $relationship_row)
	{
		$type = get_unqualified_class($relationship);

        if ($type == 'BelongsTo') {
			$id = (isset($input[0]['id']) ? $input[0]['id'] : null);
			$relationship->getParent()
				->where($relationship->getOwnerKey(), $oldKeyValue)
				->update([$relationship->getForeignKey() => $id]);
        } else if ($type == 'BelongsToMany') {
			$method = $relationship->getRelationName();
			$list = BreadFacade::model('BreadView')->find($relationship_row->options['list_id']);
			//Create sync values
			$sync = [];

			foreach ($input as $instance) {
				if (!isset($instance['id'])) {
					continue;
				}
				$sync[$instance['id']] = [];
				foreach ($instance as $field => $pivot) {
					if (starts_with($field, 'pivot')) {
						$row = $list->rows()->where('field', $field)->first();
						list($p, $r, $a) = explode_field_name($field);
						$sync[$instance['id']][$a] = $this->getContentBasedOnType($row->type, $pivot, $row->options);
					}
				}
			}

			$relationship->getParent()->$method()->sync($sync);

        } else if ($type == 'MorphToMany') {

        } else if ($type == 'MorphTo') {
            //
        } else if ($type == 'HasOne' || $type == 'HasMany') {
			//Unset all that have $oldKeyValue
			$relationship->getRelated()
				->where($relationship->getForeignKeyName(), $oldKeyValue)
				->update([$relationship->getForeignKeyName() => null]);

			foreach ($input as $id) {
				$relationship->getRelated()
					->where($relationship->getRelated()->getKeyName(), (isset($id['id']) ? $id['id'] : null))
					->update([$relationship->getForeignKeyName() => $newKeyValue]);
			}
		} else {
            //throw new \Exception('Relationship-Type '.$type.' is currently not supported!');
        }
	}

	public function getContentBasedOnType($type, $content, $options)
	{
		return BreadFacade::formField($type)->createContent($content, $options);
	}

	public function getSlug(Request $request)
    {
        return explode('.', $request->route()->getName())[1];
    }

    public function getBread(Request $request)
	{
        $slug = $this->getSlug($request);
        return BreadFacade::model('Bread')->where('slug', '=', $slug)->first();
	}
}
