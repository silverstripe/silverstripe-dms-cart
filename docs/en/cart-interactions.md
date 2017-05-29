# Cart interactions
All the below interactions supports both direct HTTP POST/GET from a controller as well as Ajax requests.
1. If posted via ajax, a json response is returned containing a `result` (true or false depending on whether call was successful),
and `message` (containing an appropriate message if the result failed).
2. If sent from a controller with no `BackURL` supplied, it simply redirects back, else it redirects to supplied `BackURL`

## Adding a document to the DMSCart
To add a document, simply post to `<your-site-url>/documentcart/add/{$ID}?quantity={$quantity}`, where $ID is the Document's ID
and $quantity = the number of copies to add ($quantity is optional and should defualt to 1 copy if omitted).

## Removing a document from the cart
To remove a document, simply post to `<your-site-url>/documentcart/remove/{$ID}`, where $ID is the Document's ID.

## To view/update your cart summary
To view/update your cart summary, simply point your users to `<your-site-url>checkout`. This displays an updatable list as well as
an extensible input form for the recipient's information.
