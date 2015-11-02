<?php

class Mycloset_Membership_Model_Entity_Userdetails extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {
        $users = mage::getModel('customer/customer')->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('firstname')
                ->addAttributeToSelect('lastname');
        if ($this->_options === null) {
            
$this->_options[] = array(
                    'value' => '0',
                    'label' => 'Sample Closet'
                );
            foreach ($users as $user) {
                $this->_options[] = array(
                    'value' => $user['entity_id'],
                    'label' => $user['firstname']
                );
            }
        }

        return $this->_options;
    }

}
