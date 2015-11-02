<?php

class Mycloset_Membership_Model_Entity_Memberships extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {


        $model = Mage::getModel('membership/types');
        $data = $model->getCollection()->getData();
  

        if ($this->_options === null) {
            $this->_options = array();

            foreach ($data as $value) {
                $this->_options[] = array(
                    'value' => $value['membership_id'],
                    'label' => $value['membership_type']
                );
            }
        }

        return $this->_options;
    }

}
