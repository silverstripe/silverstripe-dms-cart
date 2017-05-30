# Changing the cart backend

The default cart backend uses session based storage which stores the cart into a serialized
in-memory array (see `DMSSessionBackend`). This can be changed to any class which implements the `DMSCartBackendInterface`
interface using the Injector:

```yaml
Injector:
  DMSDocumentCart:
    properties:
      backend: %$NewBackendService
```

In the above example, `%$NewBackendService` should be a valid class which implements `DMSCartBackendInterface` interface.

You can also switch this by providing is to the `DMSDocumentCart` constructor:

```php
$cart = DMSDocumentCart::create($newBackendService);
```
