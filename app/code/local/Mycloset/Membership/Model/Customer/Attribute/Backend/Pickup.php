<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mycloset_Membership_Model_Customer_Attribute_Backend_Pickup extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $helper = $this->_getHelper();
        $defaultPickup = $helper->getDefaultPickup($object);
        if (is_null($defaultPickup)) {
            $object->setData('default_pickup', null);
        }
    }
    public function afterSave($object)
    {
        if ($defaultPickup = $this->_getHelper()->getDefaultPickup($object))
        {
            $addressId = false;
            /**
             * post_index set in customer save action for address
             * this is $_POST array index for address
             */
            foreach ($object->getAddresses() as $address) {
                if ($address->getPostIndex() == $defaultPickup) {
                    $addressId = $address->getId();
                }
            }
            if ($addressId) {
                $object->setDefaultPickupId($addressId);
                $this->getAttribute()->getEntity()
                    ->saveAttribute($object, $this->getAttribute()->getAttributeCode());
            }
        }
    }
     public function _saveAddresses(Mage_Customer_Model_Customer $customer)
    {
        $defaultBillingId   = $customer->getData('default_billing');
        $defaultShippingId  = $customer->getData('default_shipping');
        $defaultPickupId  = $customer->getData('default_pickup');
        foreach ($customer->getAddresses() as $address) {
            if ($address->getData('_deleted')) {
                if ($address->getId() == $defaultBillingId) {
                    $customer->setData('default_billing', null);
                }
                if ($address->getId() == $defaultShippingId) {
                    $customer->setData('default_shipping', null);
                }
                if ($address->getId() == $defaultPickupId) {
                    $customer->setData('default_pickup', null);
                }
                $address->delete();
            } else {
                $address->setParentId($customer->getId())
                    ->setStoreId($customer->getStoreId())
                    ->setIsCustomerSaveTransaction(true)
                    ->save();
                if (($address->getIsPrimaryBilling() || $address->getIsDefaultBilling())
                    && $address->getId() != $defaultBillingId
                ) {
                    $customer->setData('default_billing', $address->getId());
                }
                if (($address->getIsPrimaryShipping() || $address->getIsDefaultShipping())
                    && $address->getId() != $defaultShippingId
                ) {
                    $customer->setData('default_shipping', $address->getId());
                }
                if (($address->getIsPrimaryPickup() || $address->getIsDefaultPickup())
                    && $address->getId() != $defaultPickupId
                ) {
                    $customer->setData('default_pickup', $address->getId());
                }
            }
        }
        if ($customer->dataHasChangedFor('default_billing')) {
            $this->saveAttribute($customer, 'default_billing');
        }
        if ($customer->dataHasChangedFor('default_shipping')) {
            $this->saveAttribute($customer, 'default_shipping');
        }
        if ($customer->dataHasChangedFor('default_pickup')) {
            $this->saveAttribute($customer, 'default_pickup');
        }

        return $this;
    }

    /**
     * @return mycloset_membership_Helper_Address
     */
    protected function _getHelper()
    {
        return Mage::helper('mycloset_membership/address');
    }
    
}