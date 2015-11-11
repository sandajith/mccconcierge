<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Mycloset_Membership_OnepageController extends Mage_Checkout_OnepageController {

//    public function saveExcellenceAction() {
//
//     if ($this->_expireAjax()) {
//            return;
//        }
//        if ($this->getRequest()->isPost()) {
////             echo  $data = $this->getRequest()->getPost();
//          
//            $data = $this->getRequest()->getPost('excellence', array());
//
//            $result = $this->getOnepage()->saveExcellence($data);
//// 
////            if (!isset($result['error'])) {
//            $result['goto_section'] = 'shipping_method';
//            $result['update_section'] = array(
//                'name' => 'shipping-method',
//                'html' => $this->_getShippingMethodsHtml()
//            );
////            }
//
//            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
//        }
//    }

//    public function saveBillingAction() {
//        if ($this->_expireAjax()) {
//            return;
//        }
//        if ($this->getRequest()->isPost()) {
//            //            $postData = $this->getRequest()->getPost('billing', array());
//            //            $data = $this->_filterPostData($postData);
//            $data = $this->getRequest()->getPost('billing', array());
//            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
//
//            if (isset($data['email'])) {
//                $data['email'] = trim($data['email']);
//            }
//            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
//
//            if (!isset($result['error'])) {
//                /* check quote for virtual */
//                if ($this->getOnepage()->getQuote()->isVirtual()) {
//                    $result['goto_section'] = 'payment';
//                    $result['update_section'] = array(
//                        'name' => 'payment-method',
//                        'html' => $this->_getPaymentMethodsHtml()
//                    );
//                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
//                    $result['goto_section'] = 'excellence';  //Goes to our step
//                    $result['allow_sections'] = array('shipping');
//                    $result['duplicateBillingInfo'] = 'true';
//                } else {
//                    $result['goto_section'] = 'shipping';
//                }
//            }
//
//            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
//        }
//    }

//    public function saveShippingAction() {
//  
//        if ($this->_expireAjax()) {
//            return;
//        }
//        if ($this->getRequest()->isPost()) {
//            $data = $this->getRequest()->getPost('shipping', array());
//            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
//            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
//
//            if (!isset($result['error'])) {
//                $result['goto_section'] = 'excellence'; //Go to our step
//            }
//             $result['goto_section'] = 'excellence'; //Go to our step
//            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
//        }
//    }

    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                
             // Mage::getModel('sales/')->load($this->getOnepage()->getQuote()->getId());  
                $this->getOnepage()->getQuote()->setShippingComment($this->getRequest()->getPost('shippingcomments'))->save();
                
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}
