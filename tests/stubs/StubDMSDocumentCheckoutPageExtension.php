<?php

class StubDMSDocumentCheckoutPageExtension extends Extension implements TestOnly
{
    /**
     * For method {@link DMSCheckoutController::DMSDocumentRequestForm}
     *
     * @param  Form $form
     */
    public function updateDMSDocumentRequestForm(Form $form)
    {
        $form->Fields()->push(TextField::create('NewTextField'));
    }

    /**
     * For method {@link DMSCheckoutController::DMSDocumentRequestForm}
     *
     * @param Email $email
     */
    public function updateSend(Email $email)
    {
        $email->setSubject('Subject is changed');
    }

    /**
     * For method {@link DMSCheckoutController::DMSDocumentRequestForm}
     *
     * @param  Form $form
     */
    public function updateDMSCartEditForm(Form $form)
    {
        $form->Fields()->push(TextField::create('NewTextField'));
    }
}
