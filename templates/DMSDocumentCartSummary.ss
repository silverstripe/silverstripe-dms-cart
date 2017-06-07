<% cached 'cartsummary', $getCartSummaryCacheKey, $IncludeFormTag, $isViewOnly %>
    <% if $IncludeFormTag %>
        <form $AttributesHTML>
    <% end_if %>
        <table title="<%t DMSDocumentCart.REQUESTED_ITEMS "Requested documents" %>">
            <tbody>
                <% loop $Items %>
                <tr>
                    <% with $Document %>
                        <td>
                            <div class="checkout-page-thumbnail">
                                <% if $CoverImage %>
                                    <img class="thumbnail" src="$CoverImage.FitMax(32,32).Link"/>
                                <% else %>
                                    <img class="thumbnail" src="$Icon($Extension)"/>
                                <% end_if %>
                            </div>
                        </td>
                        <td>$Title</td>

                        <% if $Up.Up.isViewOnly %>
                            <td>
                                <label for="ItemQuantity[{$ID}]"
                                       class="visuallyhidden"><%t DMSDocumentCart.ITEM_QUANTITY "Item Quantity" %></label>
                                <input min="1" type='text' id="ItemQuantity[{$ID}]" name="ItemQuantity[{$ID}]"
                                       value="$Up.Quantity"/>
                            </td>
                            <td><a class="docCart-remove-link" href="$getActionLink('remove')"
                                   title="Remove item"><%t DMSDocumentCart.REMOVE_ITEM "X" %></a></td>
                        <% else %>
                            <td>
                                $Up.Quantity
                            </td>
                        <% end_if %>

                    <% end_with %>
                </tr>
                <% end_loop %>
            </tbody>
        </table>
        <div class="Actions">
            <% if $isViewOnly %>
                <% if $Actions  %>
                    <% loop $Actions %>
                        $Field
                    <% end_loop %>
                <% end_if %>
            <% else %>
                <a class="action" href="{$getLink('view')}"><%t DMSDocumentCart.UPDATE_CART "Update cart" %></a>
                <div class="clear"></div>
            <% end_if %>
        </div>
    <% if $IncludeFormTag %>
        </form>
    <% end_if %>
<% end_cached %>
