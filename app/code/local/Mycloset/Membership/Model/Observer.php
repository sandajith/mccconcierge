<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mycloset_Membership_Model_Observer {

    public function createAsset($observer) {
        $product = $observer->getEvent()->getProduct();
        $customerid = $product->getCustomerId();
        if ($customerid > 0) {
//            $productid = $product->getId();
            $productStatus = $product->getProductStatus();
            if ($productStatus) {
                $_product = Mage::getModel('catalog/product')->load($product->getId());
                $oldcustomerid = $_product->getCustomerId();
                if ($oldcustomerid != '') {
                    $categoryIds = $product->getCategoryIds();
                   
                    if (count($categoryIds)) {
                        $firstCategoryId = $categoryIds[0];
                        $_category = Mage::getModel('catalog/category')->load($firstCategoryId);
                        $categoryname[0] = str_split($_category->getName());
                        foreach ($categoryname as $catname1) {
                        echo    $catname .=$catname1[0] . $catname1[1];
                        }
                    }
                 
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
                }
                //get username firstletter      
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
                $SecurityToken = Mage::getStoreConfig('barcloud/general/securitytoken');
                $IPAddress = $_SERVER['REMOTE_ADDR'];
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
  "IPAddress": "' . $IPAddress . '",
  "MACAddress": "hand held device",
  "Name": "' . $Name . '",
  "SecurityToken": "' . $SecurityToken . '",
  "Size": "' . $Size . '",
  "Manufacturer": "' . $designer . '",
  "DisposeTo": "' . $shipped_to . '"
}';
                $this->addAsset($data_json, $newsku);
            }
        }
    }

    public function catalogProductEditBefore($observer) {

        $product = $observer->getEvent()->getProduct();
        $customerid = $product->getCustomerId();
        $_product = Mage::getModel('catalog/product')->load($product->getId());
        $productStatus = $product->getProductStatus(); //check for "SHOP"
        $productid = $product->getId();
        $old_customerid = $_product->getCustomerId();
        $new_customerid = $product->getCustomerId();
        // Condition1
        // $new_customerid and $new_customerid==$old_customerid => simple edit with NO sku update
        //$cat == old catid  => simple edit with NO sku update
        //$cat != old catid  => Delete existing asset and Create new asset with newsku
        // Condition2
        // $new_customerid and $old_customerid==0 (samplecloset to mycloset) => create sku and create new asset
        //Condition3 Customer to Sample closet
        //$new_customerid = 0 and $old_customerid =>Delete asset -YES
//        if ($new_customerid > 0) {
        if ($productid) {
            if ($productStatus) {
                if (($old_customerid > 0 ) && ($new_customerid == 0)) {
                    $this->deleteAsset($_product); //Asset delete, No sku
                } else if (($old_customerid == 0 ) && ($new_customerid > 0)) {
                    $this->createAsset($observer); //Create asset, New sku
                } else if (($new_customerid == $old_customerid) && $new_customerid) {
//                if ($productid) {
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
                        $SecurityToken = Mage::getStoreConfig('barcloud/general/securitytoken');
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
  "Notes": "' . $notes . '"
}';
                        $this->updateAsset($data_json);
                        return true;
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

    public function deleteProduct($observer) {
        $product = $observer->getEvent()->getProduct();
        $this->deleteAsset($product);
    }

//    public function catalogSampleProductSaveBefore($observer) {
    public function deleteAsset($product) {

        $oldsku = $product->getSku('sku');
        $Color = $product->getAttributeText('color');
        $Condition = $product->getConditionClothingItems();
        $Description = $product->getDescription();
        $Name = $product->getName();
        $Size = $product->getAttributeText('size');
        $designer = $product->getAttributeText('designer');
        $shipped_to = $product->getShippedTo();
        $SecurityToken = Mage::getStoreConfig('barcloud/general/securitytoken');
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
  "DisposeTo": "' . $shipped_to . '"
}';

        $this->updateAsset($data_json);
        return true;
    }

    public function addAsset($data_json, $newsku) {
        $this->alreadyExsistingData($data_json,$newsku);
        $BARCLOUD_API_URL = Mage::getStoreConfig('barcloud/general/barcloudApiUrl');
        $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');
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

    public function updateAsset($data_json) {
        $BARCLOUD_API_URL = Mage::getStoreConfig('barcloud/general/barcloudApiUrl');
        $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');

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
        return true;
//return $data->ZGetAssetsResult;
    }

    public function alreadyExsistingData($data_json,$newsku) {
      $old_data = json_decode($data_json);
        $BARCLOUD_API_URL = Mage::getStoreConfig('barcloud/general/barcloudApiUrl');
        $barcloudApiKey = Mage::getStoreConfig('barcloud/general/barcloudApiKey');
        $SecurityToken = Mage::getStoreConfig('barcloud/general/securitytoken');
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
            $changed_data = '{
"AssetNumber": "' . $newsku . '",
  "Authorized": true,
  "CheckedOut": false,
  "Color": "' . $old_data->Color . '",
  "Condition": "' . $old_data->Condition . '",
  "CreateDate": "\/Date(1427718290000+0300)\/",
  "CreatedBy": "APITest",
  "CurrentLocation": "158",
  "Description": "' . $old_data->Description. '",
  "IPAddress":  "' . $IPAddress . '",
  "MACAddress": "hand held device",
  "Name": "' . $old_data->Name . '",
  "SecurityToken": "' . $SecurityToken . '",
  "Size": "' . $old_data->Size . '",
  "Manufacturer": "' . $old_data->Manufacturer . '",
  "DisposeTo": "' . $old_data->DisposeTo . '"
}';
                  
            $this->updateAsset($changed_data);
            return true;
        }
    }

}
