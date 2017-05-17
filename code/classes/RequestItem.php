<?php

/**
 * Class RequestItem wrapper which represents a DocumentCartItem
 */
class RequestItem extends ViewableData
{
    /**
     * The number of copies required of @itemID
     * @var Int
     */
    private $quantity;

    /**
     * The linked {@link DMSDocument}
     * @var DMSDocument
     */
    private $document;

    /**
     * @return Int
     */
    public function getItemID()
    {
        return $this->document->ID;
    }

    /**
     * @return Int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param Int $quantity
     *
     * @return RequestItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return DMSDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param DMSDocument $document
     *
     * @return RequestItem
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }
}
