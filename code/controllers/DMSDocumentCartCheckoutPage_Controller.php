<?php

class DMSDocumentCartCheckoutPage_Controller extends Page_Controller
{
    private static $allowed_actions = array(
        'DMSDocumentRequestForm',
        'complete'
    );

    /**
     * An array containing the recipients basic information
     *
     * @var array
     */
    public static $receiverInfo = array(
        'ReceiverName'            => '',
        'ReceiverPhone'           => '',
        'ReceiverEmail'           => '',
        'DeliveryAddressLine1'    => '',
        'DeliveryAddressLine2'    => '',
        'DeliveryAddressCountry'  => '',
        'DeliveryAddressPostCode' => '',
    );

    /**
     * Gets and displays an editable list of items within the cart, as well as a contact form with entry
     * fields for the recipients information.
     *
     * To extend use the following from within an Extension subclass:
     *
     * <code>
     * public function updateDMSDocumentRequestForm($form)
     * {
     *     // Do something here
     * }
     * </code>
     *
     * @return Form
     */
    public function DMSDocumentRequestForm()
    {
        $fields = DMSDocumentCartSubmission::create()->scaffoldFormFields();
        $fields->replaceField('DeliveryAddressLine2', TextField::create('DeliveryAddressLine2', ''));
        $fields->replaceField('DeliveryAddressCountry', CountryDropdownField::create(
            'DeliveryAddressCountry',
            _t('DMSDocumentCartCheckoutPage.RECEIVER_COUNTRY', 'Country')
        )->setValue('NZ'));

        $requiredFields = array(
            'ReceiverName',
            'ReceiverPhone',
            'ReceiverEmail',
            'DeliveryAddressLine1',
            'DeliveryAddressCountry',
            'DeliveryAddressPostCode',
        );
        foreach ($fields as $field) {
            if (in_array($field->name, $requiredFields)) {
                $field->addExtraClass('requiredField');
            }
        }
        $validator = RequiredFields::create($requiredFields);
        $actions = FieldList::create(
            FormAction::create(
                'doRequestSend',
                _t('DMSDocumentCartCheckoutPage.SEND_ACTION', 'Send your request')
            )
        );

        $form = Form::create(
            $this,
            'DMSDocumentRequestForm',
            $fields,
            $actions,
            $validator
        );

        if ($receiverInfo = $this->getCart()->getReceiverInfo()) {
            $form->loadDataFrom($receiverInfo);
        }

        $form->setTemplate('DMSDocumentRequestForm');
        $this->extend('updateDMSDocumentRequestForm', $form);

        return $form;
    }

    /**
     * Sends an email to both the configured recipient as well as the requester. The
     * configured recipient is bcc'ed to the email in order to fulfill it.
     *
     * To extend use the following from within an Extension subclass:
     *
     * <code>
     * public function updateSend($email)
     * {
     *     // Do something here
     * }
     * </code>
     * @return mixed
     *
     * @throws DMSDocumentCartException
     */
    public function send()
    {
        $member = $this->CartEmailRecipient();

        if (!$member->exists()) {
            throw new DMSDocumentCartException('No recipient has been configured. Please do so from the CMS');
        }

        $cart = $this->getCart();
        $from = Config::inst()->get('Email', 'admin_email');
        $emailAddress = ($info = $cart->getReceiverInfo()) ? $info['ReceiverEmail'] : $from;
        $email = Email::create(
            $from,
            $emailAddress,
            _t('DMSDocumentCartCheckoutPage.EMAIL_SUBJECT', 'Request for Printed Publications')
        );
        $email->setBcc($member->Email);
        $renderedCart = $cart->renderWith('DocumentCart_email');
        $body = sprintf(
            '<p>%s</p>',
            _t(
                'DMSDocumentCartCheckoutPage.EMAIL_BODY',
                'A request for printed publications has been submitted with the following details:'
            )
        );
        $body .= $renderedCart->getValue();
        $email->setBody($body)->setReplyTo($emailAddress);
        $this->extend('updateSend', $email);

        return $email->send();
    }

    /**
     * Handles form submission.
     * Totals requested are updated, delivery details added, email sent for fulfillment
     * and print request totals updated.
     *
     * @param array          $data
     * @param Form           $form
     * @param SS_HTTPRequest $request
     *
     * @return SS_HTTPResponse
     */
    public function doRequestSend($data, Form $form, SS_HTTPRequest $request)
    {
        $this->updateCartItems($data);
        $this->updateCartReceiverInfo($data);
        $this->send();
        $this->trackTimestampedPrintRequest();
        $this->getCart()->saveSubmission($form);
        $this->getCart()->emptyCart();

        return $this->redirect($this->Link('complete'));
    }

    /**
     * Displays the preconfigured thank you message to the user upon completion
     *
     * @return ViewableData_Customised
     */
    public function complete()
    {
        return $this->customise(
            ArrayData::create(
                array(
                    'Content' => $this->ThanksMessage,
                )
            )
        );
    }

    /**
     * Increments the print counts of all documents which were successfully sent.
     */
    public function trackTimestampedPrintRequest()
    {
        /** @var DMSRequestItem $item */
        foreach ($this->getCart()->getItems() as $item) {
            $item->getDocument()->incrementPrintRequest();
        }
    }

    /**
     * Retrieves a {@link DMSDocumentCart} instance
     *
     * @return DMSDocumentCart
     */
    public function getCart()
    {
        return singleton('DMSDocumentCart');
    }

    /**
     * Updates the document quantities just before the request is sent.
     *
     * @param array $data
     */
    public function updateCartItems($data)
    {
        if (!empty($data['ItemQuantity'])) {
            foreach ($data['ItemQuantity'] as $itemID => $quantity) {
                // Only update if quantity has changed
                $item = $this->getCart()->getItem($itemID);
                if ($item->getQuantity() == $quantity) {
                    continue;
                }
                $this->getCart()->updateItemQuantity($itemID, $quantity - 1);
            }
        }
    }

    /**
     * Updates the cart receiver info just before the request is sent.
     *
     * @param array $data
     */
    public function updateCartReceiverInfo($data)
    {
        $info = array_merge(self::$receiverInfo, $data);
        $this->getCart()->setReceiverInfo($info);
    }
}
