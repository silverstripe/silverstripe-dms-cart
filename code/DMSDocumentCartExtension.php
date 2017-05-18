<?php

/**
 * Class DMSDocumentCartExtension adds a control
 *
 * @property Boolean     AllowedInCart
 *
 * @property DMSDocument owner
 */
class DMSDocumentCartExtension extends DataExtension
{
    private static $db = array(
        'AllowedInCart' => 'Boolean',
    );

    public function isAllowedInCart()
    {
        return $this->owner->AllowedInCart;
    }

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->insertBefore(
            CheckboxField::create(
                'AllowedInCart',
                _t('DMSDocument.ALLOWED_IN_CART', 'Allowed in document cart')
            ),
            'Description'
        );
    }
}
