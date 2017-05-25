<?php

class DMSDocumentCartExtensionTest extends SapphireTest
{
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
}
