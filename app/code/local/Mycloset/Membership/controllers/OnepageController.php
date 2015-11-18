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

    public function saveShippingAction() {
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

    public function savePaymentAction() {
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

            // get section and redirect data
//            $this->getOnepage()->getQuote()->setShippingComment($this->getRequest()->getPost('shippingcomments'))->save();
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();

            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getOrder() {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');
            if (!$this->_order->getId()) {
                throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
            }
        }
        return $this->_order;
    }

    /**
     * Create order action
     */
    public function saveOrderAction() {


        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*');
            return;
        }

        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));

                $diff = array_diff($requiredAgreements, $postedAgreements);
                if ($diff) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }

            $data = $this->getRequest()->getPost('payment', array());
            if ($data) {
                $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }

            $this->getOnepage()->saveOrder();
                $rr = Mage::app()->getRequest()->getPost();
//            $rr = $this->getRequest()->getPost();
 $final = array();
                    $i = 0;
                    foreach ($rr['quantity'] as $key => $val) {
                       $final[$i]['cat_id'] = $key;
                        $category = Mage::getModel('catalog/category')->load($final[$i]['cat_id']);
                       
                        $final[$i]['categoryname'] = $category->name;
                        $final[$i]['quantity'] = $val['quantity'];
                       $i++;
                    }

//            $fp = fopen('data.txt', 'a+');
//            fwrite($fp, print_r($final, true));
//            fclose($fp);
                     $info = serialize($final);
                    
            $quoteItem = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');
            $quoteItem->setShippingComment($this->getOnepage()->getQuote()->getShippingComment());
            $quoteItem->setOtherData($info);
            $quoteItem->save();

            
               if ($quoteItem->getCanSendNewEmailFlag()) {
                try {
                    $quoteItem->queueNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            
            
            
            
            
            
            
            
            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error'] = false;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
            if ($gotoSection) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }
            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
            if ($updateSection) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

}
