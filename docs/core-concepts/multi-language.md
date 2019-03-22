# Multi language

This package stores all translations as JSON in the same field.  
For best performance it's suggested to use JSON as the field type.  

By default, searching a BREAD will search in all locales saved.  
To only search in the currently selected locale the field **has** to be JSON and the options `Search in locale` must be checked for your fields.  

Your searches will be case-sensitive if you are using JSON, **don't** have `Search in locale` checked and have a case-sensitive charset for your field.