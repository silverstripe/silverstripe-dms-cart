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
     * Ensure that the "created" field is readonly in the CMS
     */
    public function testCreatedFieldIsReadonly()
    {
        $submission = DMSDocumentCartSubmission::create();
        $submission->write();
        $fields = $submission->getCMSFields();
        $this->assertInstanceOf('ReadonlyField', $fields->fieldByName('Root.Main.Created'));
    }
}
