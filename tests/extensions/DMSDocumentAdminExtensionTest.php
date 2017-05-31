<?php

class DMSDocumentAdminExtensionTest extends FunctionalTest
{
    protected $requiredExtensions = array(
        'DMSDocumentAdmin' => array('DMSDocumentAdminExtension')
    );

    public function setUp()
    {
        parent::setUp();
        $this->logInWithPermission('ADMIN');
    }

    /**
     * Ensure that the cart submission tab is available in the DMS model admin
     */
    public function testHasCartSubmissionsTab()
    {
        $response = $this->get('admin/documents');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Cart Submission', (string) $response->getBody());
    }

    /**
     * Ensure that the "add new" button has been removed
     */
    public function testNoAddButtonInGridField()
    {
        $response = $this->get('admin/documents/DMSDocumentCartSubmission');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotContains('Add Cart Submission', (string) $response->getBody());
    }
}
