<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Mycloset_Membership_Helper_Address extends Mage_Core_Helper_Abstract
{
    public function getDefaultPickup(Mage_Customer_Model_Customer $customer)
    {
        return $customer->getData('default_pickup');
    }

    public function getDefaultPickupAddress(Mage_Customer_Model_Customer $customer)
    {
        return $customer->getPrimaryAddress($customer->getData('default_pickup'));
    }
}