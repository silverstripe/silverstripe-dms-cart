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
        'CreatedAt'               => 'Datetime',
    );

    private static $has_many = array(
        'Items' => 'DMSDocumentCartSubmissionItem',
    );

    private static $summary_fields = array(
        'ReceiverName' => 'Receiver Name',
        'ReceiverPhone' => 'Receiver Phone',
        'ReceiverEmail' => 'Receiver Email',
        'Items.Count' => 'No. Items',
        'CreatedAt.Nice' => 'Created At'
    );

    private static $singular_name = 'Cart Submission';
    private static $plural_name = 'Cart Submissions';

    /**
     * Removing the add and existing GridField components to ensure that the model admin for submissions doesn't
     * let you add new records
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // PHP 5.3 support
        $self = $this;
        $this->beforeUpdateCMSFields(function (FieldList $fields) use ($self) {
            $fields->addFieldToTab(
                'Root.Main',
                $fields->fieldByName('Root.Main.CreatedAt')->performReadonlyTransformation()
            );

            $gridField = GridField::create('Items', null, $self->Items(), $config = new GridFieldConfig_RecordEditor);
            $fields->addFieldToTab('Root.Items', $gridField);

            foreach (array('GridFieldAddExistingAutocompleter', 'GridFieldAddNewButton') as $component) {
                $config->removeComponentsByType($component);
            }
        });
        return parent::getCMSFields();
    }

    /**
     * Set the created at datetime if it hasn't been set already
     */
    public function onBeforeWrite()
    {
        if (!$this->CreatedAt) {
            $this->CreatedAt = SS_Datetime::now();
        }
        return parent::onBeforeWrite();
    }
}
