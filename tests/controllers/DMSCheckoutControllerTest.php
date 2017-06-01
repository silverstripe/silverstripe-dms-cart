<?php

class DMSCheckoutControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'dms-cart/tests/DMSDocumentCartTest.yml';

    /**
     * @var DMSCheckoutController
     */
    protected $controller;

    /**
     * @var DMSDocumentCart
     */
    protected $cart;

    public function setUp()
    {
        parent::setUp();

        DMSCheckoutController::add_extension('StubDMSDocumentCheckoutPageExtension');
        Injector::inst()->registerService(new StubEmail(), 'Email');

        $this->controller = DMSCheckoutController::create();
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

    /**
     * Test the checkout success page shows a pretty message
     */
    public function testCompletePage()
    {
        $result = (string) $this->get('checkout/complete')->getBody();
        $this->assertContains('Thanks!', $result);
        $this->assertContains('You will receive a confirmation email', $result);
    }

    /**
     * Test that the items in my cart are listed on the checkout page, and that some form fields exist
     */
    public function testIndexCheckoutForm()
    {
        $backend = DMSSessionBackend::singleton();
        $document = $this->objFromFixture('DMSDocument', 'limited_supply');
        $requestItem = DMSRequestItem::create($document);
        $backend->addItem($requestItem);

        $response = $this->get('checkout');
        $this->assertEquals(200, $response->getStatusCode());

        $body = (string) $response->getBody();
        $this->assertContains('Checkout', $body);
        $this->assertContains('Your request in summary', $body);
        $this->assertContains('Doc3', $body);
        $this->assertContains('Receiver Name', $body);
    }

    /**
     * Ensure the link is "friendly", not a class name
     */
    public function testLink()
    {
        $this->assertSame('checkout', $this->controller->Link());
        $this->assertSame('checkout/complete', $this->controller->Link('complete'));
    }
}
