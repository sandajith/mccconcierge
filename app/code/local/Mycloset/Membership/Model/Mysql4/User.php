<?php
class Mycloset_Membership_Model_Mysql4_User extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('membership/userdetails', 'entity_id');       
    }
    
   
    
}
