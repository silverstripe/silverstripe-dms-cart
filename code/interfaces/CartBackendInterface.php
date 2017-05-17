<?php

/**
 * Interface CartBackendInterface represents the contract for a Session Backend
 */
interface CartBackendInterface
{
    /**
     * Get all the {@link RequestItem} objects
     * serialized in the cart.
     *
     * @return array
     */
    public function getItems();

    /**
     * Returns a single element from the items list
     *
     * @param int $id
     *
     * @return RequestItem|boolean
     */
    public function getItem($id);

    /**
     * Add an {@link RequestItem} object into the cart.
     *
     * @param RequestItem $item
     *
     * @return CartBackendInterface
     */
    public function addItem(RequestItem $item);

    /**
     * Remove a {@link RequestItem} object from the cart.
     *
     * @param RequestItem $item
     *
     * @return CartBackendInterface
     */
    public function removeItem(RequestItem $item);

    /**
     * Removes a {@link RequestItem} from the cart by it's id
     *
     * @param int $itemID
     *
     * @return CartBackendInterface
     *
     */
    public function removeItemByID($itemID);

    /**
     * Flushes the cart
     *
     * @return CartBackendInterface
     */
    public function emptyCart();

    /**
     * Set the backURL to be a Session variable for the current Document Cart
     *
     * @param $backURL
     *
     * @return CartBackendInterface
     */
    public function setBackUrl($backURL);

    /**
     * Returns the backURL for the current Document Cart
     *
     * @return mixed
     */
    public function getBackURL();

    /**
     * Sets the recipients information
     *
     * @param $receiverInfo
     *
     * @return CartBackendInterface
     */
    public function setReceiverInfo($receiverInfo);

    /**
     * Retrieves the recipients info
     *
     * @return mixed
     */
    public function getReceiverInfo();
}
