## Relationships
Relationships now work in a different way.  
These are the necessary steps:  
1. Include the `HasRelationships` Trait in your model: `use Bread\Traits\HasRelationships;`
2. Use the Trait `use HasRelationships;`
3. Define your relationship methods like `protected $relationships = ['categories', 'tags'];`  
	This step will maybe get unneccesary with PHP 7.1 and Reflection

Thats it. You can now drop your Relationships in the View-Builder, use single Fields in Lists, and use Pivot-Fields for `BelongsToMany` relationships.


### A note on ordering relationships
While searching relationships is possible with no problems, ordering by a relationship is very expensive.  
Try to avoid it if you have a lot of data.
