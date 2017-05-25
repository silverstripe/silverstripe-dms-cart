<% if $isAllowedInCart %>
    <% require css('dms-cart/css/dms-cart.css') %>
    <div class="dms-cart-actions">
        <p>
            <a class="<% if not $isInCart %>hidden<% end_if %>"
               href="$getActionLink('remove')"><%t DMSCart.REMOVE_FROM_CART "Remove from cart" %> </a>
            <a class="<% if $isInCart %>hidden<% end_if %>"
               href="$getActionLink('add')"><%t DMSCart.ADD_TO_CART "Request a printed copy" %> </a>
            <% if $isInCart %>
                <a class="dms-bullet-item <% if not $isInCart %>hidden<% end_if %>" href="$getActionLink('checkout')"
                   title="<%t DMSCart.VIEW_MY_CART "View my Document cart" %>"
                ><%t DMSCart.VIEW_MY_CART "View my Document cart" %></a>
            <% end_if %>
        </p>
    </div>
<% end_if %>
