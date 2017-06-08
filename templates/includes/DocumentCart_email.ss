<div>
    <% include RequestItems_email %>
    <table style="margin-top:20px; text-align:left; width:100%;">
        <thead>
        <tr>
            <th colspan="2">
                <%t DMSDocumentCartSubmission.DELIVERY_INFORMATION "Delivery Information" %>
            </th>
        </tr>
        </thead>
        <tbody>
            <% with $ReceiverInfoNice %>
            <tr>
                <td>
                    <%t DMSDocumentCartSubmission.RECEIVER_NAME "Name" %>
                </td>
                <td>$ReceiverName.XML</td>
            </tr>
            <tr>
                <td>
                    <%t DMSDocumentCartSubmission.RECEIVER_PHONE "Phone" %>
                </td>
                <td>$ReceiverPhone.XML</td>
            </tr>
            <tr>
                <td>
                    <%t DMSDocumentCartSubmission.RECEIVER_EMAIL "Email" %>
                </td>
                <td>$ReceiverEmail.XML</td>
            </tr>
            <tr>
                <td>
                    <%t DMSDocumentCartSubmission.RECEIVER_ADDRESS "Shipping Address" %>
                </td>
                <td>
                    $DeliveryAddressLine1.XML<br/>
                    $DeliveryAddressLine2.XML<br/>
                    $DeliveryAddressPostCode.XML<br/>
                    $DeliveryAddressCountryLiteral.XML
                </td>
            </tr>
            <% end_with %>
        </tbody>
    </table>
</div>
