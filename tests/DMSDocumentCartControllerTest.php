<?php

/**
 * Class DMSDocumentCartControllerTest contains all the tests for {@link DMSDocumentCartController}
 */
class DMSDocumentCartControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'DMSDocumentCartTest.yml';

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
        $this->assertInstanceOf('ArrayList', $this->controller->Items(), 'DMSDocumentCartController->Items() returned an ArrayList');
        $this->assertCount(
            2,
            $this->controller->Items(),
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

        $item = $this->cart->getItem($doc1->ID);
        $this->assertEquals(12, $item->getQuantity());
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
    }

    public function testCart()
    {
        $this->assertInstanceOf('DMSDocumentCart', $this->controller->getCart());
        //For good measure assert it's empty
        $this->assertTrue($this->controller->getIsCartEmpty());
    }
}
