# Change backend provider

The default backend provider uses session based storage which stores the cart into a serialized
in-memory array. This can be changed to any class which implements the `DMSCartBackendInterface`
interface using the Injector:

```yaml
Injector:
    DMSDocumentCart:
      properties:
        backend: %$NewBackendService
```

In the above example, `%$NewBackendService` should be a valid class which implements `DMSCartBackendInterface` interface.
