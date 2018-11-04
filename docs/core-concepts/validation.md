# Validation

You can apply validation-rules the same way you'd do in Laravel.  
You'll find a full list of available rules in the [Laravel documentation](https://laravel.com/docs/validation#available-validation-rules).

{% hint style="info" %}
Validation rules are automatically applied to translatable fields to match the current app-locale.  
This can be changed with the [fully\_validate\_translatable\_fields ](../getting-started/configurations.md#fully_validate_translatable_fields)config-var.

**For example:**  
You have two locales **en** and **de** where **en** is your current app-locale, a validation rule will only apply for the english part of the input, **not** the german.
{% endhint %}



