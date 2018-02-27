<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Bread\Traits\HasRelationships;

class Page extends Model
{
    use HasRelationships;

    protected $relationships = ['categories'];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
