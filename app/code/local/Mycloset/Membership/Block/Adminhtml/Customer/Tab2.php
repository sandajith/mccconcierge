<?php

class Mycloset_Membership_Block_Adminhtml_Customer_Tab2 extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Set the template for the block
     *
     */
    public function _construct() {
        parent::_construct();
        $this->setTemplate('membership/customer/tab2.phtml');
    }

    public function getCustomerpayments() {
      $customer = Mage::registry('current_customer');
      
        $model = Mage::getModel('membership/payment')->load($customer['entity_id'], 'customer_id');
        $customermembership = Mage::getModel('membership/customermembership')->load($customer['entity_id'], 'customer_id');
        $membership_id = $customermembership['membership_id'];
        $model2 = Mage::getModel('membership/membership')->load($membership_id, 'membership_id');
        $data = array();
        $data['customer_id'] = $customer['entity_id'];
        $data['customer_profileId'] = $model->getCustomerProfileId();
        $data['payment_profileId'] = $model->getPaymentProfileId();
        $data['shipping_addressId'] = $model->getShippingAddressId();
        $data['membership_Id'] = $membership_id;
        $data['membership_price'] = $model2->getMembershipPrice();
        $data['membership_type'] = $model2->getMembershipType();     
        return $data;
    }

    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->__('Membership settings');
    }

    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->__('Cutomer settings section');
    }

    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden() {
        return false;
    }

}
