<?php

/**
 * Class DMSDocumentCart represents the shopping cart.
 *
 */
class DMSDocumentCart extends Object
{

    /**
     * A handle to the classes' {@link DMSCartBackendInterface}
     * @var DMSCartBackendInterface
     */
    protected $backend;

    /**
     * DMSDocumentCart constructor.
     *
     * @param DMSCartBackendInterface|null $backend
     */
    public function __construct($backend = null)
    {
        parent::__construct();
        $this->backend = ($backend) ?: DMSSessionBackend::create(); // Default to DMSSessionBackend if not provided
    }

    /**
     * @see DMSSessionBackend::getItems()
     */
    public function getItems()
    {
        return $this->backend->getItems();
    }

    /**
     * @see DMSSessionBackend::addItem()
     *
     * @param DMSRequestItem $item
     */
    public function addItem(DMSRequestItem $item)
    {
        $this->backend->addItem($item);
    }

    /**
     * Get a {@link DMSRequestItem} object from the cart.
     *
     * @param int $itemID The ID of the item
     *
     * @return DMSRequestItem|boolean
     */
    public function getItem($itemID)
    {
        return $this->backend->getItem($itemID);
    }

    /**
     * @see DMSSessionBackend::removeItem()
     *
     * @param DMSRequestItem $item
     */
    public function removeItem(DMSRequestItem $item)
    {
        $this->backend->removeItem($item);
    }

    /**
     * @see DMSSessionBackend::removeItemByID()
     *
     * @param int $itemID
     */
    public function removeItemByID($itemID)
    {
        $this->backend->removeItemByID($itemID);
    }

    /**
     * Increment the quantity of an {@link DMSRequestItem}
     * object that already exists in the cart, replacing
     * the existing object with an updated one.
     *
     * @param object|int $itemID   The document ID or DMSRequestItem object
     * @param int        $quantity The quantity to increment by
     *
     * @return boolean TRUE successfully incremented | FALSE item not found
     */
    public function addItemQuantity($itemID, $quantity = 1)
    {
        $item = $this->getItem($itemID);
        if (!$item) {
            return false;
        }
        $currentQuantity = $item->getField('Quantity');
        $item->setField('Quantity', $currentQuantity + $quantity);
        $this->addItem($item);

        return true;
    }

    public function updateItemQuantity($itemID, $quantity)
    {
        $item = $this->getItem($itemID);
        if (!$item) {
            return false;
        }
        $item->setField('Quantity', $quantity);
        $this->addItem($item);

        return true;
    }

    /**
     * Decrement the quantity of an {@link DMSRequestItem}
     * object that exists in the cart, replacing the
     * existing object with an updated one. If the quantity
     * falls below 1, the item is removed completely.
     *
     * @param object|int $itemID   The document ID or DMSRequestItem object
     * @param int        $quantity The quantity to decrement by
     *
     * @return boolean TRUE successfully deducted | FALSE item not found
     */
    public function deductItemQuantity($itemID, $quantity = 1)
    {
        $item = $this->getItem($itemID);
        if (!$item) {
            return false;
        }
        $currentQuantity = $item->getField('Quantity');
        $newQuantity = $currentQuantity - $quantity;
        if ($newQuantity > 0) {
            $item->setField('Quantity', $newQuantity);
            $this->addItem($item);
        } else {
            $this->removeItem($itemID);
        }

        return true;
    }

    /**
     * @see DMSSessionBackend::emptyCart()
     */
    public function emptyCart()
    {
        $this->backend->emptyCart();
    }

    /**
     * Check if a cart is empty
     *
     * @return boolean
     */
    public function isCartEmpty()
    {
        $items = $this->getItems();

        return empty($items);
    }

    /**
     * @see DMSSessionBackend::setBackUrl()
     *
     * @param $backURL
     */
    public function setBackURL($backURL)
    {
        $this->backend->setBackUrl($backURL);
    }

    /**
     * @see DMSSessionBackend::getBackURL()
     */
    public function getBackURL()
    {
        return $this->backend->getBackURL();
    }

    /**
     * @see DMSSessionBackend::getItems()
     *
     * @param $receiverInfo
     */
    public function setReceiverInfo($receiverInfo)
    {
        $this->backend->setReceiverInfo($receiverInfo);
    }

    /**
     * @see DMSSessionBackend::getItems()
     */
    public function getReceiverInfo()
    {
        return $this->backend->getReceiverInfo();
    }

    /**
     * @return DMSCartBackendInterface
     */
    public function getBackend()
    {
        return $this->backend;
    }
}
