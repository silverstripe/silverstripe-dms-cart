<% if $IncludeFormTag %>
    <form $AttributesHTML>
<% end_if %>
<% if $Message %>
        <p id="{$FormName}_error" class="message $MessageType">$Message</p>
<% else %>
        <p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
<% end_if %>
    <a id='continueBrowsing' class='button alt' href='$Controller.getCart.BackUrl()'>
        <%t DMSDocumentCart.CONTINUE_BROWSING "Continue browsing" %></a>
<% if $Controller.Cart.Items.Count %>
        <fieldset>
            <% if $Legend %>
                <legend>$Legend</legend><% end_if %>
            <div>
                <h4><%t DMSDocumentCart.REQUEST_FORM_HEADING "Your request in summary"  %></h4>
            </div>
            <% with $Controller.getCart %>
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
                                <td>
                                    <label for="ItemQuantity[{$ID}]" class="visuallyhidden"><%t DMSDocumentCart.ITEM_QUANTITY "Item Quantity" %></label>
                                    <input min="1" type='text' id="ItemQuantity[{$ID}]" name="ItemQuantity[{$ID}]"
                                           value="$Up.Quantity"/>
                                </td>
                                <td><a class="docCart-remove-link" href="$getActionLink('remove')"
                                       title="Remove item"><%t DMSDocumentCart.REMOVE_ITEM "X" %></a></td>
                            <% end_with %>
                        </tr>
                        <% end_loop %>
                    </tbody>
                </table>
            <% end_with %>
            <hr/>
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
<% end_if %>
<% if $IncludeFormTag %>
    </form>
<% end_if %>
