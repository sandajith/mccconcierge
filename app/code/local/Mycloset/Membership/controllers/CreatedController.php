<?php

class Mycloset_Membership_CreatedController extends Mage_Core_Controller_Front_Action {

    //const PATH_GATE_THRESHOLD = 'membership/general/threshold';

    public function indexAction() {
        
        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Choose your Payment"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Mycloset"),
            "title" => $this->__("My closet"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("Membership", array(
            "label" => $this->__("Membership"),
            "title" => $this->__("Membership")
        ));
        $this->renderLayout();
        
        echo 'success';
    }
}
    