<?php

class Mycloset_Membership_MemberaccountController extends Mage_Core_Controller_Front_Action {

public function memberloginAction()
{
    $webips = Mage::getStoreConfig('membership/general/ipaddress');
    $ips = explode(',',$webips);
    $current_ip = $_SERVER['REMOTE_ADDR'];
    if (in_array($current_ip, $ips)) 
    {
        $customerId = $this->getRequest()->getParam('id');
   
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if ($customer->getWebsiteId()) {
            $session = Mage::getSingleton('customer/session');
            $session->loginById($customerId);
            header('Location: my-closet.html');exit;
           // $this->_redirect("my-closet.html");
        }
    }else
    {
        $this->_redirect('*/account/login');
    }
}
}