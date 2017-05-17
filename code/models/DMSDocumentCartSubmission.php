<?php

class DMSDocumentCartSubmission extends DataObject
{
    private static $db = array(
        'ReceiverName'            => 'Varchar(100)',
        'ReceiverPhone'           => 'Varchar(20)',
        'ReceiverEmail'           => 'Varchar(254)',
        'DeliveryAddressLine1'    => 'Varchar(200)',
        'DeliveryAddressLine2'    => 'Varchar(200)',
        'DeliveryAddressCountry'  => 'Varchar(50)',
        'DeliveryAddressPostCode' => 'Varchar(20)',
    );

    private static $has_many = array(
        'Items' => 'DMSDocumentCartSubmissionItem',
    );
}
