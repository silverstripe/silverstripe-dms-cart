<?php

/**
 * Interface DMSCartBackendInterface represents the contract for a Session Backend
 */
interface DMSCartBackendInterface
{
    /**
     * Get all the {@link DMSRequestItem} objects
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
     * @return DMSRequestItem|boolean
     */
    public function getItem($id);

    /**
     * Add an {@link DMSRequestItem} object into the cart.
     *
     * @param DMSRequestItem $item
     *
     * @return DMSCartBackendInterface
     */
    public function addItem(DMSRequestItem $item);

    /**
     * Remove a {@link DMSRequestItem} object from the cart.
     *
     * @param DMSRequestItem $item
     *
     * @return DMSCartBackendInterface
     */
    public function removeItem(DMSRequestItem $item);

    /**
     * Removes a {@link DMSRequestItem} from the cart by it's id
     *
     * @param int $itemID
     *
     * @return DMSCartBackendInterface
     *
     */
    public function removeItemByID($itemID);

    /**
     * Flushes the cart
     *
     * @return DMSCartBackendInterface
     */
    public function emptyCart();

    /**
     * Set the backURL to be a Session variable for the current Document Cart
     *
     * @param string $backURL
     *
     * @return DMSCartBackendInterface
     */
    public function setBackUrl($backURL);

    /**
     * Returns the backURL for the current Document Cart
     *
     * @return string
     */
    public function getBackUrl();

    /**
     * Sets the recipients information
     *
     * @param array $receiverInfo
     *
     * @return DMSCartBackendInterface
     */
    public function setReceiverInfo(array $receiverInfo = array());

    /**
     * Retrieves the recipients info
     *
     * @return array
     */
    public function getReceiverInfo();
}
