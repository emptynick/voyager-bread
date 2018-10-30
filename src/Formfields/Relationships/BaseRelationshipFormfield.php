<?php

namespace Bread\Formfields\Relationships;

use Bread\BreadFacade;
use Bread\Formfields\BaseFormfield;

class BaseRelationshipFormfield extends BaseFormfield
{
    public $group = 'relationship';

    public function getComponent($action, $render = false)
    {
        $path = 'bread::formfields.relationships.'.$this->codename.'.'.$action;
        if (!\View::exists($path)) {
            $path = 'bread::formfields.relationships.'.$this->codename;
        }
        if ($render) {
            return view($path);
        } else {
            return $path;
        }
    }

    public function store($input, $model = null)
    {
        //Todo: return false because we dont actually store anything in the DB (directly)
        return false;
    }

    public function prepare($bread, $model, $content = null)
    {
        parent::prepare($bread, $model, $content);

        if (method_exists($model, $this->options['relationship'])) {
            $relationship = $model->{$this->options['relationship']}();
            $related = $relationship->getRelated();
            $related_bread = BreadFacade::getBreadByTable($related->getTable());
            if ($related_bread) {
                $list = $related_bread->layouts->where('type', 'list')->where('name', $this->options['list'])->first();
                $list = $list->prepare($bread, $model);
                $rl_model = app($related_bread->model);
                $this->computed->put('relationship_element', $list->elements->get($list->relationship ?? 1));
                $this->computed->put('relationship_url', route('voyager.'.get_translated_value($related_bread->slug).'.data'));

                $this->computed->put('isTranslatable', ($rl_model->isTranslatable && $rl_model->isFieldTranslatable($this->computed['relationship_element']->field)));
                if ($this->options['allow_add'] && $this->options['add_view'] != '') {
                    $view = $related_bread->getViews()->where('name', $this->options['add_view'])->first();
                    $view->isTranslatable = $rl_model->isTranslatable ?? false;
                    //Todo: this might end in an endless loop
                    $this->computed->put('view', $view->prepare($bread, $model));
                    $this->computed->put('create_url', route('voyager.'.get_translated_value($related_bread->slug).'.store'));
                }
            }
        }

        return $this;
    }
}
