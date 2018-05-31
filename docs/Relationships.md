# Relationships

Relationships now work in a different way.  
These are the necessary steps:  
1. Include the `HasRelationships` Trait in your model: `use Bread\Traits\HasRelationships;`
2. Use the Trait `use HasRelationships;`
3. Define your relationship methods like `protected $relationships = ['categories', 'tags'];`  
	This step will maybe get unneccesary with PHP 7.1 and Reflection

Thats it. You can now drop your Relationships in the View-Builder, use single Fields in Lists, and use Pivot-Fields for `BelongsToMany` relationships.

### Adding a relationship entry
If you want to add a related entry from a BREAD, e.g. add a `Category` from a `Post` edit/add page, you have to set an `Add-View` to your `Category` relationship inside your `Post` View.  

#### A note on ordering relationships
While searching relationships is possible with no problems, ordering by a relationship is very expensive.  
Try to avoid it if you have a lot of data.

### Example
This is an example model using Relationships

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Bread\Traits\HasRelationships;

class Car extends Model
{
    use HasRelationships;

    protected $relationships = ['manufacturer', 'drivers', 'color'];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function drivers()
    {
        return $this->belongsToMany(Driver::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
```
