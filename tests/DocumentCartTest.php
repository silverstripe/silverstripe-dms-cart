<?php

/**
 * Class DocumentCartTest
 */
class DocumentCartTest extends SapphireTest
{

    protected static $fixture_file = 'DocumentCartTest.yml';

    /**
     * @var DocumentCart
     */
    protected $cart;

    public function setUp()
    {
        parent::setUp();
        $this->cart = singleton('DocumentCart');
    }


    public function testAddItem()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var RequestItem $item */
        $item = RequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());
    }

    public function testAddItemQuantity()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var RequestItem $item */
        $item = RequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);

        $this->assertEquals(2, $item->getQuantity());
        $this->cart->addItemQuantity($doc->ID, 16);
        $this->assertEquals(18, $this->cart->getItem($doc->ID)->getQuantity());
    }

    public function testDeductItemQuantity()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var RequestItem $item */
        $item = RequestItem::create();
        $item->setDocument($doc)->setQuantity(18);
        $this->cart->addItem($item);

        $this->assertEquals(18, $item->getQuantity());
        $this->cart->deductItemQuantity($doc->ID, 16);
        $this->assertEquals(2, $this->cart->getItem($doc->ID)->getQuantity());
    }

    public function testEmptyCart()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var RequestItem $item */
        $item = RequestItem::create();
        $item->setDocument($doc)->setQuantity(18);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());

        $this->cart->emptyCart();
        $this->assertTrue($this->cart->isCartEmpty());
    }

    public function testGetBackend()
    {
        /** @var DocumentCart $cart */
        $this->assertEquals('DMSSessionBackend', get_class($this->cart->getBackend()));
    }

    public function testGetBackURL()
    {
        $url = 'TestURL';
        $this->cart->setBackURL($url);
        $this->assertEquals($url, $this->cart->getBackURL());
    }

    public function testGetItem()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var RequestItem $item */
        $item = RequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);

        $this->assertEquals($doc, $this->cart->getItem($doc->ID)->getDocument());
    }

    public function testGetItems()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var RequestItem $item */
        $item = RequestItem::create();
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
        /** @var RequestItem $item */
        $item = RequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());
    }

    public function testRemoveItem()
    {
        $this->assertTrue($this->cart->isCartEmpty());
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var RequestItem $item */
        $item = RequestItem::create();
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
        /** @var RequestItem $item */
        $item = RequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertFalse($this->cart->isCartEmpty());

        $this->cart->removeItemByID($doc->ID);
        $this->assertTrue($this->cart->isCartEmpty());
    }

    public function testUpdateItemQuantity()
    {
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        /** @var RequestItem $item */
        $item = RequestItem::create();
        $item->setDocument($doc)->setQuantity(2);
        $this->cart->addItem($item);
        $this->assertEquals(2, $this->cart->getItem($doc->ID)->getQuantity());

        $this->cart->updateItemQuantity($item->getItemID(), 17);
        $this->assertEquals(17, $this->cart->getItem($doc->ID)->getQuantity());
    }
}
