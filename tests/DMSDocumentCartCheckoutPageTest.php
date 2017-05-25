<?php

class DMSDocumentCartCheckoutPageTest extends FunctionalTest
{
    protected static $fixture_file = 'DMSDocumentCartTest.yml';
    /**
     * @var DMSDocumentCartCheckoutPage_Controller
     */
    protected $controller;

    /**
     * @var DMSDocumentCart
     */
    protected $cart;

    /**
     * @var DMSDocumentCartCheckoutPage
     */
    protected $page;

    public function setUp()
    {
        parent::setUp();
        $this->page = $this->objFromFixture('DMSDocumentCartCheckoutPage', 'page1');
        DMSDocumentCartCheckoutPage_Controller::add_extension('StubDMSDocumentCheckoutPageExtension');
        Injector::inst()->registerService(new StubEmail(), 'Email');
        $this->controller = ModelAsController::controller_for($this->page);
        $this->cart = $this->controller->getCart();
    }

    /**
     * Tests DMSDocumentRequest form has a FieldList
     */
    public function testDMSDocumentRequestForm()
    {
        $form = $this->controller->DMSDocumentRequestForm();
        $this->assertInstanceOf('FieldList', $form->Fields());
    }

    /**
     * Tests if the DMSDocumentRequestForm is extensible
     */
    public function testDMSDocumentRequestFormIsExtensible()
    {
        $controller = $this->controller;
        $form = $controller->DMSDocumentRequestForm();
        $this->assertNotNull(
            $form->Fields()->fieldByName('NewTextField'),
            'DMSDocumentRequestForm() is extensible as it included the field from the extension'
        );
    }

    /**
     * Tests if a Cart is received
     */
    public function testGetCart()
    {
        $this->assertInstanceOf('DMSDocumentCart', $this->controller->getCart());
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
        $updatedQuantities = array(
            'ItemQuantity' => array($doc->ID => 5),
        );
        $this->controller->updateCartItems($updatedQuantities);
        $this->assertEquals(6, $this->cart->getItem($item->getItemId())->getQuantity());
    }

    /**
     * Tests whether the recipient details are updated from the controller
     */
    public function testUpdateCartReceiverInfo()
    {
        $newInfo = array(
            'ReceiverName'            => 'Joe Soap',
            'ReceiverPhone'           => '111',
            'ReceiverEmail'           => 'joe@example.com',
            'DeliveryAddressLine1'    => 'A1',
            'DeliveryAddressLine2'    => 'A2',
            'DeliveryAddressCountry'  => 'NZ',
            'DeliveryAddressPostCode' => '6011',
        );
        $this->controller->updateCartReceiverInfo($newInfo);
        $this->assertEquals($newInfo, $this->cart->getReceiverInfo());
    }

    /**
     * Tests whether emails are sent. Emails are mocked so not actually sent.
     */
    public function testSend()
    {
        // Set admin email
        Config::inst()->update('Email', 'admin_email', 'admin');
        $data = array(
            'ReceiverName'            => 'Joe Soap',
            'ReceiverPhone'           => '111',
            'ReceiverEmail'           => 'joe@example.com',
            'DeliveryAddressLine1'    => 'A1',
            'DeliveryAddressLine2'    => 'A2',
            'DeliveryAddressCountry'  => 'NZ',
            'DeliveryAddressPostCode' => '6011'
        );
        $this->cart->setReceiverInfo($data);
        $result = $this->controller->send();
        $this->assertTrue(is_array($result));
        $this->assertEquals('joe@example.com', $result['to']);
        $this->assertEquals('admin', $result['from']);
    }

    /**
     * Tests whether email sending is extensible.
     */
    public function testSendIsExtensible()
    {
        $result = $this->controller->send();
        $this->assertEquals('Subject is changed', $result['subject']);
    }

    /**
     * Test to see whether the cart is empty after a request is sent.
     */
    public function testDoRequestSend()
    {
        // Form for use later
        $form = $this->controller->DMSDocumentRequestForm();
        $doc = $this->objFromFixture('DMSDocument', 'doc1');
        // Add some an item to the cart to assert later that its empty
        $item = DMSRequestItem::create()->setDocument($doc)->setQuantity(15);
        $this->controller->getCart()->addItem($item);
        $data = array(
            'ReceiverName'            => 'Joe Soap',
            'ReceiverPhone'           => '111',
            'ReceiverEmail'           => 'joe@example.com',
            'DeliveryAddressLine1'    => 'A1',
            'DeliveryAddressLine2'    => 'A2',
            'DeliveryAddressCountry'  => 'NZ',
            'DeliveryAddressPostCode' => '6011',
            'ItemQuantity'            => array($doc->ID => 5),
        );
        $request = new SS_HTTPRequest('POST', 'mock/url');
        // Assert cart is empty
        $this->assertFalse($this->controller->getCart()->isCartEmpty());
        $result = $this->controller->doRequestSend($data, $form, $request);
        $this->assertInstanceOf('SS_HTTPResponse', $result);
        $this->assertTrue($this->controller->getCart()->isCartEmpty());
    }
}
