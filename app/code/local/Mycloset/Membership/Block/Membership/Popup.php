<?php

class Mycloset_Membership_Block_Membership_Popup extends Mage_Catalog_Block_Product_Abstract {

    public function getProductdetails() {

        $product_id = Mage::app()->getRequest()->getParam('product_id');
        $product['rel_id'] =$product_id;
//         $productowner = Mage::getModel('catalog/product')->load($product_id)->getCustomerName();
            // product owner session id  
//            Mage::getSingleton('core/session')->setProductOwner($productowner); // set session for product owner
//       $product['customerid']= Mage::getSingleton('core/session')->getProductOwner();
//      $customerid = Mage::getSingleton('core/session')->getProductOwner();
        $model = Mage::getModel('catalog/product'); //getting product model
      $product['id'] = $model->load($product_id); //getting product object for particular product id               
        $product['ShortDescription'] = $model->load($product_id)->getShortDescription(); //product's short description
        $product['Description'] = $model->load($product_id)->getDescription(); // product's long description
        $product['Name'] = $model->load($product_id)->getName(); //product name
        $product['Price'] = $model->load($product_id)->getPrice(); //product's regular Price
        $product['SpecialPrice'] = $model->load($product_id)->getSpecialPrice(); //product's special Price
        $product['ProductUrl'] = $model->load($product_id)->getProductUrl(); //product url
        $product['ImageUrl'] = $model->load($product_id)->getImageUrl(); //product's image url
        $product['SmallImageUrl'] = $model->load($product_id)->getSmallImageUrl(); //product's small image url
        $product['ThumbnailUrl'] = $model->load($product_id)->getThumbnailUrl(); //product's thumbnail image url
        $product['color'] = $model->load($product_id)->getAttributeText('color'); //product's thumbnail image url
        $product['sku'] = $model->load($product_id)->getSku(); //product's thumbnail image url      
        $product['size'] = $model->load($product_id)->getAttributeText('size'); //product's thumbnail image url      
        $product['designer'] = $model->load($product_id)->getAttributeText('designer'); //product's thumbnail image url
        $product['product_status'] = $model->load($product_id)->getAttributeText('product_status'); //product's thumbnail image url
        $product['season'] = $model->load($product_id)->getAttributeText('season'); //product's thumbnail image url
        $product['shipped_to'] = $model->load($product_id)->getAttributeText('shipped_to'); //product's thumbnail image url      
//       echo $cats = $model->load($product_id)->getCategoryIds();
//        foreach ($cats as $categoryId) {
//            $category = Mage::getModel('catalog/category')->load($categoryId);
//            $product['category']= $category->getName();
//        }
//        print_r($product);
        return $product;
    }

}
