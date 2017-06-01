<?php

class DMSDocumentCartController extends DMSCartAbstractController
{
    private static $url_handlers = array(
        '$Action//$ID' => 'handleAction',
    );

    private static $allowed_actions = array(
        'DMSCartEditForm',
        'add',
        'deduct',
        'remove',
        'view'
    );

    public function init()
    {
        parent::init();
        Requirements::css(DMS_CART_DIR . '/css/dms-cart.css');
    }
    /**
     * See {@link DMSDocumentCart::getItems()}
     *
     * @return ArrayList
     */
    public function items()
    {
        return $this->getCart()->getItems();
    }

    /**
     * Prepares receiver info for the template.
     * Additionally it uses Zend_Locale to retrieve the localised spelling of the Country
     *
     * @return array
     */
    public function getReceiverInfo()
    {
        $receiverInfo = $this->getCart()->getReceiverInfo();

        if (isset($receiverInfo['DeliveryAddressCountry']) && $receiverInfo['DeliveryAddressCountry']) {
            $source = Zend_Locale::getTranslationList('territory', $receiverInfo['DeliveryAddressCountry'], 2);
            $receiverInfo['DeliveryAddressCountryLiteral'] = $source[$receiverInfo['DeliveryAddressCountry']];
        }

        if (!empty($receiverInfo)) {
            $result = $receiverInfo;
        } else {
            $result = array('Result' => 'no data');
        }

        return $result;
    }

    /**
     * See DMSDocumentCart::isCartEmpty()
     *
     * @return bool
     */
    public function getIsCartEmpty()
    {
        return $this->getCart()->isCartEmpty();
    }

    /**
     * Add quantity to an item that exists in {@link DMSDocumentCart}.
     * If the item does nt exist, try to add a new item of the particular
     * class given the URL parameters available.
     *
     * @param SS_HTTPRequest $request
     *
     * @return SS_HTTPResponse|string
     */
    public function add(SS_HTTPRequest $request)
    {
        $quantity = ($request->requestVar('quantity')) ? intval($request->requestVar('quantity')) : 1;
        $documentId = (int)$request->param('ID');
        $result = true;
        $message = '';

        if ($doc = DMSDocument::get()->byID($documentId)) {
            /** @var ValidationResult $validate */
            $validate = $this->validateAddRequest($quantity, $doc);
            if ($validate->valid()) {
                if ($this->getCart()->getItem($documentId)) {
                    $this->getCart()->updateItemQuantity($documentId, $quantity);
                } else {
                    $requestItem = DMSRequestItem::create()->setDocument($doc)->setQuantity($quantity);
                    $this->getCart()->addItem($requestItem);
                }
                $backURL = $request->getVar('BackURL');
                // make sure that backURL is a relative path (starts with /)
                if (isset($backURL) && preg_match('/^\//', $backURL)) {
                    $this->getCart()->setBackUrl($backURL);
                }
            } else {
                $message = $validate->starredList();
                $result = false;
            }
        }

        if ($request->isAjax()) {
            $this->response->addHeader('Content-Type', 'application/json');
            return Convert::raw2json(array('result' => $result, 'message' => $message));
        }

        if (!$result) {
            Session::set('dms-cart-validation-message', $message);
        }

        if ($backURL = $request->getVar('BackURL')) {
            return $this->redirect($backURL);
        }

        return $this->redirectBack();
    }

    /**
     * Deduct quantity from an item that exists in {@link DMSDocumentCart}
     *
     * @param SS_HTTPRequest $request
     *
     * @return SS_HTTPResponse|string
     */
    public function deduct(SS_HTTPRequest $request)
    {
        $quantity = ($request->requestVar('quantity')) ? intval($request->requestVar('quantity')) : 1;
        $this->getCart()->updateItemQuantity((int)$request->param('ID'), $quantity);
        $this->redirectBack();

        if ($request->isAjax()) {
            $this->response->addHeader('Content-Type', 'application/json');

            return Convert::raw2json(array('result' => true));
        }
        if ($backURL = $request->getVar('BackURL')) {
            return $this->redirect($backURL);
        }

        return $this->redirectBack();
    }

    /**
     * Completely remove an item that exists in {@link DMSDocumentCart}
     *
     * @param SS_HTTPRequest $request
     *
     * @return string
     */
    public function remove(SS_HTTPRequest $request)
    {
        $this->getCart()->removeItemByID(intval($request->param('ID')));

        if ($request->isAjax()) {
            $this->response->addHeader('Content-Type', 'application/json');

            return Convert::raw2json(array('result' => !$this->getIsCartEmpty()));
        }

        return $this->redirectBack();
    }

    /**
     * Validates a request to add a document to the cart
     *
     * @param  int $quantity
     * @param  DMSDocument $document
     * @return ValidationResult
     */
    protected function validateAddRequest($quantity, DMSDocument $document)
    {
        $result = ValidationResult::create();

        if (!$document->isAllowedInCart()) {
            $result->error(_t(__CLASS__ . '.ERROR_NOT_ALLOWED', 'You are not allowed to add this document'));
        }

        if ($document->getHasQuantityLimit() && $quantity > $document->getMaximumQuantity()) {
            $result->error(_t(
                __CLASS__ . '.ERROR_QUANTITY_EXCEEDED',
                'You can\'t add {quantity} of \'{title}\'',
                array('quantity' => $quantity, 'title' => $document->getTitle())
            ));
        }

        $this->extend('updateValidateAddRequest', $result, $quantity, $document);

        return $result;
    }

    /**
     * Updates the document quantities just before the request is sent.
     *
     * @param array $data
     * @param Form $form
     * @param SS_HTTPRequest $request
     *
     * @return SS_HTTPResponse
     */
    public function updateCartItems($data, Form $form, SS_HTTPRequest $request)
    {
        if (!empty($data['ItemQuantity'])) {
            foreach ($data['ItemQuantity'] as $itemID => $quantity) {
                if (!is_numeric($quantity) || $quantity < 0) {
                    continue;
                }
                // Only update if quantity has changed
                $item = $this->getCart()->getItem($itemID);
                if ($item->getQuantity() == $quantity) {
                    continue;
                }
                // No validate item
                $validate = $this->validateAddRequest($quantity, $item->getDocument());
                if ($validate->valid()) {
                    // Removes, then adds a item new item.
                    $this->getCart()->removeItem($item);
                    $this->getCart()->addItem($item->setQuantity($quantity));
                } else {
                    $form->sessionMessage($validate->starredList(), 'bad');
                    return $this->redirectBack();
                }
            }
        }

        return $this->redirect($this->getCart()->getBackUrl());
    }

    /**
     * Presents an interface for user to update the cart quantities
     *
     * @param SS_HTTPRequest $request
     * @return ViewableData_Customised
     */
    public function view(SS_HTTPRequest $request)
    {
        $this->getCart()->setViewOnly(true);
        $form = $this->DMSCartEditForm();
        return $this
            ->customise(
                array(
                'Form'  => $form,
                'Title' => _t(__CLASS__ . '.UPDATE_TITLE', 'Updating cart items')
                )
            );
    }

    /**
     * Gets and displays an editable list of items within the cart.
     *
     * To extend use the following from within an Extension subclass:
     *
     * <code>
     * public function updateDMSCartEditForm($form)
     * {
     *     // Do something here
     * }
     * </code>
     *
     * @return Form
     */
    public function DMSCartEditForm()
    {
        $actions = FieldList::create(
            FormAction::create(
                'updateCartItems',
                _t(__CLASS__ . '.SAVE_BUTTON', 'Save changes')
            )
        );
        $form = Form::create(
            $this,
            'DMSCartEditForm',
            FieldList::create(),
            $actions
        );
        $form->setTemplate('DMSDocumentRequestForm');
        $this->extend('updateDMSCartEditForm', $form);
        return $form;
    }
}
