# Configurations

After installing the hook you will find a configuration file under \`config/bread.php\`.  
This file contains the following options:

### bread\_path

This is the path to the folder where all BREAD json files are stored and read from.  
It defaults to `storage_path('bread')`

### colors

This defines which colors show up in the color picker. Possible values are:

* basic
* text-basic
* text-advanced
* material-basic
* material-light
* material-dark

Those give you a good set of colors, but if you want to use your own colors, you can do so by passing an array of 6-digit HEX colors, for example:

```php
'colors'  => ['#FF0000', '##00FF00', '#0000FF', ''],
```

Use an empty string for transparent.

