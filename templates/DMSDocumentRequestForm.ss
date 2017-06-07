<% include DMSCartNavigation %>
<% if $Controller.Cart.Items.Count %>

    <% if $IncludeFormTag %>
        <form $AttributesHTML>
    <% end_if %>
    <% include DMSCartNotification %>
    $Controller.Cart.Summary
        <hr/>

        <fieldset>
        <% if $Legend %>
            <legend>$Legend</legend><% end_if %>
        <% loop $Fields %>
            $FieldHolder
        <% end_loop %>

        <div class="clear"><!-- --></div>
    </fieldset>

    <% if $Actions  %>
        <div class="Actions">
            <% loop $Actions %>
                $Field
            <% end_loop %>
        </div>
    <% end_if %>
<% else %>
    <p class="dms-cart-empty"><%t DMSDocumentCart.EMPTY_CART "Your cart is currently empty." %></p>
<% end_if %>
<% if $IncludeFormTag %>
    </form>
<% end_if %>
