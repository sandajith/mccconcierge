<?php

class Mycloset_Membership_Model_Mysql4_Payment extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('membership/payment', 'payment_id');
    }

}
