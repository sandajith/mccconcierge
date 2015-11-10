<?php
class Mycloset_Membership_Block_Checkout_Onepage_Excellence extends Mage_Checkout_Block_Onepage_Abstract{
      protected function _construct()
    {
        $this->getCheckout()->setStepData('excellence', array(
            'label'     => Mage::helper('checkout')->__('Pickup Address'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }
}
