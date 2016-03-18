<?php

class Mycloset_Membership_PaymentController extends Mage_Core_Controller_Front_Action {

    const PATH_GATE_THRESHOLD = 'membership/general/threshold';
    const PATH_API_LOGIN = 'membership/general/apilogin';
    const PATH_TRANS_KEY = 'membership/general/transkey';
    const PATH_GATE_URL = 'membership/general/gatewayurl';

    public function IndexAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Payment | My Closet Concierge"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Mycloset"),
            "title" => $this->__("My closet"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("payment", array(
            "label" => $this->__("Payment"),
            "title" => $this->__("payment")
        ));

        $this->renderLayout();
    }

    public function GetpriceAction() {
        $finalPrice = 0;
        $value = Mage::app()->getRequest()->getParam('memid');
        if ($value) {
            $model = Mage::getModel('membership/types')->load($value);
            $finalPrice = $model->getMembershipPrice();
            $details = $model->getDetails();
            $membershipType = $model->getMembershipType();
            $membershipId = $model->getMembershipId();
        }
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currency_symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();
        echo $currency_symbol . $finalPrice . '<br>' . $details . '@' . $finalPrice . '@' . $membershipType . '@' . $membershipId;
    }

    public function confirmpaymentAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function updategatewayAction() {

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

        $quote = Mage::getSingleton('checkout/session')->getQuote();

        $billingAddress = $quote->getBillingAddress();
//        print_r($billingAddress);
       
        $company = $billingAddress->getCompany();
        $streets = $billingAddress->getstreet();
        $street1 = $streets[0];
        $street2 = $streets[1];
        if ($street2) {
            $street = $street1 . ', ' . $street2;
        } else {
            $street = $street1;
        }
        $city = $billingAddress->getCity();
        $region = $billingAddress->getRegion();
        $zipcode = $billingAddress->getPostcode();
        
        $country_code = $billingAddress->getCountryId();
        $Country_name = Mage::app()->getLocale()->getCountryTranslation($country_code);
        $telephone = $billingAddress->getTelephone();
        $fax = $billingAddress->getFax();
      

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
                "<company>$company</company>" .
                "<address>$street</address>" .
                "<city>$city</city>" .
                "<state>$region</state>" .
                "<zip>$zipcode</zip>" .
                "<country>$Country_name</country>" .
                "<phoneNumber>$telephone</phoneNumber>" .
                "<faxNumber>$fax</faxNumber>" .
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
                "<company>$company</company>" .
                "<address>$street</address>" .
                "<city>$city</city>" .
                "<state>$region</state>" .
                "<zip>$zipcode</zip>" .
                "<country>$Country_name</country>" .
                "<phoneNumber>$telephone</phoneNumber>" .
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
        $error_msg = strrchr($parsedresponse, "Error");
        if ($error_msg) {
            $error_msg_string = "Payment failed by invalid element";
            Mage::getSingleton('core/session')->addError($error_msg_string);
            if ($data['checkout'] == 'no') {
                $this->_redirect('mycloset/account/changecreditcard/');
            } else {
                $this->_redirect('checkout/onepage/');
            }
        }
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
                if ($data['checkout'] == 'no') {
                    Mage::getSingleton('core/session')->addSuccess('Successfully changed Credit Card details ');
                    $this->_redirect('mycloset/account/changecreditcard/');
                } else {
                    Mage::getSingleton('core/session')->addSuccess('Successfully changed Credit Card details ');
                    $this->_redirect('checkout/onepage/');
                }
            }
        }
    }

    public function authorizepaymentAction() {
        $data = $this->getRequest()->getPost();
        $payment_emailid = $this->getRequest()->getPost('emailid');
        $payment_custid = $this->getRequest()->getPost('cust_id');
        $payment_memtype = $this->getRequest()->getPost('mem_type');
        $payment_createaddr = $this->getRequest()->getPost('create_address');
        $fname = $this->getRequest()->getPost('firstname');
        $lname = $this->getRequest()->getPost('lastname');
        $payment_company = $this->getRequest()->getPost('company');
        $payment_street = $this->getRequest()->getPost('street');
        $payment_street1 = $payment_street[0];
        $payment_street2 = $payment_street[1];
        $memcity = $this->getRequest()->getPost('city');
        $payment_regionid = $this->getRequest()->getPost('region_id');
        $region = Mage::getModel('directory/region')->load($payment_regionid);
        $region_name = $region->getName();
        $postalcode = $this->getRequest()->getPost('postcode');
        $country_code = $this->getRequest()->getPost('country_id');
        $MemCountry_name = Mage::app()->getLocale()->getCountryTranslation($country_code);
        $telephone = $this->getRequest()->getPost('cus_tele');
        $payment_fax = $this->getRequest()->getPost('fax');
        $payment_default_billing = $this->getRequest()->getPost('default_billing');
        $payment_default_shipping = $this->getRequest()->getPost('default_shipping');
        $name_card = $this->getRequest()->getPost('x_card_name');
        $payment_card_exp_year = $this->getRequest()->getPost('card_exp_year');
        $payment_card_exp_month = $this->getRequest()->getPost('card_exp_month');
        $payment_card_code = $this->getRequest()->getPost('x_card_code');
        $number_card = $this->getRequest()->getPost('x_card_num');
        $creditcard = substr($number_card, -4, 4);


        if ($payment_street2) {
            $street = $payment_street1 . ', ' . $payment_street2;
        } else {
            $street = $payment_street1;
        }

        $z_memtype1 = $this->getRequest()->getPost('mem_table_id');
        $taxrate = $this->getRequest()->getPost('tax_rate');
        $emailid = $this->getRequest()->getPost('emailid');
        $customerid = $this->getRequest()->getPost('cust_id');

        if ($taxrate === '') {
            $mem_amount = $this->getRequest()->getPost('amt');
        } else {
            $taxrate;
            $taxval = $taxrate / 100;
            $taxable_amount = $this->getRequest()->getPost('amt') * $taxval;
            $amt = $this->getRequest()->getPost('amt');
            $mem_amount = $amt + $taxable_amount;
        }

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
                "<description>" . $this->getRequest()->getPost('mem_type') . " of " . $fname . "</description>" .
                "<email>" . $emailid . "</email>" .
                "</profile>" .
                "</createCustomerProfileRequest>";


        $response = send_xml_request($g_apihost, $g_apipath, $content);
        $parsedresponse = parse_api_response($response);


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
                "<company>" . $payment_company . "</company>" .
                "<address>" . $street . "</address>" .
                "<city>" . $memcity . "</city>" .
                "<state>" . $region_name . "</state>" .
                "<zip>" . $postalcode . "</zip>" .
                "<country>" . $MemCountry_name . "</country>" .
                "<phoneNumber>" . $telephone . "</phoneNumber>" .
                "<faxNumber>" . $payment_fax . "</faxNumber>" .
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
                "<company>" . $payment_company . "</company>" .
                "<address>" . $street . "</address>" .
                "<city>" . $memcity . "</city>" .
                "<state>" . $region_name . "</state>" .
                "<zip>" . $postalcode . "</zip>" .
                "<country>" . $MemCountry_name . "</country>" .
                "<phoneNumber>" . $telephone . "</phoneNumber>" .
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
                "<amount>0.00</amount>" .
                "<name>Free Shipping</name>" .
                "<description> My Closet Concierge </description>" .
                "</shipping>" .
                "<lineItems>" .
                "<itemId>" . time() . "</itemId>" .
                "<name>" . $this->getRequest()->getPost('mem_type') . "</name>" .
                "<description>" . $this->getRequest()->getPost('mem_type') . " of " . $fname . "</description>" .
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
        $error_msg = strrchr($parsedresponse, "Error");
        if ($error_msg) {
            $error_msg_string = "Payment failed by invalid element";
            Mage::getSingleton('core/session')->addError($error_msg_string);
            $this->_redirect('mycloset/payment');
        }

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
                $templateId = 'Success Mycloset Registration';
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
                $data = array(
                    'customer_id' => $customerid,
                    'customer_profile_id' => $parsed_customer_id,
                    'payment_profile_id' => $parsed_paymentprofile_id,
                    'shipping_address_id' => $parsed_address_id,
                    'creditcard_num' => $creditcard,
                    'name_creditcard' => $name_card
                );
                $model = Mage::getModel('membership/payment')->setData($data);
                $insertId = $model->save()->getId();
                $payment_id = $insertId;
                $j = Mage::getModel('membership/paymenthistory');
                $j->setCustomerId($customerid)
                        ->setTransactionId($transId)
                        ->setPaymentId($payment_id)
                        ->setPaymentDetails($paymentdetails)
                        ->setAmountPaid($mem_amount)
                        ->setTaxRate($taxrate)
                        ->setMembershipAmount($this->getRequest()->getPost('amt'))
                        ->save();
//logging-in the customer after successful payment
                $session = $this->_getSession();
                $customer = Mage::getModel('customer/customer')->load($customerid);
                $customer->setData('created_at', Mage::getModel('core/date')->gmtDate());
//changing customer group
                $customerid = $customerid;
                $update = array(
                    'entity_id' => $customerid,
                    'group_id' => '1',
                    'firstname' => $fname,
                    'lastname' => $lname,
                    'country' => $MemCountry_name,
                    'postcode' => $postalcode,
                    'telephone' => $telephone,
                    'fax' => $payment_fax
                );
                $customer->addData($update);
                $customer->setId($customerid)->save();
                $address123 = Mage::getModel("customer/address")->load($payment_default_shipping);
                $address123->setCustomerId($customerid)
                        ->setFirstname($fname)
                        ->setLastname($lname)
                        ->setCountryId($country_code)
                        ->setRegionId($payment_regionid)
                        ->setPostcode($postalcode)
                        ->setCompany($payment_company)
                        ->setCity($memcity)
                        ->setTelephone($telephone)
                        ->setFax($payment_fax)
                        ->setStreet(array($payment_street1, $payment_street2))
                        ->setIsDefaultBilling('1')
                        ->setIsDefaultShipping('1')
                        ->setSaveInAddressBook('1');
                try {
                    $address123->save();
                    $address123->setConfirmation(null);
                    $address123->save();
                } catch (Exception $e) {
                    
                }
                $jyuy = Mage::getModel('membership/customermembership')
                        ->load($customerid);
                $membershiphistory = Mage::getModel('membership/membershiphistory');
                $membershiphistory->setCustomerId($customerid)
                        ->setMembershipId($z_memtype1);
                try {
                    $model->setId($customerid)->save();
                    if ($jyuy->getId()) {
                        $jyuy->setMembershipId($z_memtype1)->save();
                    } else {
                        Mage::getModel('membership/customermembership')->setCustomerId($customerid)->setMembershipId($z_memtype1)->save();
                    }
                    $membershiphistory->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
//changing customer group end
                $session->setCustomerAsLoggedIn($customer);
                $this->_redirect('mycloset/payment/confirmpayment');

//echo "The transaction was successful.<br>";
            } else if ("2" == $responseCode) {
                $log_path = Mage::getBaseDir('code') . '/local/Mycloset/Membership/Api/authorizenet.txt';
                $fp = fopen($log_path, 'a+');
                fwrite($fp, print_r($parsedresponse, true));
                fclose($fp);
                $message = "The transaction was declined.<br>";
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('mycloset/payment');
            } else {
                $message = "Payment failed !" . htmlspecialchars($responseReasonText) . "<br>";
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('mycloset/payment');
            }
        }
//                }
    }

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function paymeAction() {
        $customerid = $this->getRequest()->getPost('customer_entity_id');
        $include_membershipcharge = $this->getRequest()->getPost('include_membershipcharge');


        $payment_details = array();
        $g_loginname = Mage::getStoreConfig(self::PATH_API_LOGIN); // Keep this secure.
        $g_transactionkey = Mage::getStoreConfig(self::PATH_TRANS_KEY); // Keep this secure.
        $g_apihost = Mage::getStoreConfig(self::PATH_GATE_URL);
        $g_apipath = "/xml/v1/request.api";
        require_once (Mage::getBaseDir('code') . '/local/Mycloset/Membership/Api/util.php');
        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                "<transaction>" .
                "<profileTransAuthOnly>" .
                "<amount>" . $this->getRequest()->getPost('amount') . "</amount>" . // should include tax, shipping, and everything.
                "<shipping>" .
                "<amount>0.00</amount>" .
                "<name>Free Shipping</name>" .
                "<description>My Closet Concierge</description>" .
                "</shipping>" .
                "<lineItems>" .
                "<itemId>" . time() . "</itemId>" .
                "<name>" . $this->getRequest()->getPost('mem_type_name') . "</name>" .
                "<description>Membership Renewal/Upgrade</description>" .
                "<quantity>1</quantity>" .
                "<unitPrice>" . $this->getRequest()->getPost('servicesum') . "</unitPrice>" .
                "<taxable>false</taxable>" .
                "</lineItems>" .
                "<customerProfileId>" . $this->getRequest()->getPost('customer_pro_id') . "</customerProfileId>" .
                "<customerPaymentProfileId>" . $this->getRequest()->getPost('customer_payment_id') . "</customerPaymentProfileId>" .
                "<customerShippingAddressId>" . $this->getRequest()->getPost('customer_address_id') . "</customerShippingAddressId>" .
                "<order>" .
                "<invoiceNumber>" . "MCC" . $this->getRequest()->getPost('customer_address_id') . "</invoiceNumber>" .
                "</order>" .
                "</profileTransAuthOnly>" .
                "</transaction>" .
                "</createCustomerProfileTransactionRequest>";

// product count
        $payment_details['product_count'] = $this->getRequest()->getPost('myclosetcount');
//storage price
        $payment_details['storage_price'] = $this->getRequest()->getPost('storeamt');
// Additional payments comment
        $payment_details['comment'] = $this->getRequest()->getPost('comment');
//oreder charges
        $payment_details['service_sum'] = $this->getRequest()->getPost('servicesum');
// Amount to be paid
        $payment_details['amount_paid'] = $this->getRequest()->getPost('amount');
//serialized array for payment_details
        $payment_details1 = serialize($payment_details);

        $response = send_xml_request($g_apihost, $g_apipath, $content);
        $parsedresponse = parse_api_response($response);

        if (isset($parsedresponse->directResponse)) {
            $directResponseFields = explode(",", $parsedresponse->directResponse);
            $responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error       
            $responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
            $responseReasonText = $directResponseFields[3];
            $approvalCode = $directResponseFields[4]; // Authorization code
            $transId = $directResponseFields[6];
            if ("1" == $responseCode) {
                $data = array(
                    'customer_id' => $customerid,
                    'transaction_id' => $transId,
                    'payment_details' => $payment_details1,
                    'amount_paid' => $this->getRequest()->getPost('amount'),
                    'monthly_payment' => '1'
                );

                $model = Mage::getModel('membership/paymenthistory')->setData($data);
                $model->save();

                $path = $this->getRequest()->getPost('return_url') . '?q=success' . '&tranid=' . $transId;
                $this->_redirectUrl($path);
//// Automatically changed  invoice/ship status to 'complete' after payment
                $ordernum = $this->getRequest()->getPost('order_id');
                foreach ($ordernum as $order_id) {
                    $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
//////START Handle Invoice
                    if ($order->canInvoice()) {
                        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                        $invoice->register();
                        $invoice->getOrder()->setCustomerNoteNotify(false);
                        $invoice->getOrder()->setIsInProcess(true);
                        $transactionSave = Mage::getModel('core/resource_transaction')
                                ->addObject($invoice)
                                ->addObject($invoice->getOrder());
                        $order->addStatusHistoryComment('Invoice processing by MyCloset Admin.', false);
                        $transactionSave->save();
                        if ($include_membershipcharge) {
                            $freeshipping = Mage::getModel('membership/customermembership')
                                            ->load($customerid, 'customer_id')
                                            ->setFreeshippingFlag(0)->save();
                        }
                    }
                }
            }
//// END CODE Automatically changed  invoice/ship status to 'complete' after payment  
        } else if ("2" == $responseCode) {
            $path = $this->getRequest()->getPost('return_url') . '?q=error';
            $this->_redirectUrl($path);
        } else {
            $path = $this->getRequest()->getPost('return_url') . '?q=error';
            $this->_redirectUrl($path);
        }
    }

    public function settingsAction() {

        $upgrade_mem_id = $this->getRequest()->getPost('mem_upgrade');
        $customer_id = $this->getRequest()->getPost('customer_id');
        $lock_grp_id = $this->getRequest()->getPost('locak_member');
        $close_grp_id = $this->getRequest()->getPost('close_mem');
        $unlock_grp_id = $this->getRequest()->getPost('unlock_mem');
        $clc_grp_id = $this->getRequest()->getPost('cls_mem');
        $nonpaid_grp_id = $this->getRequest()->getPost('non_paid');
// insert data into customer membership table
        $update123 = array(
            'customer_id' => $customer_id,
            'membership_id' => $upgrade_mem_id
        );
        $custmember = Mage::getModel('membership/customermembership')
                ->load($customer_id, 'customer_id')
                ->addData($update123);
        try {
            $custmember->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

// insert data in to history 
        $history_data = array(
            'customer_id' => $customer_id,
            'membership_id' => $upgrade_mem_id
        );
        $history = Mage::getModel('membership/membershiphistory')->load()->addData($history_data);
        try {
            $history->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

// lock membership
        $lock = array(
            'entity_id' => $customer_id,
            'group_id' => $lock_grp_id
        );
        $lockmodel = Mage::getModel('customer/customer')->load($customer_id, 'customer_id')->addData($lock);
        $lockmodel->setData('created_at', Mage::getModel('core/date')->gmtDate());
        try {
            $lockmodel->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
// close membership from member 
        $close = array(
            'entity_id' => $customer_id,
            'group_id' => $close_grp_id
        );
        $closemodel = Mage::getModel('customer/customer')->load($customer_id, 'customer_id')->addData($close);
        $closemodel->setData('created_at', Mage::getModel('core/date')->gmtDate());
        try {
            $closemodel->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
// unlock member from notice period
        $unlock = array(
            'entity_id' => $customer_id,
            'group_id' => $unlock_grp_id
        );
        $unlockmodel = Mage::getModel('customer/customer')->load($customer_id, 'customer_id')->addData($unlock);
        $unlockmodel->setData('created_at', Mage::getModel('core/date')->gmtDate());
        try {
            $unlockmodel->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
// cls member from notice period
        $cls = array(
            'entity_id' => $customer_id,
            'group_id' => $clc_grp_id
        );
        $clsmodel = Mage::getModel('customer/customer')->load($customer_id, 'customer_id')->addData($cls);
        $clsmodel->setData('created_at', Mage::getModel('core/date')->gmtDate());
        try {
            $clsmodel->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
// move meber to nonpaid  from waiting list

        $nonpaid = array(
            'entity_id' => $customer_id,
            'group_id' => $nonpaid_grp_id
        );
        $nonpaidmodel = Mage::getModel('customer/customer')->load($customer_id, 'customer_id')->addData($nonpaid);
        $nonpaidmodel->setData('created_at', Mage::getModel('core/date')->gmtDate());
        try {
            $nonpaidmodel->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
//changing customer group end
        $path = $this->getRequest()->getPost('return_url') . '?q=closed';
        $this->_redirectUrl($path);
    }

    public function testAction() {
        $templateId = 'Success Mycloset Registration';
        $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
        $vars = array('first_name' => $z_firstname, 'last_name' => $z_lastname, 'email' => $z_email, 'mem_type' => $z_memtype, 'mem_amt' => $z_amount);
        $emailTemplate->getProcessedTemplate($vars);
        $admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
        $email = array('neenurobin28@gmail.com', 'neenwil@gmail.com');
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
        $emailTemplate->send('neenurobin28@gmail.com', 'Mycloset mail', $vars);
        $emailTemplate->send('neenurobin28@gmail.com', 'Mycloset mail', $vars);
    }

}
