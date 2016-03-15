<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Mycloset_Membership_OnepageController extends Mage_Checkout_OnepageController {
const PATH_GATE_THRESHOLD = 'membership/general/threshold';
    const PATH_API_LOGIN = 'membership/general/apilogin';
    const PATH_TRANS_KEY = 'membership/general/transkey';
    const PATH_GATE_URL = 'membership/general/gatewayurl';
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

    public function saveShippingMethodAction() {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {

            $shipping_method = $this->getRequest()->getPost('shipping_method');
            switch ($shipping_method) {
                case 'flatrate_flatrate':
                    $shipping_date1 = $this->getRequest()->getPost('inputDatetator1');

                    $dateArray = explode("(", $shipping_date1);

                    if ($dateArray > 0) {
//                        date_default_timezone_set('UTC');
                        date_default_timezone_set('America/New_York');
                        $shipping_date = date('Y-m-d', strtotime(trim($dateArray[0])));
                    }
                    break;
                case 'ups_1DA':
                    $shipping_date = $this->getRequest()->getPost('ups_1DA');
                    break;
                case 'ups_2DA':
                    $shipping_date = $this->getRequest()->getPost('ups_2DA');
                    break;
                case 'ups_GND':
                    $shipping_date = $this->getRequest()->getPost('ups_GND');
                    break;
            }

            $data = $this->getRequest()->getPost('shipping_method', '');

            $result = $this->getOnepage()->saveShippingMethod($data);
            // $result will contain error data if shipping method is empty
            if (!$result) {
                Mage::dispatchEvent(
                        'checkout_controller_onepage_save_shipping_method', array(
                    'request' => $this->getRequest(),
                    'quote' => $this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }

            $this->getOnepage()->getQuote()->collectTotals()->save();

            $this->getOnepage()->getQuote()->setShippingDate($shipping_date)->save();
//          $this->getOnepage()->getQuote()->setShippingDate('2015-12-25')->save();
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
 $ccchange = $this->getRequest()->getPost('cc_change');

// cc change
//if($ccchange){
   
            $customer_id = Mage::getSingleton('customer/session')->getId();
            $mem_amount = Mage::getStoreConfig('membership/general/ccchange');
            $data = $this->getRequest()->getPost();
            $emailid = $this->getRequest()->getPost('emailid');
            $fname = $this->getRequest()->getPost('firstname');
            $lname = $this->getRequest()->getPost('lastname');
            $name_card = $this->getRequest()->getPost('x_card_name');
            $payment_card_exp_year = $this->getRequest()->getPost('card_exp_year');
            $payment_card_exp_month = $this->getRequest()->getPost('card_exp_month');
            $payment_card_code = $this->getRequest()->getPost('x_card_code');
            $number_card = $this->getRequest()->getPost('x_card_num');
            $creditcard = substr($number_card, -4, 4);
            $g_loginname = Mage::getStoreConfig(self::PATH_API_LOGIN); // Keep this secure.      
            $g_transactionkey_encrypt = Mage::getStoreConfig(self::PATH_TRANS_KEY); // Keep this secure.   
            $g_transactionkey = Mage::helper('core')->decrypt($g_transactionkey_encrypt);
            $g_apihost = Mage::getStoreConfig(self::PATH_GATE_URL);
            $g_apipath = "/xml/v1/request.api";
            require_once (Mage::getBaseDir('code') . '/local/Mycloset/Membership/Api/util.php');



// Create new customer profile
            $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                    "<createCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                    MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                    "<profile>" .
                    "<merchantCustomerId>" . time() . rand(1, 100) . "</merchantCustomerId>" . // Your own identifier for the customer.
                    "<description> </description>" .
                    "<email>" . $emailid . "</email>" .
                    "</profile>" .
                    "</createCustomerProfileRequest>";
            $response1 = send_xml_request($g_apihost, $g_apipath, $content);
            $parsedresponse = parse_api_response($response1);
            $parsed_customer_id = $parsedresponse->customerProfileId;
// Add payment profile
            $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                    "<createCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                    MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                    "<customerProfileId>" . $parsed_customer_id . "</customerProfileId>" .
                    "<paymentProfile>" .
                    "<billTo>" .
                    "<firstName>" . $fname . "</firstName>" .
                    "<lastName>" . $lname . "</lastName>" .
                    "<company></company>" .
                    "<address></address>" .
                    "<city></city>" .
                    "<state></state>" .
                    "<zip></zip>" .
                    "<country></country>" .
                    "<phoneNumber></phoneNumber>" .
                    "<faxNumber></faxNumber>" .
                    "</billTo>" .
                    "<payment>" .
                    "<creditCard>" .
                    "<cardNumber>" . $this->getRequest()->getPost('x_card_num') . "</cardNumber>" .
                    "<expirationDate>" . $payment_card_exp_year . '-' . $payment_card_exp_month . "</expirationDate>" . // required format for API is YYYY-MM
                    "</creditCard>" .
                    "</payment>" .
                    "</paymentProfile>" .
                    "<validationMode>none</validationMode>" . // or testMode
                    "</createCustomerPaymentProfileRequest>";
            $response = send_xml_request($g_apihost, $g_apipath, $content);
            $parsedresponse = parse_api_response($response);
            $parsed_paymentprofile_id = $parsedresponse->customerPaymentProfileId;

//Add Shipping address profile
            $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                    "<createCustomerShippingAddressRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                    MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                    "<customerProfileId>" . $parsed_customer_id . "</customerProfileId>" .
                    "<address>" .
                    "<firstName>" . $fname . "</firstName>" .
                    "<lastName>" . $lname . "</lastName>" .
                    "<company></company>" .
                    "<address></address>" .
                    "<city></city>" .
                    "<state></state>" .
                    "<zip></zip>" .
                    "<country></country>" .
                    "<phoneNumber></phoneNumber>" .
                    "</address>" .
                    "</createCustomerShippingAddressRequest>";
            $response = send_xml_request($g_apihost, $g_apipath, $content);
            $parsedresponse = parse_api_response($response);
            $parsed_address_id = $parsedresponse->customerAddressId;
//Making a payment for the customerprofileid
            $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                    "<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                    MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                    "<transaction>" .
                    "<profileTransAuthOnly>" .
                    "<amount>" . $mem_amount . "</amount>" . // should include tax, shipping, and everything.
                    "<shipping>" .
                    "<amount>" . $mem_amount . "</amount>" .
                    "<name>Free Shipping</name>" .
                    "<description> My Closet Concierge </description>" .
                    "</shipping>" .
                    "<lineItems>" .
                    "<itemId>" . time() . "</itemId>" .
                    "<name>Change credit card</name>" .
                    "<description> Changed credit card number</description>" .
                    "<quantity>1</quantity>" .
                    "<unitPrice>" . $mem_amount . "</unitPrice>" .
                    "<taxable>false</taxable>" .
                    "</lineItems>" .
                    "<customerProfileId>" . $parsed_customer_id . "</customerProfileId>" .
                    "<customerPaymentProfileId>" . $parsed_paymentprofile_id . "</customerPaymentProfileId>" .
                    "<customerShippingAddressId>" . $parsed_address_id . "</customerShippingAddressId>" .
                    "<order>" .
                    "<invoiceNumber>" . "MCC" . $parsed_customer_id . "</invoiceNumber>" .
                    "</order>" .
                    "</profileTransAuthOnly>" .
                    "</transaction>" .
                    "</createCustomerProfileTransactionRequest>";
            $response = send_xml_request($g_apihost, $g_apipath, $content);
            $parsedresponse = parse_api_response($response);
            if (isset($parsedresponse->directResponse)) {
                $directResponseFields = explode(",", $parsedresponse->directResponse);
                $responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
                $responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
                $responseReasonText = $directResponseFields[3];
                $approvalCode = $directResponseFields[4]; // Authorization code
                $transId = $directResponseFields[6];
//Variables to send e-mail
                $z_firstname = $fname;
                $z_lastname = $lname;
                $z_email = $emailid;
                $z_memtype = $this->getRequest()->getPost('mem_type');
                $z_amount = $mem_amount;
                if ("1" == $responseCode) {
//Email sending to the customer upon successful payment
                    $templateId = 'Change credit card';
                    $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
                    $vars = array('first_name' => $z_firstname, 'last_name' => $z_lastname, 'email' => $z_email, 'mem_type' => $z_memtype, 'mem_amt' => $z_amount);
                    $emailTemplate->getProcessedTemplate($vars);
                    $admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
                    $admin_name = Mage::getStoreConfig('trans_email/ident_general/name');
// $email = array($admin_email,$z_email);
                    $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
                    $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
                    $emailTemplate->send($z_email, $z_firstname . ' ' . $z_lastname, $vars);
                    $emailTemplate->send($admin_email, $admin_name, $vars);
                    $paymentdetails = serialize($vars);
                    $date = date("Y-m-d H:i:s ", time());
                    $model = Mage::getModel('membership/payment')->load($customer_id, 'customer_id')
                                    ->setCustomerId($customer_id)
                                    ->setCustomerProfileId($parsed_customer_id)
                                    ->setPaymentProfileId($parsed_paymentprofile_id)
                                    ->setShippingAddressId($parsed_address_id)
                                    ->setCreditcardNum($creditcard)
                                    ->setNameCreditcard($name_card)
                                    ->save()->getId();
                    $insertId = $model;
                    $payment_id = $insertId;
                    $j = Mage::getModel('membership/paymenthistory');
                    $j->setCustomerId($customer_id)
                            ->setTransactionId($transId)
                            ->setPaymentId($payment_id)
                            ->setPaymentDetails($paymentdetails)
                            ->setAmountPaid($mem_amount)
                            ->setTaxRate(0)
                            ->setMembershipAmount(1)
                            ->save();
                   
                }
//                 $result['redirect'] = $redirectUrl;
            }
             
//            end cc change
//            }
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

    public function updategatewayAction() {

        if ($this->getRequest()->isPost()) {
            $postdata = $this->getRequest()->getPost();
            print_r($postdata);
        }
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


            $info = serialize($final);
//            $shippingdate = $this->getOnepage()->getQuote()->getShippingDate();
//            $fp = fopen('shippingdate.txt', 'a+');
//            fwrite($fp, print_r($shippingdate, true));
//            fclose($fp);
            $quoteItem = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');
            $quoteItem->setShippingComment($this->getOnepage()->getQuote()->getShippingComment());
            $quoteItem->setShippingDate($this->getOnepage()->getQuote()->getShippingDate());
            $quoteItem->setOtherData($info);
            $quoteItem->save();
            $userid = Mage::getSingleton('customer/session')->getId();
            $freeshipping = Mage::getModel('membership/customermembership')
                            ->load($userid, 'customer_id')
                            ->setFreeshippingFlag(0)->save();


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
