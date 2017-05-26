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
}
