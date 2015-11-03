<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Mycloset_Membership_Model_Adminhtml_Observer
{
    public function checkpickupAddress($observer)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getCustomer();
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getRequest();
        $data = $request->getPost();
        if (isset($data['account']['default_pickup'])) {
            $customer->setData('default_pickup', $data['account']['default_pickup']);
        }
    }
}