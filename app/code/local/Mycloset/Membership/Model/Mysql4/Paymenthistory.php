<?php

class Mycloset_Membership_Model_Mysql4_Paymenthistory extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('membership/paymenthistory', 'payment_history_id');
    }

}
