# Configurations

After installing the hook you will find a configuration file under `config/bread.php`.  
This file contains the following options:

### bread\_path

This is the path to the folder where all BREAD json files are stored and read from.  
It defaults to `storage_path('bread')`

### bread\_buttons

```php
'bread_buttons' => [
    'save_edit'     => true,
    'save_new'      => true,
]
```

Here you can decide which buttons will be displayed when editing/adding entries.

`save_edit` saves the entry and redirects to the edit-page

`save_new` saves the entry and redirects to the add-page

![](../.gitbook/assets/bread_buttons.png)
