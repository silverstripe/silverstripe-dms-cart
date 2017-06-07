<?php

/**
 * Class DMSDocumentCartTest contains all the tests for {@link DMSDocumentCart}
 */
class DMSDocumentCartTest extends SapphireTest
{
    protected static $fixture_file = 'DMSDocumentCartTest.yml';

    /**
     * @var DMSDocumentCart
     */
    protected $cart;

    public function setUp()
    {
        parent::setUp();
        $this->cart = singleton('DMSDocumentCart');
    }

    /**
     * @expectedException DMSDocumentCartException
     * @expectedExceptionMessage Backend must implement DMSCartBackendInterface!
     */
    public function testConstructorThrowsExceptionWhenProvidedBackendDoesNotImplementInterface()
    {
        DMSDocumentCart::create(new DMSDocument);
    }

    public function testAddItem()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');

        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());
    }

    public function testEmptyCart()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');

        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(18);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());

        $this->cart->emptyCart();
        $this->assertTrue($this->cart->isCartEmpty());
    }

    public function testGetBackend()
    {
        $this->assertInstanceOf('DMSSessionBackend', $this->cart->getBackend());
    }

    public function testGetBackURL()
    {
        $url = 'TestURL';
        $this->cart->setBackUrl($url);
        $this->assertEquals($url, $this->cart->getBackUrl());
    }

    public function testGetItem()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');

        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);

        $this->assertEquals($doc, $this->cart->getItem($doc->ID)->getDocument());
    }

    public function testGetItems()
    {
        $item = DMSRequestItem::create();
        $item->setDocument($this->objFromFixture('DMSDocument', 'doc1'))->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertCount(1, $this->cart->getItems());

        $item2 = DMSRequestItem::create();
        $item2->setDocument($this->objFromFixture('DMSDocument', 'limited_supply'))->setQuantity(2);
        $this->cart->addItem($item2);
        $this->assertCount(2, $this->cart->getItems());

        // Edge case handling for when a document is deleted in the background
        $item2->getDocument()->delete();
        $this->assertCount(1, $this->cart->getItems());
    }

    public function testGetReceiverInfo()
    {
        $info = array('Name' => 'Jane');
        $this->cart->setReceiverInfo($info);
        $this->assertEquals($info, $this->cart->getReceiverInfo());
    }

    public function testIsCartEmpty()
    {
        $this->assertTrue($this->cart->isCartEmpty());
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        $item = DMSRequestItem::create()->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());
    }

    public function testRemoveItem()
    {
        $this->assertTrue($this->cart->isCartEmpty());
        $doc = $this->objFromFixture('DMSDocument', 'doc1');

        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());

        $this->cart->removeItem($item);
        $this->assertTrue($this->cart->isCartEmpty());
    }

    public function testRemoveItemByID()
    {
        $this->assertTrue($this->cart->isCartEmpty());
        $doc = $this->objFromFixture('DMSDocument', 'doc1');

        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());

        $this->cart->removeItemByID($doc->ID);
        $this->assertTrue($this->cart->isCartEmpty());
    }

    public function testUpdateItemQuantity()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');

        // Test Update
        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertEquals(2, $this->cart->getItem($doc->ID)->getQuantity());

        $this->cart->updateItemQuantity($item->getItemID(), 17);
        $this->assertEquals(19, $this->cart->getItem($doc->ID)->getQuantity());

        // Test Add
        $this->cart->updateItemQuantity($doc->ID, 16);
        $this->assertEquals(35, $this->cart->getItem($doc->ID)->getQuantity());

        // Test Deduct
        $this->cart->updateItemQuantity($doc->ID, -16);
        $this->assertEquals(19, $this->cart->getItem($doc->ID)->getQuantity());

        // Test deduct all items that it removes it
        $this->cart->updateItemQuantity($doc->ID, -19);
        $this->assertTrue($this->cart->isCartEmpty());
    }

    public function testSaveSubmission()
    {
        $controller = DMSCheckoutController::create();

        // Form for use later
        $form = $controller->DMSDocumentRequestForm();
        $doc = $this->objFromFixture('DMSDocument', 'doc1');

        // Add some an item to the cart to assert later that its empty
        $item = DMSRequestItem::create($doc)->setQuantity(15);
        $controller->getCart()->addItem($item);

        $submissionID = $controller->getCart()->saveSubmission($form);
        $submission = DMSDocumentCartSubmission::get()->byID($submissionID);
        $this->assertEquals(15, $submission->Items()->first()->Quantity);
    }


    public function testIsInCart()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        $item = DMSRequestItem::create()->setDocument($doc)->setQuantity(2);
        $this->assertFalse($this->cart->isInCart($item->getItemId()));

        $this->cart->addItem($item);
        $this->assertTrue($this->cart->isInCart($item->getItemId()));
    }

    /**
     * Ensure that the cart contents are hashed an used for the cart summary cache key
     */
    public function testGetCartSummaryCacheKey()
    {
        $item = DMSRequestItem::create($this->objFromFixture('DMSDocument', 'doc1'))->setQuantity(2);
        $item2 = DMSRequestItem::create($this->objFromFixture('DMSDocument', 'limited_supply'))->setQuantity(2);

        $this->cart->addItem($item)->addItem($item2);

        $hash = md5(serialize($this->cart->getItems()));
        $this->assertContains($hash, $this->cart->getCartSummaryCacheKey());
    }
}
