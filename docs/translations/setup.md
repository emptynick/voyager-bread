# Setup

All fields are translatable very easy.

To make a model and its fields translatable, just include the following Trait

`Bread\Traits\Translatable`

And define which fields are translatable:

`protected $translatable = ['slug', 'name'];`

After that, your model should look like this:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Bread\Traits\Translatable;

class Category extends Model
{
    use Translatable;

    protected $translatable = ['slug', 'name'];
}

```

Now you can define which languages your app will use by changing the multilanguages config in `config/voyager.php` for example:

```php
'multilingual' => [
        'enabled' => true,
        'rtl' => false,
        'default' => 'en',
        'locales' => [
            'en',
            'de',
            'fr'
        ],
    ],
```

