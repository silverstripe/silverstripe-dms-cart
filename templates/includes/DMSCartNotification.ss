<% if $Message %>
    <p id="{$FormName}_error" class="message $MessageType">$Message</p>
<% else %>
    <p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
<% end_if %>
