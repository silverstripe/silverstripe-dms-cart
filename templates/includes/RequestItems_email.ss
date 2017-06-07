<table title="<%t DMSDocumentCartSubmissionItem.TABLE_TITLE "Requested documents" %>" style="text-align:left; width:100%;">
    <thead>
    <tr>
        <th>
            <%t DMSDocumentCartSubmissionItem.TITLE "Title" %>
        </th>
        <th>
            <%t DMSDocumentCartSubmissionItem.FILE_NAME "File name" %>
        </th>
        <th>
            <%t DMSDocumentCartSubmissionItem.TOTAL_COPIES "# of copies" %>
        </th>
    </tr>
    </thead>
    <tbody>
        <% loop $Items %>
        <tr>
            <% with $getDocument %>
            <td>$Title</td>
            <td>$FilenameWithoutID</td>
            <% end_with %>
            <td>$Quantity</td>
        </tr>
        <% end_loop %>
    </tbody>
</table>
