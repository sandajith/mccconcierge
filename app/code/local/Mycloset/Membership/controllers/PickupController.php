<?php

class Mycloset_Membership_PickupController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {


        $this->loadLayout();
        $this->renderLayout();
    }

    public function postAction() {
        $userid = Mage::getSingleton('customer/session')->getId();
        if (empty($userid)) {
            $this->_redirect('/membership/account/login/');
        } else {
            $vars = Mage::app()->getRequest()->getPost();

            if (!empty($vars)) {
                $userid = Mage::getSingleton('customer/session')->getId();
                $values = Mage::app()->getRequest()->getParams();
                $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('customer_id', $userid);

                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $final = array();
                    $i = 0;
                    foreach ($values['categoryname'] as $cat_id) {
                        $final[$i]['cat_id'] = $cat_id;
                        $category = Mage::getModel('catalog/category')->load($final[$i]['cat_id']);
                        $image = Mage::getBaseUrl('media') . 'catalog/category/' . $category->image;
                        $catname = $category->name;
                        $final[$i]['quantity'] = $values['quantity'][$cat_id];
                        $i++;
                    }

                    $comment = $values['comment'];
                    $info = serialize($final);
                    $content = ' ';
                    foreach ($final as $pickupinfo) {
                        $category = Mage::getModel('catalog/category')->load($pickupinfo['cat_id']);
                        $image = Mage::getBaseUrl('media') . 'catalog/category/' . $category->image;
                       $content.= '<tr>
                       <td v-align="middle"><img src="'. $image.'" alt="mycloset" width="80" height="80" border="0" /></td>
                       <td> ' . $category->getName() . ' </td>
                       <td style="text-align:center">' . $pickupinfo['quantity'] . '</td> 
                    </tr>';
                
                    }
                    $data = array(
                        'pickup_user_id' => $userid,
                        'pickup_info' => $info,
                        'pickup_comment' => $values['comment']
                    );
                       
                    $model = Mage::getModel('membership/pickupdetails')->setData($data);
                    $model->save()->getId();
                    $model = Mage::getModel('membership/pickupdetails')->setData($data);
                    $model->save()->getId();

                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    $templateId = 'Pickup mail to user';
                    $user_name = $customer->getName();
                    $user_email = $customer->getEmail();
                    $admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
                    $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
                    $varables = array(
                        'user_name' => $user_name,
                        'comment' => $comment,
                        'content' => $content
                    );

                    $email = $user_email;
                    $emailTemplate->getProcessedTemplate($vars);
                    $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
                    $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));                   
                    $emailTemplate->send($email, 'Testing mail', $varables);
                    $emailTemplate->send($admin_email, 'Testing mail', $varables);
                    $this->_redirect('mycloset/pickup/success');
                } else {
                    $this->_redirect('/membership/account/login/');
                }
            }
        }
    }

    public function successAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
