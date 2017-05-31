<?php

class DMSDocumentAdminExtension extends Extension
{
    private static $managed_models = array(
        'DMSDocumentCartSubmission'
    );

    /**
     * Remove ability to add new items in these grid fields
     *
     * @param CMSForm $form
     */
    public function updateEditForm(CMSForm $form)
    {
        $gridField = $form->Fields()->fieldByName('DMSDocumentCartSubmission');
        if ($gridField) {
            $gridField->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        }
    }
}
