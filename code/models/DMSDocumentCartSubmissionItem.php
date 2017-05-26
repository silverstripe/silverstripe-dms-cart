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

    private static $summary_fields = array(
        'Document.getTitle' => 'Document',
        'Quantity' => 'Quantity'
    );

    private static $singular_name = 'Submission Item';
    private static $plural_name = 'Submission Items';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('DMSDocumentCartSubmissionID');
        return $fields;
    }
}
