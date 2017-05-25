<?php

/**
 * Class DMSDocumentCartExtension
 *
 * @property Boolean     AllowedInCart
 * @property Int         PrintRequestCount
 *
 * @property DMSDocument owner
 */
class DMSDocumentCartExtension extends DataExtension
{
    /**
     * @var DMSDocumentCartController
     */
    private $cartController;

    private static $db = array(
        'AllowedInCart'     => 'Boolean',
        // Running total of print requests on this document
        'PrintRequestCount' => 'Int',
    );

    /**
     * Returns if a Document is permitted to reflect in a cart
     *
     * @return boolean
     */
    public function isAllowedInCart()
    {
        return ($this->owner->AllowedInCart && $this->owner->canView());
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->insertBefore(
            'Description',
            CheckboxField::create(
                'AllowedInCart',
                _t('DMSDocumentCart.ALLOWED_IN_CART', 'Allowed in document cart')
            )
        );
    }

    /**
     * Increments the number of times a document was printed
     *
     * @return DMSDocument
     */
    public function incrementPrintRequest()
    {
        $this->owner->PrintRequestCount++;
        $this->owner->write();

        return $this->owner;
    }

    /**
     * Checks if a given document already exists within the Cart. True if it does, false otherwise
     *
     * @return bool
     */
    public function isInCart()
    {
        return (bool) $this->getCart()->isInCart($this->owner->ID);
    }

    /**
     * Builds and returns a valid DMSDocumentController URL from the given $action link
     *
     * @param string $action Can be either 'add', 'remove' or 'checkout
     *
     * @return string
     *
     * @throws InvalidArgumentException if the provided $action is not allowed.
     */
    public function getActionLink($action = 'add')
    {
        $action = strtolower($action);
        $allowedActions = array_merge($this->getCartController()->allowedActions(), array('checkout'));
        if (!in_array($action, $allowedActions)) {
            throw new InvalidArgumentException("{$action} is not accepted for this method.");
        }

        if ($action !== 'checkout') {
            $result = Controller::join_links('documentcart', $action, $this->owner->ID);
        } else {
            $result = SiteTree::get_one('DMSDocumentCartCheckoutPage')->Link();
        }

        return $result;
    }

    /**
     * Retrieves a DMSDocumentCartController handle
     *
     * @return DMSDocumentCartController
     */
    public function getCartController()
    {
        if (!$this->cartController) {
            $this->cartController = DMSDocumentCartController::create();
        }

        return $this->cartController;
    }

    /**
     * Retrieves a DMSDocumentCart handle
     *
     * @return DMSDocumentCart
     */
    public function getCart()
    {
        return $this->getCartController()->getCart();
    }
}
