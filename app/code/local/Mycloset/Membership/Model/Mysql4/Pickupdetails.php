<?php

class Mycloset_Membership_Model_Mysql4_Pickupdetails extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('membership/pickupdetails','pickup_id');
    }

}
