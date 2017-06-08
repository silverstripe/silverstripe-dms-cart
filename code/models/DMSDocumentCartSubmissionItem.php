<?php

class DMSDocumentCartSubmissionItem extends DataObject
{
    private static $db = array(
        'OriginalID' => 'Int',
        'Title' => 'Varchar(255)',
        'Filename' => 'Varchar(255)',
        'Quantity' => 'Int',
    );

    private static $has_one = array(
        'DMSDocumentCartSubmission' => 'DMSDocumentCartSubmission',
    );

    private static $summary_fields = array('Title', 'Filename', 'Quantity');

    private static $singular_name = 'Submission Item';
    private static $plural_name = 'Submission Items';

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('DMSDocumentCartSubmissionID');

            $fields->addFieldToTab(
                'Root.Main',
                $fields->fieldByName('Root.Main.OriginalID')
                    ->performReadonlyTransformation()
                    ->setDescription(_t(
                        'DMSDocumentCartSubmissionItem.ORIGINALIDHELPTIP',
                        'Note: The original document may have been modified or removed since this request was made.'
                    )),
                'Title'
            );
        });
        return parent::getCMSFields();
    }
}
