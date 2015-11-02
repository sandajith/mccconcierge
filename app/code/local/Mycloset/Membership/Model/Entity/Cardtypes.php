<?php

class Mycloset_Membership_Model_Entity_Cardtypes extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {

        $model = Mage::getModel('membership/card');
        $data = $model->getCollection()->getData();

        if ($this->_options === null) {
            $this->_options = array();
            foreach ($data as $value) {
                $this->_options[] = array(
                    'value' => $value['card_id'],
                    'label' => $value['card_type']
                );
            }
        }
        return $this->_options;
    }

}
