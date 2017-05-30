# Configuration

## Setting BCC recipient email addresses

You can change the default list of email recipients using YAML configuration, for example:

```yaml
DMSCheckoutController:
  recipient_emails:
    - your@email.com
    - another@email.com
```

## Allowing a document to be added to a cart

By default new documents are not allowed to be added to a cart for printed copy requests. CMS users with permission to edit documents can edit a document
and check the "Allowed in Cart" checkbox to enable this functionality. This can however also be done in code, just before a
document is added:

```php
$document = DMSDocument::create(array('AllowedInCart' => true));
$document->write();
```

The `AllowedInCart` field is added by the `DMSDocumentCartExtension` extension.
