<?php

class DMSRequestItemTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testConstructorAcceptsDocument()
    {
        $document = DMSDocument::create(array('Title' => 'Test'));
        $document->write();

        // Ensure it's empty by default
        $this->assertNull(DMSRequestItem::create()->getDocument());

        // It can be set in the constructor
        $requestItem = DMSRequestItem::create($document);
        $this->assertEquals($document->ID, $requestItem->getDocument()->ID);
    }
}
