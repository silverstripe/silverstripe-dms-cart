# Use in templates

Add an include to your `.ss` template which displays the `DMSDocument` where you would like the
"Request a printed copy" button to be displayed:

```
<% include CartActions %>
```

You can fine tune the HTML markup or display behaviour of any of the templates in `/dms-cart/templates/Includes` to change
the way it will be displayed in your project. When doing so, please copy the template into your `mysite/templates` folder, or your custom theme.

## Checking if a document can be added to cart

You can check whether a document is allowed in the cart:

```
<% loop $getDocuments %>
    <% if $isAllowedInCart %>
        <p>Yes, this document can be requested for a printed copy.</p>
    <% end_if %>
<% end_loop %>
```

## Checking for a quantity limit

If you need to enforce a quantity limit, for example in frontend Javascript or HTML5 validation:

```
<% loop $getDocuments %>
    <% if $getHasQuantityLimit %>
        <p>Quantity limit is $getMaximumQuantity.</p>
    <% end_if %>
<% end_loop %>
```

## Getting a cart action link

Retrieve the URL for adding, removing or navigating to the checkout:

```
<% loop $getDocuments %>
    <ul>
        <li><a href="{$getActionLink('add')}">Add</a></li>
        <li><a href="{$getActionLink('remove')}">Remove</a></li>
    </ul>
<% end_loop %>
<p><a href="{$getActionLink('checkout')}">Checkout</a></p>
```

## Accessing the cart

You can access the cart object from a template, for example to show how many documents are in it already:

```
<p>You have {$getCart.getItems.Count} items in your cart.</p>
```
