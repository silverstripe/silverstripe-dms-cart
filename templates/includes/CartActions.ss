<% if $isAllowedInCart %>
    <% require css('dms-cart/css/dms-cart.css') %>
    <div class="dms-cart-actions">
        <% if $getValidationResult %>
            <form>
                <p class="message dms-cart-actions-messages bad">$getValidationResult</p>
            </form>
        <% end_if %>
        <p>
            <% if $isInCart %>
                <a class="dms-cart-actions-removelink" href="$getActionLink('remove')">
                    <%t DMSCart.REMOVE_FROM_CART "Remove from cart" %>
                </a>
                <a class="dms-cart-actions-viewcartlink" href="$getActionLink('checkout')"
                   title="<%t DMSCart.VIEW_MY_CART "View my Document cart" %>"
                ><%t DMSCart.VIEW_MY_CART "View my Document cart" %></a>
            <% else %>
                <a class="dms-cart-actions-addlink" href="$getActionLink('add')">
                    <%t DMSCart.ADD_TO_CART "Request a printed copy" %>
                </a>
            <% end_if %>
        </p>
    </div>
<% end_if %>
