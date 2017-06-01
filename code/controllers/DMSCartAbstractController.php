<?php

class DMSCartAbstractController extends ContentController
{
    /**
     * Ensure that links for this controller use the customised route.
     * Searches through the rules set up for the class and returns the first route.
     *
     * @param  string $action
     * @return string
     */
    public function Link($action = null)
    {
        if ($url = array_search(get_called_class(), (array)Config::inst()->get('Director', 'rules'))) {
            // Check for slashes and drop them
            if ($indexOf = stripos($url, '/')) {
                $url = substr($url, 0, $indexOf);
            }
            return $this->join_links($url, $action);
        }
    }

    /**
     * Retrieves a {@link DMSDocumentCart} instance
     *
     * @return DMSDocumentCart
     */
    public function getCart()
    {
        return DMSDocumentCart::singleton();
    }

    /**
     * Controls the `Continue browsing` link found in DMSCartNavigation.ss. Defaults all requests back to home.
     * @return string
     */
    public function getContinueBrowsingLink()
    {
        return Director::absoluteBaseURL();
    }
}
