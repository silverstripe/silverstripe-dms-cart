<?php

/**
 * Class DMSRequestItem wrapper which represents a DocumentCartItem
 */
class DMSRequestItem extends ViewableData
{
    /**
     * The number of copies required of @itemID
     *
     * @var int
     */
    private $quantity;

    /**
     * The linked {@link DMSDocument} which was added to the cart.
     *
     * @var DMSDocument
     */
    private $document;

    /**
     * If a document is provided on construction, set it to this item instance
     *
     * @param DMSDocument $document
     */
    public function __construct($document = null)
    {
        if ($document instanceof DMSDocument) {
            $this->setDocument($document);
        }
        parent::__construct();
    }

    /**
     * Returns the ID of the $this->document
     *
     * @return int
     */
    public function getItemId()
    {
        return $this->document->ID;
    }

    /**
     * Returns the linked item Quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets the quantity of documents to be ordered.
     *
     * @param int $quantity
     *
     * @return DMSRequestItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Returns the linked DMSDocument
     *
     * @return DMSDocument
     */
    public function getDocument()
    {
        if ($this->document) {
            return DMSDocument::get()->byID($this->document->ID);
        }
    }

    /**
     * @param DMSDocument $document
     *
     * @return DMSRequestItem
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }
}
