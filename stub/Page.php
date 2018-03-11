<?php

namespace App;

use Bread\Traits\HasRelationships;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasRelationships;

    protected $relationships = ['categories'];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
