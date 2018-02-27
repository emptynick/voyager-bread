<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Bread\Traits\HasRelationships;

class Category extends Model
{
    use HasRelationships;

    protected $relationships = ['posts', 'pages', 'parent'];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }
}
