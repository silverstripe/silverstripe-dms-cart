<?php
/**
 * Class DMSDocumentCart represents the shopping cart.
 *
 */
class DMSDocumentCart extends ViewableData
{
    /**
     * A handle to the classes' {@link DMSCartBackendInterface}
     *
     * @var DMSCartBackendInterface
     */
    protected $backend;

    /**
     * Variable to control whether a cart is being updated or not
     *
     * @var bool
     */
    private $viewOnly = false;

    /**
     * Instantiate a cart backend either by that provided, or a session default
     *
     * @param DMSCartBackendInterface $backend
     * @throws DMSDocumentCartException If a backend was provided but doesn't implement the backend interface
     */
    public function __construct($backend = null)
    {
        parent::__construct();
        if ($backend && !($backend instanceof DMSCartBackendInterface)) {
            throw new DMSDocumentCartException('Backend must implement DMSCartBackendInterface!');
        }
        $this->backend = ($backend) ?: DMSSessionBackend::singleton();
    }

    /**
     * Returns all the cart items as an array
     *
     * @return ArrayList
     */
    public function getItems()
    {
        $validItems = ArrayList::create();
        foreach ($this->backend->getItems() as $item) {
            /** @var DMSRequestItem $item */
            if (!$item->getDocument()) {
                $this->backend->removeItem($item);
                continue;
            }
            $validItems->push($item);
        }
        return $validItems;
    }

    /**
     * Gets a partial caching key that can be used to prevent the getItems method from hitting the database every
     * time to check whether a document exists. Includes a hash of the valid items in the cart (including their
     * quantity).
     *
     * @return string
     */
    public function getCartSummaryCacheKey()
    {
        return 'dms-cart-items-' . md5(serialize($this->getItems()));
    }

    /**
     * Add an {@link DMSRequestItem} object into the cart.
     *
     * @param DMSRequestItem $item
     *
     * @return DMSDocumentCart
     */
    public function addItem(DMSRequestItem $item)
    {
        $this->backend->addItem($item);

        return $this;
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
     * Removes a {@link DMSRequestItem} from the cart by it's id
     *
     * @param DMSRequestItem $item
     *
     * @return DMSDocumentCart
     */
    public function removeItem(DMSRequestItem $item)
    {
        $this->backend->removeItem($item);

        return $this;
    }

    /**
     * Removes a {@link DMSRequestItem} from the cart by it's id
     *
     * @param int $itemID
     *
     * @return DMSDocumentCart
     */
    public function removeItemByID($itemID)
    {
        $this->backend->removeItemByID($itemID);

        return $this;
    }

    /**
     * Adjusts (increments, decrements or amends) the quantity of an {@link DMSRequestItem}.'
     * A positive $quantity increments the total, whereas a negative value decrements the total. A cart item
     * is removed completely if it's value reaches <= 0.
     *
     * @param int $itemID
     * @param int $quantity
     *
     * @return DMSDocumentCart
     */
    public function updateItemQuantity($itemID, $quantity)
    {
        if ($item = $this->getItem($itemID)) {
            $currentQuantity = $item->getQuantity();
            $newQuantity = $currentQuantity + $quantity;
            if ($newQuantity <= 0) {
                $this->removeItemByID($itemID);
            } else {
                $item->setQuantity($newQuantity);
                $this->addItem($item);
            }
        }

        return $this;
    }

    /**
     * Completely empties a cart
     *
     * @return DMSDocumentCart
     */
    public function emptyCart()
    {
        $this->backend->emptyCart();

        return $this;
    }

    /**
     * Checks if a cart is empty.
     * Returns true if cart is empty, false otherwise.
     *
     * @return boolean
     */
    public function isCartEmpty()
    {
        $items = $this->getItems();

        return !$items->exists();
    }

    /**
     * Set the backURL to be a Session variable for the current Document Cart
     *
     * @param string $backURL
     *
     * @return DMSDocumentCart
     */
    public function setBackUrl($backURL)
    {
        $this->backend->setBackUrl($backURL);

        return $this;
    }

    /**
     * Returns the backURL for the current Document Cart
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->backend->getBackUrl();
    }

    /**
     * Sets the recipients info as an array (e.g. array('Name'=>'Joe','Surname'=>'Soap'))
     *
     * @param array $receiverInfo
     *
     * @return DMSDocumentCart
     */
    public function setReceiverInfo($receiverInfo)
    {
        $this->backend->setReceiverInfo($receiverInfo);

        return $this;
    }

    /**
     * Retrieves the recipients info as an array (e.g. array('Name'=>'Joe','Surname'=>'Soap'))
     *
     * @return array
     */
    public function getReceiverInfo()
    {
        return $this->backend->getReceiverInfo();
    }

    /**
     * Returns the recipients in a Viewable format
     *
     * @return ArrayData|bool
     */
    public function getReceiverInfoNice()
    {
        return (is_array($this->getReceiverInfo())) ? ArrayData::create($this->getReceiverInfo()) : false;
    }

    /**
     * Gets the backend handler
     *
     * @return DMSSessionBackend
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * Checks if an item exists within a cart. Returns true (if exists) or false.
     *
     * @param int $itemID
     *
     * @return bool
     */
    public function isInCart($itemID)
    {
        return (bool) $this->getItem($itemID);
    }

    /**
     * Persists a cart submission to the database
     *
     * @param Form $form
     *
     * @return int
     */
    public function saveSubmission(Form $form)
    {
        $submission = DMSDocumentCartSubmission::create();
        $form->saveInto($submission);
        $return = $submission->write();
        $this->getItems()->each(function ($item) use ($submission) {
            /** @var DMSDocument $document */
            $document = $item->getDocument();
            $submissionItem = DMSDocumentCartSubmissionItem::create(array(
                'OriginalID' => $document->ID,
                'Quantity' => $item->getQuantity(),
                'Title' => $document->getTitle(),
                'Filename' => $document->getFilenameWithoutID()
            ));
            $submission->Items()->add($submissionItem);
            $document->incrementPrintRequest();
        });

        return $return;
    }

    /**
     * Returns true if the cart is being updated. False otherwise
     * @return bool
     */
    public function isViewOnly()
    {
        return $this->viewOnly;
    }

    /**
     * Sets the updating flag
     *
     * @param bool $viewOnly
     * @return DMSDocumentCart
     */
    public function setViewOnly($viewOnly)
    {
        $this->viewOnly = (bool) $viewOnly;
        return $this;
    }

    /**
     * Displays a view-only table of the cart items.
     *
     * @return HTMLText
     */
    public function getSummary()
    {
        return $this->renderWith('DMSDocumentCartSummary');
    }

    /**
     * Utility method to link to the current controllers action
     *
     * @param string $action
     * @return string
     */
    public function getLink($action = null)
    {
        return DMSDocumentCartController::create()->Link($action);
    }
}
