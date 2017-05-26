<?php

class DMSDocumentCartSubmissionTest extends SapphireTest
{
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
}
