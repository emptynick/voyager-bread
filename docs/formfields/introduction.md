# Introduction

Formfields bring all the functionality to the BREAD-builder and they have a few things in common.

### Field

The field is the column in your database table where the data is stored and read from.  
You want to make sure that you choose a field for every formfield and that you only assign the field once to one formfield.

{% hint style="warning" %}
When you are using Accessors in Views, please make sure that the accessor-name is either a field, or you defined a [Mutator](https://laravel.com/docs/eloquent-mutators#defining-a-mutator) for it.
{% endhint %}

### Validation

Here you can enter all your validation rules and messages for this formfield.  
You can find a list of all rules in the [Laravel docs](https://laravel.com/docs/validation#available-validation-rules).

![](../.gitbook/assets/validation.png)

### Translatable options

All option inputs are translatable via language files.  
Instead of writing normal text you can just pass your language key prefixed with two underscores, for example `__validation.required`

