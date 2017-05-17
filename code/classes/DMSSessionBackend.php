<?php

/**
 * Class DMSSessionBackend represents a session based DocumentCart backend
 */
class DMSSessionBackend extends Object implements CartBackendInterface
{

    /**
     * @return array
     */
    public function getItems()
    {
        $items = Session::get('DocumentCart.Items');
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
     * @param RequestItem $item
     *
     * @return CartBackendInterface
     */
    public function addItem(RequestItem $item)
    {
        Session::set("DocumentCart.Items.{$item->getItemID()}", serialize($item));

        return $this;
    }

    /**
     * @param int|RequestItem $item
     *
     * @return CartBackendInterface
     */
    public function removeItem(RequestItem $item)
    {
        Session::clear("DocumentCart.Items.{$item->getItemID()}");

        return $this;
    }

    public function emptyCart()
    {
        Session::clear('DocumentCart');

        return $this;
    }

    /**
     * @param $backURL
     *
     * @return CartBackendInterface
     */
    public function setBackUrl($backURL)
    {
        Session::set("DocumentCart.BackURL", $backURL);

        return $this;
    }

    /**
     * @return array|mixed|null|Session
     */
    public function getBackURL()
    {
        return Session::get("DocumentCart.BackURL");
    }

    /**
     * @param array $receiverInfo
     *
     * @return CartBackendInterface
     */
    public function setReceiverInfo($receiverInfo)
    {
        Session::set("DocumentCart.ReceiverInfo", serialize($receiverInfo));

        return $this;
    }

    /**
     * @return array
     */
    public function getReceiverInfo()
    {
        return unserialize(Session::get("DocumentCart.ReceiverInfo"));
    }

    /**
     * Returns a single element from the items list
     *
     * @param int $id
     *
     * @return RequestItem
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
     * Removes a {@link RequestItem} from the cart by it's id
     *
     * @param int $itemID
     *
     * @return CartBackendInterface
     */
    public function removeItemByID($itemID)
    {
        Session::clear("DocumentCart.Items.{$itemID}");

        return $this;
    }
}
