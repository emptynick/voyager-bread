<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Illuminate\Http\Request;

class BreadController extends Controller
{
    public function index(Request $request)
    {
        $bread = $this->getBread($request);
        // Get Browse List
        $breadView = $bread->list($bread->browse_list, 'browse');
        if ($breadView === null) {
            throw new \Exception('Please assign a (default) Browse List!');
        }

        $notOrderableColumns = [];
        $firstOrderedColumn = '';
        foreach ($breadView->visible_rows as $col => $row) {
            if (!$row->is_orderable) {
                $notOrderableColumns[] = $col + 1; /* @todo: +1 only if can delete **/
            } else {
                if ($firstOrderedColumn == '') {
                    $firstOrderedColumn = $col + 1;
                }
            }
        }

        $view = 'bread::bread.browse';

        return view($view, compact(
            'bread',
            'breadView',
            'notOrderableColumns',
            'firstOrderedColumn'
        ));
    }

    public function show(Request $request, $id)
    {
        $bread = $this->getBread($request);
        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;
        $breadContent = (strlen($bread->model_name) != 0)
            ? app($bread->model_name)->findOrFail($id)
            : DB::table($bread->name)->where('id', $id)->first();
        // Check permission
        // $this->authorize('edit', app($bread->model_name));
        // Get Read View
        $breadView = $bread->view($bread->read_view, 'read');

        $view = 'bread::bread.read';

        return view($view, compact(
            'bread',
            'breadView',
            'breadContent'
        ));
    }

    public function edit(Request $request, $id)
    {
        $bread = $this->getBread($request);
        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;
        $breadContent = (strlen($bread->model_name) != 0)
            ? app($bread->model_name)->findOrFail($id)
            : DB::table($bread->name)->where('id', $id)->first();
        // Check permission
        //$this->authorize('edit', app($bread->model_name));
        // Get Edit View
        $breadView = $bread->view($bread->edit_view, 'edit');
        if ($breadView === null) {
            throw new \Exception('Please assign a (default) Edit View!');
        }

        $compact = false;

        $view = 'bread::bread.edit-add';

        return view($view, compact(
            'bread',
            'breadView',
            'breadContent',
            'compact'
        ));
    }

    public function update(Request $request, $id = null)
    {
        $bread = $this->getBread($request);
        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;
        $breadContent = (strlen($bread->model_name) != 0)
            ? app($bread->model_name)->findOrFail($id)
            : DB::table($bread->name)->where('id', $id)->first();

        $oldKeyValue = $breadContent->{$breadContent->getKeyName()};

        $view = BreadFacade::model('BreadView')->find($bread->edit_view);
        $rules = $view->getValidationRules();
        $messages = $view->getValidationRules(true);

        if ($request->has('relationship')) {
            /* @todo: Validate Relationships */
        }
        $this->validate($request->input(), $rules, $messages);

        $this->fillModel($request, $view, $breadContent);
        $newKeyValue = $breadContent->{$breadContent->getKeyName()};
        $breadContent->save();

        if ($request->has('relationship')) {
            foreach ($request->relationship as $id => $input) {
                $relationship_row = BreadFacade::model('BreadRow')->find($id);
                if (isset($relationship_row)) {
                    $relationship = $breadContent->relationships[$relationship_row->options['relationship']];
                    $this->saveRelationship($relationship, $input, $oldKeyValue, $newKeyValue, $relationship_row);
                }
            }
        }

        return redirect()
            ->route('voyager.'.$bread->slug.'.index')
            ->with([
                'message'    => __('voyager::generic.successfully_updated').' '.$bread->display_name_singular,
                'alert-type' => 'success',
        ]);
    }

    public function create(Request $request, $view = null)
    {
        $bread = $this->getBread($request);
        // Get Edit View
        if (isset($view)) {
            $breadView = $bread->view($view);
            $compact = true;
        } else {
            $breadView = $bread->view($bread->add_view, 'add');
            $compact = false;
        }

        $view = 'bread::bread.edit-add';

        return view($view, compact(
            'bread',
            'breadView',
            'compact'
        ));
    }

    public function store(Request $request, $view = null)
    {
        $bread = $this->getBread($request);
        $breadModel = app($bread->model_name);

        if (isset($view)) {
            $breadView = $bread->view($view);
        } else {
            $breadView = $bread->view($bread->add_view);
        }

        $rules = $breadView->getValidationRules();
        $messages = $breadView->getValidationRules(true);

        //Validate fields based on view
        $this->validate($request->input(), $rules, $messages);

        $created = new $breadModel();
        $this->fillModel($request, $breadView, $created);
        $created->save();

        if ($request->ajax()) {
            return $created->{$created->getKeyName()};
        }

        return redirect()
            ->route('voyager.'.$bread->slug.'.index')
            ->with([
                'message'    => __('voyager::generic.successfully_added_new').' '.$bread->display_name_singular,
                'alert-type' => 'success',
            ]);
    }

    public function delete(Request $request, $id = null)
    {
        $bread = $this->getBread($request);

        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL or Model Binding
            $ids[] = $id instanceof Model ? $id->{$id->getKeyName()} : $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$bread->model_name, 'findOrFail'], $id);
        }

        $res = $data->destroy($ids);
        $displayName = count($ids) > 1 ? $bread->display_name_plural : $bread->display_name_singular;
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
                'alert-type' => 'error',
            ];
        if ($res) {
            //event(new BreadDataDeleted($bread, $data)); //Todo: Re-implement event
        }

        return redirect()->route("voyager.{$bread->slug}.index")->with($data);
    }

    // Gets data for Datatable
    public function data(Request $request, $breadView = null, $breadRow = null)
    {
        $compact = false;

        if ($breadView === null) {
            $bread = $this->getBread($request);
            /** @todo: Change view based on role **/
            $breadView = $bread->list($bread->browse_list);
        } else {
            $breadView = BreadFacade::model('BreadView')->find($breadView);
            $bread = $breadView->bread;
            $compact = true;
        }

        $model = $bread->model;
        $recordsTotal = $model->count();

        if ($request->has('columns')) {
            //Global Search Query
            if ($request->has('search')) {
                $global_query = $request->input('search.value');

                $model = $model->where(function ($query) use ($breadView, $global_query) {
                    foreach ($breadView->searchable_rows as $row) {
                        $query->orWhere($row->field, 'like', '%'.$global_query.'%');
                    }
                });
            }

            //Order
            $order_column = -1;
            $order_relationship = null;
            $order_attribute = null;
            if ($request->has('order')) {
                $order_column = intval($request->input('order.0.column'));
                $order_dir = $request->input('order.0.dir');
            }

            foreach ($request->columns as $key => $column) {
                $details = parse_field_name($column['name']);

                if (boolval($column['searchable'])) {
                    if (isset($column['search']) && isset($column['search']['value'])) {
                        $local_query = $column['search']['value'];

                        if ($details['type'] == 'pivot') {
                            $relationship = $details['relationship'];
                            $model = $model->$relationship()->wherePivot($details['attribute'], 'like', '%'.$local_query.'%');
                        } elseif ($details['type'] == 'relationship') {
                            $model = $model->whereHas($details['relationship'], function ($query) use ($details, $local_query) {
                                $query->where($details['attribute'], 'like', '%'.$local_query.'%');
                            });
                        } elseif ($details['type'] == 'attribute') {
                            $model = $model->where($details['attribute'], 'like', '%'.$local_query.'%');
                        }
                    }
                }

                if ($key == $order_column) {
                    if ($details['type'] == 'attribute') {
                        $model = $model->orderBy($details['attribute'], $order_dir);
                    } elseif ($details['type'] == 'pivot') {
                        /* @todo: Order here **/
                    } elseif ($details['type'] == 'relationship') {
                        /** @todo: Order here **/
                        $order_relationship = $details['relationship'];
                        $order_attribute = $details['attribute'];
                    }
                }
            }
        }

        $recordsFiltered = $model->count();
        if (isset($order_relationship) && isset($order_attribute)) {
            $results = $model->get();
            if ($order_dir == 'desc') {
                $results = $results->sortByDesc($order_relationship.'.'.$order_attribute);
            } else {
                $results = $results->sortBy($order_relationship.'.'.$order_attribute);
            }
            $results = $results->slice($request->input('start'), $request->input('length'));
        } else {
            $model = $model->skip($request->input('start'))->take($request->input('length'));
            $results = $model->get();
        }

        $tableData = [];
        foreach ($results as $num => $result) {
            $nested = [];

            if (!$compact) {
                /* @todo: Hide checkbox ONLY here if the user has no permission to delete **/
                $nested[] = '<input type="checkbox" name="row_id" id="checkbox_'.$result->getKey().'" value="'.$result->getKey().'">';
            }

            foreach ($breadView->visible_rows as $key => $row) {
                $parts = explode_field_name($row->field);

                $content = '';
                if (count($parts) == 1) {
                    //Standard Field
                    $content = $row->formfield->createOutput($result->{$row->field});
                } elseif (count($parts) == 2) {
                    //Relationship
                    list($relation, $attribute) = $parts;

                    $relationship_content = $result->{$relation};
                    if (isset($relationship_content)) {
                        if ($relationship_content instanceof \Illuminate\Support\Collection) { /** @todo: maybe invert this condition **/
                            $content = $row->formfield->createOutput($relationship_content, true, $attribute);
                        } else {
                            $content = $row->formfield->createOutput($relationship_content->{$attribute});
                        }
                    }
                } elseif (count($parts) == 3) {
                    //Pivot
                }
                $nested['DT_RowId'] = $result->{$result->getKeyName()};
                if ($bread->model->getKeyName() == $row->field && !$compact) {
                    /* @todo: && user is allowed to display **/
                    $nested[] = '<a href="'.route('voyager.'.$bread->slug.'.show', $result->{$result->getKeyName()}).'">'.
                                $content.
                                '</a>';
                } else {
                    $nested[] = $content;
                }
            }

            if (!$compact) {
                /** @todo: Test if user has permission to edit/display/delete */
                $actions = '<a href="'.route('voyager.'.$bread->slug.'.delete', $result->{$result->getKeyName()}).'"
                            title="'.__('voyager::generic.delete').'" class="btn btn-sm btn-danger pull-right edit">
                            <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">'.__('voyager::generic.delete').'</span>
                            </a>
                            <a href="'.route('voyager.'.$bread->slug.'.edit', $result->{$result->getKeyName()}).'"
                            title="'.__('voyager::generic.edit').'" class="btn btn-sm btn-primary pull-right edit">
                            <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">'.__('voyager::generic.edit').'</span>
                            </a>
                            <a href="'.route('voyager.'.$bread->slug.'.show', $result->{$result->getKeyName()}).'"
                            title="'.__('voyager::generic.view').'" class="btn btn-sm btn-warning pull-right">
                            <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">'.__('voyager::generic.view').'</span>
                            </a>';
                $nested[] = $actions;
            }
            $tableData[] = $nested;
        }

        return [
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $tableData,
        ];
    }
}
