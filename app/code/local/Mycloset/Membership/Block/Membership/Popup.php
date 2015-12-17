<?php

class Mycloset_Membership_Block_Membership_Popup extends Mage_Catalog_Block_Product_Abstract {

    public function getProductdetails() {

        $product_id = Mage::app()->getRequest()->getParam('product_id');
        $product['rel_id'] = $product_id;
//         $productowner = Mage::getModel('catalog/product')->load($product_id)->getCustomerName();
        // product owner session id  
//            Mage::getSingleton('core/session')->setProductOwner($productowner); // set session for product owner
//       $product['customerid']= Mage::getSingleton('core/session')->getProductOwner();
//      $customerid = Mage::getSingleton('core/session')->getProductOwner();
        $model = Mage::getModel('catalog/product')->load($product_id); //getting product model

        $product['id'] = $model->load($product_id); //getting product object for particular product id               
        $product['ShortDescription'] = $model->getShortDescription(); //product's short description
        $product['Description'] = $model->getDescription(); // product's long description
        $product['Name'] = $model->getName(); //product name
        $product['Price'] = $model->load($product_id)->getPrice(); //product's regular Price
        $product['SpecialPrice'] = $model->getSpecialPrice(); //product's special Price
        $product['ProductUrl'] = $model->getProductUrl(); //product url
        $product['ImageUrl'] = $model->getImageUrl(); //product's image url
        $product['SmallImageUrl'] = $model->getSmallImageUrl(); //product's small image url
        $product['ThumbnailUrl'] = $model->getThumbnailUrl(); //product's thumbnail image url
        $product['color'] = $model->getAttributeText('color'); //product's thumbnail image url
        $product['sku'] = $model->getSku();
         
                                 
                                   if($product['sku']==''){
                                       echo 'Not Applicable';
                                   }else{
                                      echo $product['sku']; 
                                   }     
        $product['size'] = $model->getAttributeText('size'); //product's thumbnail image url      
        $product['designer'] = $model->getAttributeText('designer'); //product's thumbnail image url
        $product['product_status'] = $model->getAttributeText('product_status'); //product's thumbnail image url
        $product['season'] = $model->getAttributeText('season'); //product's thumbnail image url
        $product['shipped_to'] = $model['shipped_to']; //product's thumbnail image url      
//       echo $cats = $model->load($product_id)->getCategoryIds();
//        foreach ($cats as $categoryId) {
//            $category = Mage::getModel('catalog/category')->load($categoryId);
//            $product['category']= $category->getName();
//        }
//        print_r($product);

        return $product;
    }

}
