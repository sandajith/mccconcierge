<?php

class Mycloset_Membership_Adminhtml_MembershipController extends Mage_Adminhtml_Controller_action {

//    public function indexAction() {
//        $this->loadLayout();
//        $this->renderLayout();
//    }



    public function indexAction() {
        $this->_title($this->__('Customers'))->_title($this->__('Additional services'));

        
        $this->loadLayout();

        $this->_setActiveMenu('customer');

        $this->renderLayout();
    }

}
