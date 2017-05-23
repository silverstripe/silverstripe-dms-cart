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

    public function testAddItem()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var DMSRequestItem $item */
        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());
    }

    public function testEmptyCart()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var DMSRequestItem $item */
        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(18);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());

        $this->cart->emptyCart();
        $this->assertTrue($this->cart->isCartEmpty());
    }

    public function testGetBackend()
    {
        /** @var DMSDocumentCart $cart */
        $this->assertEquals('DMSSessionBackend', get_class($this->cart->getBackend()));
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
        /** @var DMSRequestItem $item */
        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);

        $this->assertEquals($doc, $this->cart->getItem($doc->ID)->getDocument());
    }

    public function testGetItems()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var DMSRequestItem $item */
        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);

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
        /** @var DMSRequestItem $item */
        $item = DMSRequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());
    }

    public function testRemoveItem()
    {
        $this->assertTrue($this->cart->isCartEmpty());
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var DMSRequestItem $item */
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
        /** @var DMSRequestItem $item */
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
        /** @var DMSRequestItem $item */
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
}
