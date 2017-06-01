<?php

/**
 * Class DMSDocumentCartControllerTest contains all the tests for {@link DMSDocumentCartController}
 */
class DMSCartAbstractControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'dms-cart/tests/DMSDocumentCartTest.yml';

    /**
     * @var DMSCartAbstractController
     */
    protected $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = DMSCartAbstractController::create();
    }

    /**
     * Ensure the link is "friendly", not a class name
     */
    public function testLink()
    {
        $this->assertNull($this->controller->Link());
    }

    /**
     * Tests if a Cart is received
     */
    public function testGetCart()
    {
        // Callable from base class
        $this->assertInstanceOf('DMSDocumentCart', $this->controller->getCart());
    }

    /**
     * Controls the `Continue browsing` link found in DMSCartNavigation.ss. Defaults all requests back to home.
     * @return string
     */
    public function testGetContinueBrowsingLink()
    {
        // Base instance returns something
        $this->assertSame(Director::absoluteBaseURL(), $this->controller->getContinueBrowsingLink());
    }
}
