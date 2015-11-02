<?php
class Mycloset_Membership_Model_Mysql4_Pickupdetails_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('membership/pickupdetails');
    }
}