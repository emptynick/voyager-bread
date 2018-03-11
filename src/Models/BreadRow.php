<?php

namespace Bread\Models;

use Bread\BreadFacade;
use Illuminate\Database\Eloquent\Model;

class BreadRow extends Model
{
    protected $table = 'bread_rows';

    protected $guarded = ['id'];

    protected $casts = [
        'options'          => 'array',
        'validation_rules' => 'array',
    ];

    public function view()
    {
        return $this->belongsTo(BreadFacade::modelClass('BreadView'), 'bread_view_id');
    }

    public function getBreadAttribute()
    {
        return $this->view->bread;
    }

    public function getAddViewAttribute()
    {
        if (!isset($this->options['view_id']) || !is_numeric($this->options['view_id'])) {
            return;
        }

        return BreadFacade::model('BreadView')->find($this->options['view_id']);
    }

    public function getFormfieldAttribute()
    {
        $formfield = BreadFacade::formField($this->type);
        $formfield->setBreadRow($this);

        return $formfield;
    }

    public function getIsVisibleAttribute()
    {
        return !(isset($this->options['invisible']) && $this->options['invisible']);
    }

    public function getIsSearchableAttribute()
    {
        return isset($this->options['searchable']) && $this->options['searchable'];
    }

    public function getIsSearchableStringAttribute()
    {
        return $this->is_searchable ? 'true' : 'false';
    }

    public function getIsOrderableAttribute()
    {
        return isset($this->options['orderable']) && $this->options['orderable'];
    }

    public function getIsOrderableStringAttribute()
    {
        return $this->is_orderable ? 'true' : 'false';
    }

    public function getRelationshipBread()
    {
        return BreadFacade::model('BreadView')->find($this->options['list_id'])->bread;
    }
}
