<?php

class DMSDocumentCartExtensionTest extends SapphireTest
{
    protected static $fixture_file = 'DMSDocumentCartExtensionTest.yml';

    protected $requiredExtensions = array(
        'DMSDocument' => array('DMSDocumentCartExtension')
    );

    public function testMaximumCartQuantity()
    {
        $document = DMSDocument::create(array('AllowedInCart' => true, 'MaximumCartQuantity' => ''));

        $this->assertFalse($document->getHasQuantityLimit());

        $document->MaximumCartQuantity = 0;
        $this->assertFalse($document->getHasQuantityLimit());

        $document->MaximumCartQuantity = 1;
        $this->assertTrue($document->getHasQuantityLimit());
        $this->assertSame(1, $document->getMaximumQuantity());

        $document->MaximumCartQuantity = 10;
        $this->assertSame(10, $document->getMaximumQuantity());
    }

    /**
     * The CSS classes are required for the CMS Javascript to work, assert that they are correct
     */
    public function testCmsFieldsHaveRequiredCssClasses()
    {
        $fields = DMSDocument::create()->getCMSFields();

        $allowedInCart = $fields->fieldByName('AllowedInCart');
        $this->assertInstanceOf('CheckboxField', $allowedInCart);
        $this->assertTrue((bool) $allowedInCart->hasClass('dms-allowed-in-cart'));

        $allowedInCart = $fields->fieldByName('MaximumCartQuantity');
        $this->assertInstanceOf('TextField', $allowedInCart);
        $this->assertTrue((bool) $allowedInCart->hasClass('dms-maximum-cart-quantity'));
    }

    /**
     * Ensure that validation messages can be retrieved once, cleared, then not again
     */
    public function testGetValidationResult()
    {
        Session::set('dms-cart-validation-message', 'testing');
        $this->assertSame('testing', DMSDocument::create()->getValidationResult());
        $this->assertFalse(DMSDocument::create()->getValidationResult());
    }

    /**
     * Test that print request count is shown in the document's summary
     */
    public function testPrintRequestCountIsShownInDocumentSummary()
    {
        $document = $this->objFromFixture('DMSDocument', 'requested_for_print');
        $fields = $document->getCMSFields();

        // Results of DMSDocument::getFieldsForFile is the first in the list
        $summary = $fields->first();
        $this->assertInstanceOf('FieldGroup', $summary);

        $printRequestField = $summary->FieldList()
            ->fieldByName('FilePreview.FilePreviewData.FilePreviewDataFields.PrintRequestCount');

        $this->assertNotNull($printRequestField, 'Print request count field exists in DMSDocument fields');
        $this->assertEquals(3, $printRequestField->Value());
    }

    /**
     * @expectedException DMSDocumentCartException
     * @expectedExceptionMessage peanut is not accepted for this method.
     */
    public function testGetActionLinkThrowsExceptionOnInvalidAction()
    {
        DMSDocument::singleton()->getActionLink('Peanut');
    }
}
