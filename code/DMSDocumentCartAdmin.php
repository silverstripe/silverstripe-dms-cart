<?php

class DMSDocumentCartAdmin extends ModelAdmin
{
    private static $managed_models = array(
        'DMSDocumentCartSubmission'
    );

    private static $url_segment = 'cart';
    private static $menu_title = 'Document Cart';

    /**
     * Remove ability to add new items in these grid fields
     *
     * @return CMSForm
     */
    public function getEditForm($id = null, $fields = null)
    {
        /** @var CMSForm $form */
        $form = parent::getEditForm($id, $fields);

        // See parent class
        $gridFieldName = $this->sanitiseClassName($this->modelClass);

        $gridFieldConfig = $form->Fields()->fieldByName($gridFieldName)->getConfig();
        $gridFieldConfig->removeComponentsByType('GridFieldAddNewButton');

        return $form;
    }
}
