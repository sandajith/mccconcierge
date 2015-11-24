<?php

class Mycloset_Membership_Block_Adminhtml_Customer_Tab extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Set the template for the block
     *
     */
    public function _construct() {
        parent::_construct();
        $this->setTemplate('membership/customer/tab.phtml');
    }

    public function getCustomerpayment() {
        $customer = Mage::registry('current_customer');
        $model = Mage::getModel('membership/payment')->load($customer['entity_id'], 'customer_id');
        $cusid = $model->getCustomerId();
        $customermembership = Mage::getModel('membership/customermembership')->load($cusid, 'customer_id');
        $customermembership->getMembershipId();
        $membership_id = $customermembership->getMembershipId();
        $model2 = Mage::getModel('membership/membership')->load($membership_id, 'membership_id');
      
        $info = array();
        $info['customer_pro_id'] = $model->getCustomerProfileId();
        $info['customer_payment_id'] = $model->getPaymentProfileId();
        $info['customer_address_id'] = $model->getShippingAddressId();
        $info['getamount'] = $model2->getMembershipPrice();
        $info['membership_id'] = $customermembership->getMembershipId();
        $info['getmembership_type'] = $model2->getMembershipType();
//        order details
        
        return $info;
    }

    public function getChosenservices() {
        $customer = Mage::registry('current_customer');
//  echo $customer;
//echo  Mage::app()->getRequest()->getParam('id')
        $collection = Mage::getModel('membership/chosenservices')->getCollection()->addFieldToFilter('customer_id', $customer['entity_id']);

//$collection = Mage::getModel('membership/chosenservices')->getCollection()->addFieldToFilter('customer_id', Mage::app()->getRequest()->getParam('id'));
//$keys = array_keys($collection->getServiceName()->getData());
        $options = array();
        if (count($collection)) {
            foreach ($collection as $val) {
                $model = Mage::getModel('membership/chosenservices')->load($val->getId());
                $options[] = array(
                    'servicename' => $model->getServiceName(),
                    'amount' => $model->getAmount(),
                    'timestamp' => $model->getTimestamp()
                );
            }
        }
        return $options;
    }

    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->__('Membership payments');
    }

    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->__('Cutomer payments section');
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
