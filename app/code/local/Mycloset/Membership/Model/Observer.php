<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mycloset_Membership_Model_Observer {

    public function createAsset($observer) {
        $product = $observer->getEvent()->getProduct();
        $categoryIds = $product->getCategoryIds();
        $firstCategoryId = ($categoryIds[0] ? $categoryIds[0] : $categoryIds[1]);
        $_category = Mage::getModel('catalog/category')->load($firstCategoryId);
        $path = $_category->getPath();
        $ids = explode('/', $path);
        if (isset($ids[2])) {
            $topParent = $ids[2];
        }
        if ($topParent == 16) {
            $customerid = $product->getCustomerId();
            if ($customerid > 0) {
                $productStatus = $product->getProductStatus();
                $Status = $product->getStatus(); //check for disabled or enabled product
                if ($Status == 1) {
                    $_product = Mage::getModel('catalog/product')->load($product->getId());
                    $oldcustomerid = $_product->getCustomerId();
                    $firstCategoryId = ($categoryIds[0] ? $categoryIds[0] : $categoryIds[1]);
                    $_category = Mage::getModel('catalog/category')->load($firstCategoryId);
                    $categoryname[0] = str_split($_category->getName());
                    foreach ($categoryname as $catname1) {
                        $catname .=$catname1[0] . $catname1[1];
                    }
                    $customerData = Mage::getModel('customer/customer')->load($product->getCustomerId())->getData();
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
                    $SecurityToken = $this->getSecurityToken();
                    $IPAddress = $_SERVER['REMOTE_ADDR'];
                    $product->setSku($newsku);
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
  "IPAddress": "' . $IPAddress . '",
  "MACAddress": "hand held device",
  "Name": "' . $Name . '",
  "SecurityToken": "' . $SecurityToken . '",
  "Size": "' . $Size . '",
  "Manufacturer": "' . $designer . '",
  "DisposeTo": "' . $shipped_to . '",
  "AssetModelNumber": "myclosetconcierge"
}';
                    $this->addAsset($data_json, $newsku);
                }
            }
        }
    }

    public function catalogProductEditBefore($observer) {

        $product = $observer->getEvent()->getProduct();
// new parent category
        $categoryIds = $product->getCategoryIds();
        $firstCategoryId = $categoryIds[1];
        $_category1 = Mage::getModel('catalog/category')->load($firstCategoryId);
        $path1 = $_category1->getPath();
        $ids1 = explode('/', $path1);
        if (isset($ids1[2])) {
            $newParent = $ids1[2];
        }
// old parent category
        $_product = Mage::getModel('catalog/product')->load($product->getId());
        $cat_ids = $_product->getCategoryIds();
        $oldCategoryId = $cat_ids[0];
        $_category = Mage::getModel('catalog/category')->load($oldCategoryId);
        $path = $_category->getPath();
        $ids = explode('/', $path);
        if (isset($ids[2])) {
            $oldParent = $ids[2];
        }
        if (($newParent == 16) && ($oldParent == '')) {
            $this->createAsset($observer);
        } else if (($oldParent == 9) && ($newParent == 16)) {
            $this->createAsset($observer);
        } else if (( $newParent == 9 ) && ($oldParent == 16)) {
            $this->deleteAsset($_product);
        } else if ((($oldParent == 16) && ($newParent == 16)) || ($oldParent == 16)) {
            $customerid = $product->getCustomerId();
            $_product = Mage::getModel('catalog/product')->load($product->getId());
            $productStatus = $product->getProductStatus(); //check for "SHOP"
            $Status = $product->getStatus(); //check for disabled or enabled product
            $productid = $product->getId();
            $old_customerid = $_product->getCustomerId();
            $new_customerid = $product->getCustomerId();
            if ($productid) {
                if ($Status == 1) {
                    if (($old_customerid > 0 ) && ($new_customerid == 0)) {//move to sample closet
                        $this->deleteAsset($_product); //Asset delete, No sku
                    } else if (($old_customerid == 0 ) && ($new_customerid > 0)) {//sample closet to mycloset
                        $this->createAsset($observer); //Create asset, New sku
                    } else if (($new_customerid == $old_customerid) && $new_customerid) {// normal edit
                        $categoryIds = $product->getCategoryIds();
                        $CategoryId = $categoryIds[0];
                        $_category = Mage::getModel('catalog/category')->load($CategoryId);
                        $newcat_id = $_category->getId();
//old product details
                        $_product = Mage::getModel('catalog/product')->load($product->getId());
                        $cats = $_product->getCategoryIds();
                        foreach ($cats as $category_id) {
                            $_cat = Mage::getModel('catalog/category')->load($category_id);
                            $oldcat_id = $_cat->getId();
                            break;
                        }
                        if ($oldcat_id != $newcat_id) { //CHECK WITH CAT_ID
                            $this->deleteAsset($_product);
                            $this->createAsset($observer);
                        } else {
                            $oldsku = $_product->getSku();
                            if ($oldsku == '') {
                                $this->createAsset($observer);
                            } else {
                                $product->setSku($oldsku);
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
                                $SecurityToken = $this->getSecurityToken();
                                $IPAddress = $_SERVER['REMOTE_ADDR'];
                                $data_json = '{
  "AssetNumber": "' . $oldsku . '",
  "Authorized": true,
  "CheckedOut": false,
  "Color": "' . $Color . '",
  "Condition": "' . $Condition . '",
  "CreateDate": "\/Date(1427718290000+0300)\/",
  "CreatedBy": "APITest",
  "CurrentLocation": "158",
  "Description": "' . $Description . '",
  "IPAddress":  "' . $IPAddress . '",
  "MACAddress": "hand held device",
  "Name": "' . $Name . '",
  "SecurityToken": "' . $SecurityToken . '",
  "Size": "' . $Size . '",
  "Manufacturer": "' . $designer . '",
  "DisposeTo": "' . $shipped_to . '",
  "Notes": "' . $notes . '",
  "AssetModelNumber": "myclosetconcierge"  
}';
                                $this->updateAsset($data_json, $oldsku);
                                return true;
                            }
                        }
                    } else {
                        $this->deleteAsset($_product);
                        $this->createAsset($observer);
                    }
                }
            } else {
                $this->createAsset($observer);
            }
        }
    }

    public function deleteProduct($observer) {
        $product = $observer->getEvent()->getProduct();
        $this->deleteAsset($product);
    }

    public function deleteAsset($product) {
        $oldsku = $product->getSku('sku');
        $Color = $product->getAttributeText('color');
        $Condition = $product->getConditionClothingItems();
        $Description = $product->getDescription();
        $Name = $product->getName();
        $Size = $product->getAttributeText('size');
        $designer = $product->getAttributeText('designer');
        $shipped_to = $product->getShippedTo();
        $SecurityToken = $this->getSecurityToken();
        $IPAddress = $_SERVER['REMOTE_ADDR'];
        $data_json = '{
  "AssetNumber": "' . $oldsku . '",
  "Authorized": true,
  "CheckedOut": false,
  "Color": "' . $Color . '",
  "Condition": "' . $Condition . '",
  "CreateDate": "\/Date(1427718290000+0300)\/",
  "CreatedBy": "APITest",
  "CurrentLocation": "Deleted",
  "Description": "' . $Description . '",
  "IPAddress": "' . $IPAddress . '",
  "MACAddress": "hand held device",
  "Name": "' . $Name . '",
  "SecurityToken": "' . $SecurityToken . '",
  "Size": "' . $Size . '",
  "Manufacturer": "' . $designer . '",
  "DisposeTo": "' . $shipped_to . '",
  "AssetModelNumber": "myclosetconcierge"
}';
        $this->updateAsset($data_json, $oldsku);
        return true;
    }

    public function addAsset($data_json, $newsku) {
        $exsisiting = $this->alreadyExsistingData($data_json, $newsku);
        if ($exsisiting) {
           
            $this->updateAsset($data_json, $newsku);
        } else {
      
            $BARCLOUD_API_URL = Mage::getStoreConfig('barcloud/general/barcloudApiUrl');
            $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_json)));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_URL, $BARCLOUD_API_URL . "/{$barcloudApiKey}/AddAssetPost");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            $data = json_decode($output);             
            curl_close($ch);               
            return true;
        }
    }

    public function updateAsset($data_json, $newsku) {

        $exsisiting = $this->alreadyExsistingData($data_json, $newsku);
        $BARCLOUD_API_URL = Mage::getStoreConfig('barcloud/general/barcloudApiUrl');
        $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');
        if ($exsisiting) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_json)));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_URL, $BARCLOUD_API_URL . "/{$barcloudApiKey}/UpdateAssetPOST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            return true;
        } else {
            $this->addAsset($data_json, $newsku);
            return true;
        }
    }

    public function alreadyExsistingData($data_json, $newsku) {

        $old_data = json_decode($data_json);
        $BARCLOUD_API_URL = Mage::getStoreConfig('barcloud/general/barcloudApiUrl');
        $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');
        $SecurityToken = $this->getSecurityToken();
        $IPAddress = $_SERVER['REMOTE_ADDR'];
        $json_existdata = '{
  "SecurityToken": "' . $SecurityToken . '",
  "ObjectIdentifier": "' . $newsku . '"
}';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json_existdata)));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_existdata);
        curl_setopt($ch, CURLOPT_URL, $BARCLOUD_API_URL . "/{$barcloudApiKey}/GetAssetPOST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $data_result = json_decode($output);
        $assetsnum = $data_result->AssetNumber;
        if ($assetsnum) {
            return true;
        } else {
            return false;
        }
    }

    private function getSecurityToken() {
        $applicationkey = Mage::getStoreConfig('barcloud/general/applicationKey');
        $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');
        $username = Mage::getStoreConfig('barcloud/general/username');
        $password = Mage::getStoreConfig('barcloud/general/password');
         $BARCLOUD_API_URL = "http://zapier.asapsystems.com/Service.svc/";        
        $data_json = '{
  "ApplicationKey": "' . $applicationkey . '",
  "CustomerKey": "' . $barcloudApiKey . '",
  "UserName": "' . $username . '",
  "Password": "' . $password . '"
}';   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_json)));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_URL, $BARCLOUD_API_URL . "/{$barcloudApiKey}/GetSecurityTokenPOST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output);
       
        return $data;
    }

}
