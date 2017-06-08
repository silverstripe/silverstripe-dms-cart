<?php

class DMSCheckoutController extends DMSCartAbstractController
{
    private static $allowed_actions = array(
        'DMSDocumentRequestForm',
        'index',
        'complete',
        'send',
    );

    /**
     * An array containing the recipients basic information
     *
     * @config
     * @var array
     */
    private static $receiver_info = array(
        'ReceiverName'            => '',
        'ReceiverPhone'           => '',
        'ReceiverEmail'           => '',
        'DeliveryAddressLine1'    => '',
        'DeliveryAddressLine2'    => '',
        'DeliveryAddressCountry'  => '',
        'DeliveryAddressPostCode' => '',
    );

    public function init()
    {
        parent::init();

        Requirements::css(DMS_CART_DIR . '/css/dms-cart.css');
    }

    public function index()
    {
        $this->getCart()->setBackUrl(Director::absoluteBaseURL().$this->Link());
        $form = $this->DMSDocumentRequestForm();
        return $this
            ->customise(array(
                'Form'  => $form,
                'Title' => _t(__CLASS__ . '.CHECKOUT_TITLE', 'Checkout')
            ))
            ->renderWith(array('Page', 'DMSDocumentRequestForm'));
    }

    /**
     * Gets and displays a list of items within the cart, as well as a contact form with entry
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
            _t('DMSCheckoutController.RECEIVER_COUNTRY', 'Country')
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
                _t('DMSCheckoutController.SEND_ACTION', 'Send your request')
            )
        );

        $form = Form::create($this, 'DMSDocumentRequestForm', $fields, $actions, $validator);

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
     */
    public function send()
    {
        $cart = $this->getCart();
        $from = Config::inst()->get('Email', 'admin_email');
        $emailAddress = ($info = $cart->getReceiverInfo()) ? $info['ReceiverEmail'] : $from;
        $email = Email::create(
            $from,
            $emailAddress,
            _t('DMSCheckoutController.EMAIL_SUBJECT', 'Request for Printed Publications')
        );

        if ($bcc = $this->getConfirmationBcc()) {
            $email->setBcc($bcc);
        }

        $renderedCart = $cart->renderWith('DocumentCart_email');
        $body = sprintf(
            '<p>%s</p>',
            _t(
                'DMSCheckoutController.EMAIL_BODY',
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
     * @param array $data
     * @param Form $form
     * @param SS_HTTPRequest $request
     *
     * @return SS_HTTPResponse
     */
    public function doRequestSend($data, Form $form, SS_HTTPRequest $request)
    {
        $this->updateCartReceiverInfo($data);
        $this->send();
        $this->getCart()->saveSubmission($form);
        $this->getCart()->emptyCart();

        return $this->redirect($this->Link('complete'));
    }

    /**
     * Displays the preconfigured thank you message to the user upon completion
     *
     * @return ViewableData
     */
    public function complete()
    {
        $data = array(
            'Title'   => _t(__CLASS__ . '.COMPLETE_THANKS', 'Thanks!'),
            'Content' => _t(
                __CLASS__ . '.COMPLETE_MESSAGE',
                'Thank you. You will receive a confirmation email shortly.'
            )
        );

        $this->extend('updateCompleteMessage', $data);

        return $this->customise($data)->renderWith('Page');
    }

    /**
     * Updates the cart receiver info just before the request is sent.
     *
     * @param array $data
     */
    public function updateCartReceiverInfo($data)
    {
        $info = array_merge(static::config()->get('receiver_info'), $data);
        $this->getCart()->setReceiverInfo($info);
    }

    /**
     * If BCC email addresses are configured, return the addresses to send to in comma delimited format
     *
     * @return string
     */
    protected function getConfirmationBcc()
    {
        $emails = (array) static::config()->get('recipient_emails');
        if (empty($emails)) {
            return false;
        }

        return implode(',', $emails);
    }
}
