<?php

/**
 * Class DMSDocumentCartControllerTest contains all the tests for {@link DMSDocumentCartController}
 */
class DMSDocumentCartControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'dms-cart/tests/DMSDocumentCartTest.yml';

    /**
     * @var DMSDocumentCartController
     */
    protected $controller;

    /**
     * @var DMSDocumentCart
     */
    protected $cart;

    public function setUp()
    {
        parent::setUp();
        DMSDocumentCartController::add_extension('StubDMSDocumentCheckoutPageExtension');
        $this->controller = DMSDocumentCartController::create();
        $this->cart = $this->controller->getCart();
    }

    /**
     * Test the items method of the controller
     */
    public function testItems()
    {
        /** @var DMSDocument $doc1 */
        $doc1 = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var DMSDocument $doc2 */
        $doc2 = $this->objFromFixture('DMSDocument', 'doc2');
        /** @var DMSRequestItem $item */
        $item1 = DMSRequestItem::create()->setDocument($doc1)->setQuantity(2);
        $item2 = DMSRequestItem::create()->setDocument($doc2)->setQuantity(5);
        $this->cart->addItem($item1);
        $this->cart->addItem($item2);
        $this->assertInstanceOf(
            'ArrayList',
            $this->controller->items(),
            'DMSDocumentCartController->Items() returned an ArrayList'
        );
        $this->assertCount(
            2,
            $this->controller->items(),
            'DMSDocumentCartController->Items()->count() returned the requisite number of items'
        );
    }

    public function testReceiverInfo()
    {
        // Now add some info
        $this->cart->setReceiverInfo(array('Name' => 'Joe', 'Surname' => 'Soap'));
        $this->assertCount(2, $this->controller->getReceiverInfo());
    }

    public function testIsCartEmpty()
    {
        $this->assertTrue($this->controller->getIsCartEmpty());
        /** @var DMSDocument $doc */
        $doc1 = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var DMSRequestItem $item */
        $item1 = DMSRequestItem::create()->setDocument($doc1)->setQuantity(2);
        $this->cart->addItem($item1);
        $this->assertFalse($this->controller->getIsCartEmpty());
    }

    public function testAdd()
    {
        $this->logInWithPermission();
        $doc1 = $this->objFromFixture('DMSDocument', 'doc1');
        // Check cart is initially empty
        $this->assertTrue($this->controller->getIsCartEmpty());
        // Now call controller add
        $request = new SS_HTTPRequest('POST', '', array(), array('quantity' => 5));
        $request->setRouteParams(array('ID' => $doc1->ID));
        $this->controller->add($request);
        $this->assertFalse($this->controller->getIsCartEmpty());

        // Do it again and assert quantity was updated
        $request = new SS_HTTPRequest('POST', '', array(), array('quantity' => 7));
        $request->setRouteParams(array('ID' => $doc1->ID));
        $this->controller->add($request);

        // Test ajax
        $request = new SS_HTTPRequest('POST', '', array(), array('quantity' => 7, 'ajax' => 1));
        $request->setRouteParams(array('ID' => $doc1->ID));
        $response = $this->controller->add($request);
        $this->assertTrue($request->isAjax());
        $this->assertJson($response, 'Confirmed that an ajax call to add() responded with json JSON');

        $item = $this->cart->getItem($doc1->ID);
        $this->assertEquals(19, $item->getQuantity());
    }



    public function testDeduct()
    {
        $doc1 = $this->objFromFixture('DMSDocument', 'doc1');
        // Check catty is initially empty
        $this->assertTrue($this->controller->getIsCartEmpty());
        // Now call controller add
        $request = new SS_HTTPRequest('POST', '', array(), array('quantity'=>5));
        $request->setRouteParams(array('ID' => $doc1->ID));
        $this->controller->deduct($request);
        // Assert cart still empty because item doesn't exist
        $this->assertTrue($this->controller->getIsCartEmpty());
        // Now add item
        $this->controller->add($request);
        $this->assertFalse($this->controller->getIsCartEmpty());


        // Now try and deduct 2 from the items
        $request = new SS_HTTPRequest('POST', '', array(), array('quantity' => -2));
        $request->setRouteParams(array('ID' => $doc1->ID));
        $this->controller->deduct($request);

        $item = $this->cart->getItem($doc1->ID);
        $this->assertEquals(3, $item->getQuantity());

        // Test ajax
        $request = new SS_HTTPRequest('POST', '', array(), array('quantity' => 7, 'ajax' => 1));
        $request->setRouteParams(array('ID' => $doc1->ID));
        $response = $this->controller->deduct($request);
        $this->assertTrue($request->isAjax());
        $this->assertJson($response, 'Confirmed that an ajax call to deduct() method responded with JSON');
    }

    public function testRemove()
    {
        $doc1 = $this->objFromFixture('DMSDocument', 'doc1');
        // Check catty is initially empty
        $this->assertTrue($this->controller->getIsCartEmpty());
        // Now call controller add
        $request = new SS_HTTPRequest('POST', '', array(), array('quantity'=>5));
        $request->setRouteParams(array('ID' => $doc1->ID));
        // Now add item
        $this->controller->add($request);
        $this->assertFalse($this->controller->getIsCartEmpty());
        $this->controller->remove($request);
        $this->assertTrue($this->controller->getIsCartEmpty());

        // Test ajax
        $request = new SS_HTTPRequest('POST', '', array(), array('quantity' => 7, 'ajax' => 1));
        $request->setRouteParams(array('ID' => $doc1->ID));
        $response = $this->controller->remove($request);
        $this->assertTrue($request->isAjax());
        $this->assertJson($response, 'Confirmed that  an ajax call to remove() method responded with JSON');
    }

    /**
     * Ensure that a validation error is shown when requesting to add more of a document that is allowed
     */
    public function testCannotAddMoreThanSuggestedQuantityOfItem()
    {
        $document = $this->objFromFixture('DMSDocument', 'limited_supply');
        $result = $this->get('/documentcart/add/' . $document->ID . '?quantity=5&ajax=1');
        $this->assertContains(
            'Maximum of 3 documents exceeded for \"Doc3\", please select a lower quantity.',
            (string) $result->getBody()
        );
    }

    /**
     * Ensure that multiple validation errors are returned in the failure message, if any
     */
    public function testMultipleValidationErrorsReturned()
    {
        $document1 = $this->objFromFixture('DMSDocument', 'limited_supply');
        $document2 = $this->objFromFixture('DMSDocument', 'very_limited_supply');

        $this->cart
            ->addItem(DMSRequestItem::create($document1))
            ->addItem(DMSRequestItem::create($document2));

        $input = array('ItemQuantity' => array(
            $document1->ID => 15000,
            $document2->ID => 12000
        ));

        $form = Form::create($this->controller, '', new FieldList, new FieldList);
        $result = $this->controller->updateCartItems($input, $form, new SS_HTTPRequest('POST', '/'));

        $this->assertContains('Maximum of 3 documents exceeded for "Doc3"', $form->Message());
        $this->assertContains('Maximum of 2 documents exceeded for "Doc5"', $form->Message());
    }

    /**
     * Ensure that when a document that cannot be added to the cart is added to the cart, a validation error is
     * returned
     */
    public function testValidationErrorReturnedOnInvalidAdd()
    {
        $document = $this->objFromFixture('DMSDocument', 'not_allowed_in_cart');
        $result = $this->get('/documentcart/add/' . $document->ID . '?ajax=1');
        $this->assertContains('You are not allowed to add this document', (string) $result->getBody());
    }

    /**
     * Tests whether the cart items are updated from the controller
     */
    public function testUpdateCartItems()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var DMSRequestItem $item */
        $item = DMSRequestItem::create()->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);

        $invalidQuantities = array(
            'ItemQuantity' => array($doc->ID => 'non-numeric')
        );

        $sameQuantities = array(
            'ItemQuantity' => array($doc->ID => 2)
        );

        $updatedQuantities = array(
            'ItemQuantity' => array($doc->ID => 5),
        );
        $form = Form::create(
            $this->controller,
            'Test',
            FieldList::create(),
            FieldList::create()
        );
        $request = new SS_HTTPRequest('POST', '');
        // Test invalids leave it unchanged
        $response = $this->controller->updateCartItems($invalidQuantities, $form, $request);
        $this->assertEquals(2, $this->cart->getItem($item->getItemId())->getQuantity());

        // Test quantity remains the same
        $response->removeHeader('Location');
        $response =$this->controller->updateCartItems($sameQuantities, $form, $request);
        $this->assertEquals(2, $this->cart->getItem($item->getItemId())->getQuantity());

        $response->removeHeader('Location');
        $response = $this->controller->updateCartItems($updatedQuantities, $form, $request);
        $this->assertEquals(5, $this->cart->getItem($item->getItemId())->getQuantity());
    }

    /**
     * Tests DMSCartEditForm form has a FieldList
     */
    public function testDMSCartEditForm()
    {
        $form = $this->controller->DMSCartEditForm();
        $this->assertInstanceOf('FieldList', $form->Fields());
    }

    /**
     * Tests if the DMSCartEditForm is extensible
     */
    public function testDMSCartEditFormIsExtensible()
    {
        $controller = $this->controller;
        $form = $controller->DMSCartEditForm();
        $this->assertNotNull(
            $form->Fields()->fieldByName('NewTextField'),
            'DMSDocumentRequestForm() is extensible as it included the field from the extension'
        );
    }

    /**
     * Tests that the cart summary view is returned.
     */
    public function testView()
    {
        $result = $this->get('documentcart/view');
        $this->assertInstanceOf('SS_HTTPResponse', $result);
        $this->assertContains('Updating cart items', $result->getBody());
    }

    /**
     * Ensure the link is "friendly", not a class name
     */
    public function testLink()
    {
        $this->assertSame('documentcart', $this->controller->Link());
        $this->assertSame('documentcart/view', $this->controller->Link('view'));
    }
}
