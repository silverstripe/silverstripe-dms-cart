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
        'AllowedInCart'       => 'Boolean',
        'MaximumCartQuantity' => 'Int',
        // Running total of print requests on this document
        'PrintRequestCount'   => 'Int',
    );

    private static $summary_fields = array(
        'PrintRequestCount' => 'Print Requests',
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
        Requirements::javascript(DMS_CART_DIR . '/javascript/dmscart.js');

        $newFields = array(
            CheckboxField::create(
                'AllowedInCart',
                _t('DMSDocumentCart.ALLOWED_IN_CART', 'Allowed in document cart')
            )->addExtraClass('dms-allowed-in-cart'),
            TextField::create(
                'MaximumCartQuantity',
                _t('DMSDocumentCart.MAXIMUM_CART_QUANTITY', 'Maximum cart quantity')
            )->setRightTitle(
                _t(
                    'DMSDocumentCart.MAXIMUM_CART_QUANTITY_HELP',
                    'If set, this will enforce a maximum number of this item that can be ordered per cart'
                )
            )->addExtraClass('dms-maximum-cart-quantity hide')
        );

        foreach ($newFields as $field) {
            $fields->insertBefore($field, 'Description');
        }
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
     * Returns whether the current document has a limit on how many items can be added to a single cart
     *
     * @return bool
     */
    public function getHasQuantityLimit()
    {
        return $this->owner->getMaximumQuantity() > 0;
    }

    /**
     * Get the maximum quantity of this document that can be ordered in a single cart
     *
     * @return int
     */
    public function getMaximumQuantity()
    {
        return (int) $this->owner->MaximumCartQuantity;
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

        if ($action === 'checkout') {
            $result = DMSCheckoutController::singleton()->Link();
        } else {
            $result = Controller::join_links('documentcart', $action, $this->owner->ID);
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

    /**
     * Returns any validation messages that may have been in the session and clears them
     *
     * @return false
     */
    public function getValidationResult()
    {
        if ($result = Session::get('dms-cart-validation-message')) {
            Session::clear('dms-cart-validation-message');
            return $result;
        }
        return false;
    }

    /**
     * Add a "print request count" field to the summary fields for editing a DMS document
     *
     * @see DMSDocument::getFieldsForFile
     * @param FieldGroup $fieldGroup
     */
    public function updateFieldsForFile(FieldGroup $fieldGroup)
    {
        $fields = $fieldGroup->FieldList();

        /** @var FieldList $summaryFields */
        $summaryFields = $fields->fieldByName('FilePreview.FilePreviewData.FilePreviewDataFields');
        if (!($summaryFields instanceof CompositeField)) {
            return;
        }

        $summaryFields->FieldList()->push(
            ReadonlyField::create(
                'PrintRequestCount',
                _t(
                    __CLASS__ . '.PRINT_REQUEST_COUNT',
                    'Print request count:'
                ),
                $this->owner->PrintRequestCount
            )
        );
    }
}
