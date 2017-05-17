<?php

class DMSDocumentCartSubmissionItem extends DataObject
{
    private static $db = array(
        'Quantity' => 'Int',
    );

    private static $has_one = array(
        'Document'                  => 'DMSDocument',
        'DMSDocumentCartSubmission' => 'DMSDocumentCartSubmission',
    );
}
