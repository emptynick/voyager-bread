# Accessors

You can use [Accessors](https://laravel.com/docs/eloquent-mutators#defining-an-accessor) in both [Lists ](lists.md)and [Views](views.md).

To do so, you just have to define the following array in your model:

```php
<?php

namespace App;

class Something extends Model
{
    public $accessors = ['full_name'];
    
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
```

{% hint style="warning" %}
When you are using Accessors in Views, please make sure that the accessor-name is either a field, or you defined a [Mutator](https://laravel.com/docs/eloquent-mutators#defining-a-mutator) for it.
{% endhint %}

