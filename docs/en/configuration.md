# Configuration

## Setting the bcc recipient email addresses
You can change the default list of email recipients using YAML configuration as follow:
```yaml
DMSCheckoutController:
  recipient_emails: []
```

## Enabling ability for a Document to be added to cart
By default new documents are not visible within the cart. Content Authors will have to edit a document
and check the `Allowed in Cart` checkbox via the CMS. This can however also be done in code, just before a 
document is added:

```php
$doc = DMSDocument::create(array('AllowedInCart'=>true));
$doc->write();
```

The AllowedInCart field is added by the [DMSDocumentCartExtension.php](https://github.com/creative-commoners/silverstripe-dms-cart/blob/master/code/extensions/DMSDocumentCartExtension.php) extension.

## 

