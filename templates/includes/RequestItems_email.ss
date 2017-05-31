<table title="<%t DMSCart.TABLE_TITLE "Requested documents" %>" style="text-align:left; width:100%;">
    <thead>
    <tr>
        <th>
            <%t DMSCart.TITLE "Title" %>
        </th>
        <th>
            <%t DMSCart.FILE_NAME "File name" %>
        </th>
        <th>
            <%t DMSCart.TOTAL_COPIES "# of copies" %>
        </th>
        <th>
            <%t DMSCart.TYPE "Type" %>
        </th>
        <th>
            <%t DMSCart.SIZE "Size" %>
        </th>
        <th>
            <%t DMSCart.URL "URL" %>
        </th>
    </tr>
    </thead>
    <tbody>
        <% loop $Items %>
        <tr>
            <% with $Document %>
                <td>$Title</td>
                <td>$FilenameWithoutID</td>
                <td>$Up.Quantity</td>
                <td>$Extension.UpperCase</td>
                <td>$Size</td>
                <td>
                    <a href="$Link"><%t DMSCart.VIEW_DOCUMENT "View Document" %></a>
                </td>
            <% end_with %>
        </tr>
        <% end_loop %>
    </tbody>
</table>
