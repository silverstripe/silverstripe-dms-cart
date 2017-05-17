<?php

/**
 * Class DocumentCart_Controller
 */
class DMSDocumentCartController extends Controller
{

    private static $url_handlers = array(
        '$Action//$ID' => 'handleAction',
    );

    private static $allowed_actions = array(
        'DocumentCartForm',
        'add',
        'deduct',
        'remove',
    );

    /**
     * See {@link DMSDocumentCart::getItems()}
     *
     * @return ArrayList
     */
    public function Items()
    {
        return new ArrayList($this->Cart()->getItems());
    }

    /**
     * Prepares receiver info for the template
     *
     * @return ArrayData
     */
    public function ReceiverInfo()
    {
        $receiverInfo = $this->Cart()->getReceiverInfo();

        if (isset($receiverInfo['DeliveryAddressCountry']) && $receiverInfo['DeliveryAddressCountry']) {
            $source = Zend_Locale::getTranslationList('territory', $receiverInfo['DeliveryAddressCountry'], 2);
            $receiverInfo['DeliveryAddressCountryLiteral'] = $source[$receiverInfo['DeliveryAddressCountry']];
        }

        if (!empty($receiverInfo)) {
            $result = new ArrayData($receiverInfo);
        } else {
            $result = new ArrayData(array('Result' => 'no data'));
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
        return $this->Cart()->isCartEmpty();
    }

    /**
     * Add quantity to an item that exists in {@link DMSDocumentCart}.
     * If the item does nt exist, try to add a new item of the particular
     * class given the URL parameters available.
     *
     * @param SS_HTTPRequest $request
     */
    public function add(SS_HTTPRequest $request)
    {

        $quantity = ($request->requestVar('quantity')) ? intval($request->requestVar('quantity')) : 1;
        $documentId = (int)$request->param('ID');
        /** @var DMSDocument|DMSDocumentCartExtension $doc */
        if ($doc = DMSDocument::get()->byID($documentId)) {
            if ($doc->AllowedInCart && $doc->canView()) {
                if ($this->Cart()->getItem($documentId)) {
                    $this->Cart()->addItemQuantity($documentId, $quantity);
                } else {
                    $requestItem = DMSRequestItem::create()->setDocument($doc)->setQuantity($quantity);
                    $this->Cart()->addItem($requestItem);
                }
                $backURL = $request->getVar('BackURL');
                // make sure that backURL is a relative path (starts with /)
                if (isset($backURL) && preg_match('/^\//', $backURL)) {
                    $this->Cart()->setBackURL($backURL);
                }
            }
        }

        if ($request->isAjax()) {
            $this->response->addHeader('Content-Type', 'text/plain');
            echo true;

            return;
        }
        if ($request->getVar('BackURL')) {
            $this->redirect($backURL);
        } else {
            $checkoutPage = DocumentCartCheckoutPage::get_one('DocumentCartCheckoutPage');
            if ($checkoutPage && $checkoutPage->exists()) {
                $this->redirect($checkoutPage->Link());
            } else {
                $this->redirectBack();
            }
        }
    }

    /**
     * Deduct quantity from an item that exists in {@link DMSDocumentCart}
     *
     * @param SS_HTTPRequest $request
     */
    public function deduct(SS_HTTPRequest $request)
    {
        $quantity = ($request->requestVar('quantity')) ? intval($request->requestVar('quantity')) : 1;
        $this->Cart()->deductItemQuantity((int)$request->param('ID'), $quantity);
        $this->redirectBack();

        if ($request->isAjax()) {
            $this->response->addHeader('Content-Type', 'text/plain');
            echo true;

            return;
        }
        if ($backURL = $request->getVar('BackURL')) {
            $this->redirect($backURL);
        } else {
            $checkoutPage = DocumentCartCheckoutPage::get_one('DocumentCartCheckoutPage');
            if ($checkoutPage && $checkoutPage->exists()) {
                $this->redirect($checkoutPage->Link());
            } else {
                $this->redirectBack();
            }
        }
    }

    /**
     * Completely remove an item that exists in {@link DMSDocumentCart}
     */
    public function remove(SS_HTTPRequest $request)
    {
        $this->Cart()->removeItemByID(intval($request->param('ID')));

        if ($request->isAjax()) {
            $this->response->addHeader('Content-Type', 'text/plain');
            echo !$this->getIsCartEmpty();
        } else {
            $this->redirectBack();
        }
    }

    /**
     * Retrieves a {@link DMSDocumentCart} instance
     *
     * @return DMSDocumentCart
     */
    public function Cart()
    {
        return singleton('DMSDocumentCart');
    }
}
