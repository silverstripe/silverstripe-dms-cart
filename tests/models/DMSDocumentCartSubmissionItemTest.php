<?php

class DMSDocumentCartSubmissionItemTest extends SapphireTest
{
    /**
     * Test that the default parent "submission" ID field is gone
     */
    public function testNoParentIdField()
    {
        $fields = DMSDocumentCartSubmissionItem::create()->getCMSFields();
        $this->assertNull($fields->fieldByName('DMSDocumentCartSubmissionID'));
    }

    /**
     * The "original ID" field points to the initial ID of the document that was ordered at the time it was placed.
     * This can easily change, so test that we have a helptip to say this. It should also be read only.
     */
    public function testOriginalIdFieldHasHelptipAndIsReadonly()
    {
        $field = DMSDocumentCartSubmissionItem::create()->getCMSFields()->fieldByName('Root.Main.OriginalID');
        $this->assertInstanceOf('ReadonlyField', $field);
        $this->assertContains('The original document may have been modified', $field->getDescription());
    }
}
