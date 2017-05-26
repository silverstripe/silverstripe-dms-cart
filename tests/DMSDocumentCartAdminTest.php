<?php

class DMSDocumentCartAdminTest extends SapphireTest
{
    /**
     * Ensure that the "add new" button has been removed
     */
    public function testNoAddButtonInGridField()
    {
        $modelAdmin = new DMSDocumentCartAdmin;
        $modelAdmin->setRequest(new SS_HTTPRequest('GET', '/'));
        $modelAdmin->init();

        $fields = $modelAdmin->getEditForm()->Fields();
        $this->assertInstanceOf('GridField', $fields->first());
        $config = $fields->first()->getConfig();
        $this->assertNull($config->getComponentByType('GridFieldAddNewButton'));
    }
}
