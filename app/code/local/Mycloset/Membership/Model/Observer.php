<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mycloset_Membership_Model_Observer {

    public function catalogProductSaveBefore($observer) {
        
        $product = $observer->getEvent()->getProduct();
        
        $productid = $product->getId();
       $productStatus = $product->getProductStatus();
       if($productStatus){
           
      
        if ($productid = '') {
            $this->catalogProductEditBefore($observer);
        } else {
            //category name
            $categoryIds = $product->getCategoryIds();
            if (count($categoryIds)) {
                $firstCategoryId = $categoryIds[1];
                $_category = Mage::getModel('catalog/category')->load($firstCategoryId);
                $categoryname[0] = str_split($_category->getName());
                foreach ($categoryname as $catname1) {
                    $catname .=$catname1[0] . $catname1[1];
                }
            }
            $customerData = Mage::getModel('customer/customer')->load($product->getCustomerId())->getData();
            //get username firstletter
            $firstname[0] = str_split(ucfirst($customerData['firstname']));
            $lastname[0] = str_split(ucfirst($customerData['lastname']));
            foreach ($firstname as $fname) {
                $username .= $fname[0];
            }
            foreach ($lastname as $lname) {
                $username .= $lname[0];
            }
            //coustomerid with 4 digit
            $customerid = $product->getCustomerId();
            $fourcustomerdigit = str_pad($customerid, 4, '0', STR_PAD_LEFT);
            //category count
            $products = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('customer_id', $customerid)
                    ->addCategoryFilter($_category)
                    ->load();


            $myclosetcount = $products->count();
            $myclosetcount+=1;
            $fourdigit_cat_count = str_pad($myclosetcount, 4, '0', STR_PAD_LEFT);
            $newsku = $username . $fourcustomerdigit . '-' . $catname . $fourdigit_cat_count;
            $Color = $product->getAttributeText('color');
            $Condition = $product->getConditionClothingItems();
            $Description = $product->getDescription();
            $Name = $product->getName();
            $Size = $product->getAttributeText('size');
            $designer = $product->getAttributeText('designer');
            $shipped_to = $product->getShippedTo();
            $SecurityToken = Mage::getStoreConfig('barcloud/general/securitytoken');
            $BARCLOUD_API_URL = Mage::getStoreConfig('barcloud/general/barcloudApiUrl');
            $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');
            $IPAddress= $_SERVER['REMOTE_ADDR'];
            $product->setSku($newsku);
//            Mage::getSingleton('adminhtml/session')->addNotice('SKU of item is ' . $newsku);
            $data_json = '{
  "AssetNumber": "' . $newsku . '",
  "Authorized": true,
  "CheckedOut": false,
  "Color": "' . $Color . '",
  "Condition": "' . $Condition . '",
  "CreateDate": "\/Date(1427718290000+0300)\/",
  "CreatedBy": "APITest",
  "CurrentLocation": "158",
  "Description": "' . $Description . '",
  "IPAddress": "'.$IPAddress.'",
  "MACAddress": "hand held device",
  "Name": "' . $Name . '",
  "SecurityToken": "'.$SecurityToken.'",
  "Size": "' . $Size . '",
  "Manufacturer": "' . $designer . '",
  "DisposeTo": "' . $shipped_to . '"

}';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_json)));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_URL, $BARCLOUD_API_URL . "/{$barcloudApiKey}/AddAssetPost");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($output);
            print "<pre>";
            print_r($data);
            print "</pre>";
            //return $data->ZGetAssetsResult;
            return true;
        }   
             return false;
         }
    }

    public function catalogProductEditBefore($observer) {


        $product = $observer->getEvent()->getProduct();
           $productStatus = $product->getProductStatus();
       if($productStatus){
        $productid = $product->getId();
        if ($productid) {
            //category name
            $categoryIds = $product->getCategoryIds();
            if (count($categoryIds)) {
                $firstCategoryId = $categoryIds[0];
                $_category = Mage::getModel('catalog/category')->load($firstCategoryId);
                $_category->getName();
                $categoryname[0] = str_split($_category->getName());
                foreach ($categoryname as $catname1) {
                    $catname .=$catname1[0] . $catname1[1];
                }
            }
            $customerData = Mage::getModel('customer/customer')->load($product->getCustomerId())->getData();
            //get username firstletter
            $firstname[0] = str_split(ucfirst($customerData['firstname']));
            $lastname[0] = str_split(ucfirst($customerData['lastname']));
            foreach ($firstname as $fname) {
                $username .= $fname[0];
            }
            foreach ($lastname as $lname) {
                $username .= $lname[0];
            }
            //coustomerid with 4 digit
            $customerid = $product->getCustomerId();
            $fourcustomerdigit = str_pad($customerid, 4, '0', STR_PAD_LEFT);
            //category count
            $products = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('customer_id', $customerid)
                    ->addCategoryFilter($_category)
                    ->load();
            $myclosetcount = $products->count();
            $fourdigit_cat_count = str_pad($myclosetcount, 4, '0', STR_PAD_LEFT);
            $newsku = $username . $fourcustomerdigit . '-' . $catname . $fourdigit_cat_count;
            $product->setSku($newsku);
//             Mage::getSingleton('adminhtml/session')->addNotice('SKU of item is ' . $newsku);
            $Color = $product->getAttributeText('color');
            $Condition = $product->getConditionClothingItems();
            $Description = $product->getDescription();
            $Name = $product->getName();
            $Size = $product->getAttributeText('size');
            $designer = $product->getAttributeText('designer');
            $shipped_to = $product->getShippedTo();
            date_default_timezone_set("America/New_York");
            $modifieddate = date("Y-m-d h:i:sa");
            $notes = 'Modified at ' . $modifieddate;
            $SecurityToken = Mage::getStoreConfig('barcloud/general/securitytoken');
            $BARCLOUD_API_URL = Mage::getStoreConfig('barcloud/general/barcloudApiUrl');
            $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');
            $IPAddress= $_SERVER['REMOTE_ADDR'];
            $data_json = '{
  "AssetNumber": "' . $newsku . '",
  "Authorized": true,
  "CheckedOut": false,
  "Color": "' . $Color . '",
  "Condition": "' . $Condition . '",
  "CreateDate": "\/Date(1427718290000+0300)\/",
  "CreatedBy": "APITest",
  "CurrentLocation": "158",
  "Description": "' . $Description . '",
  "IPAddress":  "'.$IPAddress.'",
  "MACAddress": "hand held device",
  "Name": "' . $Name . '",
  "SecurityToken": "' . $SecurityToken . '",
  "Size": "' . $Size . '",
  "Manufacturer": "' . $designer . '",
  "DisposeTo": "' . $shipped_to . '",
  "Notes": "' . $notes . '"
}';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_json)));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_URL, $BARCLOUD_API_URL . "/{$barcloudApiKey}/UpdateAssetPOST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            print "test";
            $data = json_decode($output);
            print "<pre>";
            print_r($data);
            print "</pre>";
            //return $data->ZGetAssetsResult;
            return;
        }
           }return false;
    }

}
