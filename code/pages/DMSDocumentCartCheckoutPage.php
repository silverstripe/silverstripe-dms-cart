<?php
/**
 * @property Text $ThanksMessage
 * @property Int  $CartEmailRecipientID
 *
 * @method Member CartEmailRecipient
 */
class DMSDocumentCartCheckoutPage extends Page
{
    private static $db = array(
        'ThanksMessage' => 'Text',
    );

    private static $has_one = array(
        'CartEmailRecipient'     => 'Member'
    );

    private static $defaults = array(
        'URLSegment'  => 'checkout',
        'ShowInMenus' => false,
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $recipientDropDown = DropdownField::create(
            'CartEmailRecipientID',
            _t('DMSDocumentCartCheckoutPage.CART_RECIPIENT', 'Account to receive document print requests'),
            Member::get()->Map()->toArray()
        )->setEmptyString(_t(
            'DMSDocumentCartCheckoutPage.CART_RECIPIENT_EMPTY_STRING',
            'Select a member'
        ));
        $fields->insertBefore('Content', $recipientDropDown);

        $fields->insertBefore(
            'Content',
            TextareaField::create(
                'ThanksMessage',
                _t('DMSDocumentCartCheckoutPage.THANK_YOU_MESSAGE', 'Thank you message')
            )
        );

        return $fields;
    }
    /**
     * Automatically create a CheckoutPage if one is not found
     * on the site at the time the database is built (dev/build).
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        if (!SiteTree::get_one($this->class)) {
            /** @var DMSDocumentCartCheckoutPage $page */
            $page = self::create();
            $page->Title = 'Request a printed copy';
            $page->MenuTitle = 'Document Cart Checkout';
            $page->Content = '';
            $page->URLSegment = 'checkout';
            $page->ShowInMenus = 0;
            $page->ThanksMessage = 'Thanks for your request.';
            $page->write();
            $page->publish('Stage', 'Live');
            $page->flushCache();
            DB::alteration_message(
                _t(
                    'DMSDocumentCartCheckoutPage.ALTERATION_MESSAGE',
                    'Document Cart Checkout page \'Request a printed copy\' created'
                ),
                'created'
            );
        }
    }
}
