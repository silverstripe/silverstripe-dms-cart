<?php

/**
 * Class DMSSessionBackend represents a session based DMSDocumentCart backend
 */
class DMSSessionBackend extends Object implements DMSCartBackendInterface
{

    /**
     * @return array
     */
    public function getItems()
    {
        $items = Session::get('DMSDocumentCart.Items');
        if ($items && is_array($items)) {
            foreach ($items as $itemID => $serialObj) {
                if ($serialObj == null) {
                    unset($items[$itemID]);
                    continue;
                }
                $items[$itemID] = unserialize($serialObj);
            }
        }

        return $items ? $items : array();
    }

    /**
     * @param DMSRequestItem $item
     *
     * @return DMSCartBackendInterface
     */
    public function addItem(DMSRequestItem $item)
    {
        Session::set("DMSDocumentCart.Items.{$item->getItemID()}", serialize($item));

        return $this;
    }

    /**
     * @param int|DMSRequestItem $item
     *
     * @return DMSCartBackendInterface
     */
    public function removeItem(DMSRequestItem $item)
    {
        Session::clear("DMSDocumentCart.Items.{$item->getItemID()}");

        return $this;
    }

    public function emptyCart()
    {
        Session::clear('DMSDocumentCart');

        return $this;
    }

    /**
     * @param $backURL
     *
     * @return DMSCartBackendInterface
     */
    public function setBackUrl($backURL)
    {
        Session::set("DMSDocumentCart.BackURL", $backURL);

        return $this;
    }

    /**
     * @return array|mixed|null|Session
     */
    public function getBackURL()
    {
        return Session::get("DMSDocumentCart.BackURL");
    }

    /**
     * @param array $receiverInfo
     *
     * @return DMSCartBackendInterface
     */
    public function setReceiverInfo(array $receiverInfo = array())
    {
        Session::set("DMSDocumentCart.ReceiverInfo", serialize($receiverInfo));

        return $this;
    }

    /**
     * @return array
     */
    public function getReceiverInfo()
    {
        return unserialize(Session::get("DMSDocumentCart.ReceiverInfo"));
    }

    /**
     * Returns a single element from the items list
     *
     * @param int $id
     *
     * @return DMSRequestItem
     */
    public function getItem($id)
    {
        $result = false;
        if ($items = $this->getItems()) {
            if (is_array($items) && array_key_exists($id, $items)) {
                $result = $items[$id];
            }
        }

        return $result;
    }


    /**
     * Removes a {@link DMSRequestItem} from the cart by it's id
     *
     * @param int $itemID
     *
     * @return DMSCartBackendInterface
     */
    public function removeItemByID($itemID)
    {
        Session::clear("DMSDocumentCart.Items.{$itemID}");

        return $this;
    }
}
