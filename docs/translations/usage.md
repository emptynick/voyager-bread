# Usage

This hook saves translations as JSON in your database - it does not use an additional table.

{% hint style="info" %}
Please note that saving JSON with multiple languages takes more space in your database.  
Try to use TEXT fields or, even better, JSON fields, as they are searchable by Laravel natively.
{% endhint %}

After you've setup everything correctly, the Trait will automatically return the value in the locale the app is currently set to.

## Methods/Variables

### isTranslatable

`isTranslatable` returns a boolean value which indicates if this model is translatable and contains at least one translatable field.

### isFieldTranslatable

`isFieldTranslatable($field)` returns a bool whether the given field is translatable or not. Takes the field-name string as an argument.

### getTranslation

`getTranslation($field, $locale)` returns the translated attribute for a field.

### getTranslations

`getTranslations($field)` returns an array with all translations.

### getPlainValue

`getPlainValue($field)` returns the content of the field as a \(json-encoded\) string.

### setTranslation

`setTranslation($field, $value, $locale)` sets the value for a locale

