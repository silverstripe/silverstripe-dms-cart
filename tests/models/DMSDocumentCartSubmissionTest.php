<?php

class DMSDocumentCartSubmissionTest extends SapphireTest
{
    protected $usesDatabase = true;

    /**
     * Test that the add new and existing GridField components have been removed
     */
    public function testNoAddNewOrExistingButtons()
    {
        $fields = DMSDocumentCartSubmission::create()->getCMSFields();
        $gridField = $fields->fieldByName('Root.Items.Items');
        $this->assertInstanceOf('GridField', $gridField);

        $config = $gridField->getConfig();
        $this->assertNull($config->getComponentByType('GridFieldAddExistingAutocompleter'));
        $this->assertNull($config->getComponentByType('GridFieldAddNewButton'));
    }

    /**
     * Ensure that a "created at" date is set when a record is created
     */
    public function testCreatedAtIsSetOnWrite()
    {
        $submission = DMSDocumentCartSubmission::create();
        $this->assertNull($submission->CreatedAt);

        $submission->write();
        $this->assertNotNull($submission->CreatedAt);
    }

    /**
     * Ensure that the scaffolded "created at" is readonly in the CMS
     */
    public function testCreatedAtIsReadonly()
    {
        $submission = DMSDocumentCartSubmission::create();
        $submission->write();
        $fields = $submission->getCMSFields();
        $this->assertInstanceOf('DatetimeField_Readonly', $fields->fieldByName('Root.Main.CreatedAt'));
    }
}
