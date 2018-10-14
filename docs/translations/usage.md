# Usage

This hook saves translations as JSON in your database - it does not use an additional table.

{% hint style="info" %}
Please note that saving JSON with multiple languages takes more space in your database.  
Try to use TEXT fields or, even better, JSON fields, as they are searchable by Laravel natively.
{% endhint %}

After you've setup everything correctly, the Trait will automatically return the value in the locale the app is currently set to. If you want another locale than the current, use `app()->setLocale('...')`.

