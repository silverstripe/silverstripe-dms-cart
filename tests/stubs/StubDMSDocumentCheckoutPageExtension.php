<?php

class StubDMSDocumentCheckoutPageExtension extends Extension implements TestOnly
{
    /**
     * For method {@link DMSDocumentCartCheckoutPage_Controller::DMSDocumentRequestForm}
     *
     * @param  Form $form
     */
    public function updateDMSDocumentRequestForm(Form $form)
    {
        $form->Fields()->push(TextField::create('NewTextField'));
    }

    /**
     * For method {@link DMSDocumentCartCheckoutPage_Controller::DMSDocumentRequestForm}
     *
     * @param Email $email
     */
    public function updateSend(Email $email)
    {
        $email->setSubject('Subject is changed');
    }
}
