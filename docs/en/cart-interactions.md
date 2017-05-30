# Cart interactions

All the below interactions supports both direct HTTP POST and GET from a controller as well as AJAX requests.

1. If posted via AJAX, a JSON response is returned containing a `result` (true or false depending on whether call was successful),
and `message` (containing an appropriate message if the result failed).
2. If sent from a controller with no `BackURL` supplied it simply redirects back to the previous page, otherwise it redirects to the supplied `BackURL`.

## Adding a document to the cart

To add a document, simply post to `/documentcart/add/{$ID}?quantity={$quantity}`, where `$ID` is the DMSDocument's ID
and `$quantity` is the number of copies to add (default 1).

Via the public API:

```php
$document = DMSDocument::get()->byId(123);
$cart = DMSDocumentCart::singleton();
$cart->addItem($document);
```

## Removing a document from the cart

To remove a document, simply post to `/documentcart/remove/{$ID}`, where `$ID` is the DMSDocument's ID.

## To view/update your cart summary

To view or update your cart summary, simply point your users to `/checkout`. This displays an updatable list as well as
an extensible input form for the recipient's information.

When this form is posted, it creates a `DMSDocumentCartSubmission` that represents the form submission, along with an associated list of `DMSDocumentCartSubmissionItem` models which represent the documents requested.

## Empty the cart

You can empty the cart using `emptyCart`:

```php
$cart = DMSDocumentCart::singleton();
$cart->emptyCart();
```

You can also check whether the cart is empty using `isCartEmpty`:

```php
if (DMSDocumentCart::singleton()->isCartEmpty()) {
    // ...
}
```
