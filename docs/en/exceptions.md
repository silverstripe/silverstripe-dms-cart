# Exceptions

Whenever the DMS Cart module throws an exception, it will be an instance of `DMSDocumentCartException`. You can use this to catch exceptions that are specific to this module:

```php
try {
    DMSDocumentCart::create(new DMSDocument);
} catch (DMSDocumentCartException $ex) {
    echo $ex->getMessage(); // Backend must implement DMSCartBackendInterface!
}
```
