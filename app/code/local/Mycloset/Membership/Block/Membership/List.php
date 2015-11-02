<?php

class Mycloset_Membership_Block_Membership_List extends Mage_Core_Block_Template {

    const PATH_GATE_URL = 'membership/general/gatewayurl';
    const PATH_API_LOGIN = 'membership/general/apilogin';
    const PATH_MER_EMAIL = 'membership/general/merchantmail';
    const PATH_TRANS_KEY = 'membership/general/transkey';

    public function getMemberships() {

        $model = Mage::getModel('membership/types');
        $data = $model->getCollection()->getData();
        $mem = '<option value="">Select membership</option>';
        foreach ($data as $value) {
            $mem .= '<option value=' . $value['membership_id'] . '>' . $value['membership_type'] . "-" . $value['membership_price'] . '</option>';
        }

        return $mem;
    }

    public function getMembershipdetails() {

        $customer_id = Mage::getSingleton('customer/session')->getMemId();
        if (!$customer_id) {
            $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $data = array();
        $data['customer_email'] = $customer->getEmail();
        $data['customer_custtele'] = $customer->getCusTele();
        $data['customer_group'] = $customer->getGroupId();
        $data['customer_id'] = $customer->getId();
        $customer_membership = Mage::getModel('membership/customermembership')->load($customer_id, 'customer_id');
        $membership_id = $customer_membership->getMembershipId();
        $one = Mage::getModel('membership/types')->load($membership_id);
        $data['membership_id'] = $membership_id;
        $data['membership_type'] = $one->getMembershipType();
        $data['membership_price'] = $one->getMembershipPrice();


        return $data;
    }

    public function getSettings() {
        $setting['a'] = Mage::getStoreConfig(self::PATH_GATE_URL);
        $setting['b'] = Mage::getStoreConfig(self::PATH_API_LOGIN);
        $setting['c'] = Mage::getStoreConfig(self::PATH_MER_EMAIL);
        $setting['d'] = Mage::getStoreConfig(self::PATH_TRANS_KEY);
        //return Mage::getStoreConfig(self::PATH_TRANS_KEY);
        return $setting;
    }

}

//Mage::getSingleton('customer/session')->getMemId();