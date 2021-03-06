<?php

class Mycloset_Membership_Block_Membership_Popup extends Mage_Catalog_Block_Product_Abstract {

    public function getProductdetails() {

        $product_id = Mage::app()->getRequest()->getParam('product_id');
        $product['rel_id'] = $product_id;
        $model = Mage::getModel('catalog/product')->load($product_id); //getting product model
//        var_dump($model);
//        exit;
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
        $product['size'] = $model->getAttributeText('size'); //product's thumbnail image url      
        if ($product['size'] == '') {
            $product['size'] = ' Not Applicable';
        } else {
            $product['size'];
        }
        $product['designer'] = $model->getAttributeText('designer'); //product's thumbnail image url
        $product['product_status'] = $model->getAttributeText('product_status'); //product's thumbnail image url
        $product['season_select'] = $model->getAttributeText('season'); //product's season
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'season');
        if ($attribute->usesSource()) {
            $product['season'] = $attribute->getSource()->getAllOptions(false);
        }
        $tag_details = '';
        $tag_details['tag_select'] = $model->getAttributeText('tag_custom'); //product's tags
//        $tag_details['tag_select_id'] = $model->getAttributeText('tag_custom'); //product's tags
        $product['tag_select'] = $tag_details;
        $attribute_tag = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'tag_custom');
        if ($attribute_tag->usesSource()) {
            $product['tag_custom'] = $attribute_tag->getSource()->getAllOptions(false);
        }
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
