<?php

/**
 * Class DMSSessionBackend represents a Session-storage DMSDocumentCart backend
 */
class DMSSessionBackend extends Object implements DMSCartBackendInterface
{
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

    public function addItem(DMSRequestItem $item)
    {
        if ($item->getDocument()) {
            Session::set("DMSDocumentCart.Items.{$item->getItemID()}", serialize($item));
        }

        return $this;
    }

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

    public function setBackUrl($backURL)
    {
        Session::set('DMSDocumentCart.BackURL', $backURL);

        return $this;
    }

    public function getBackUrl()
    {
        return Session::get('DMSDocumentCart.BackURL');
    }

    public function setReceiverInfo(array $receiverInfo = array())
    {
        Session::set('DMSDocumentCart.ReceiverInfo', serialize($receiverInfo));

        return $this;
    }

    public function getReceiverInfo()
    {
        return unserialize(Session::get('DMSDocumentCart.ReceiverInfo'));
    }

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

    public function removeItemByID($itemID)
    {
        Session::clear("DMSDocumentCart.Items.{$itemID}");

        return $this;
    }
}
