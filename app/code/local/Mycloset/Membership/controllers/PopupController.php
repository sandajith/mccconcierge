<?php

class Mycloset_Membership_PopupController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sliderAction() {
        $categoryIds = Mage::app()->getRequest()->getParam('categoryId');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        //if($userid){
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('category_id', array('in' => $categoryIds));
        $i = 0;
        if ($collection->count() > 0) {
            ?>
            <div class="flexsliderPopup carousel">
                <ul class="slides">
                    <?php
                    foreach ($collection as $_product):

                        $productowner = Mage::getModel('catalog/product')->load($_product->getId())->getCustomerId();
                        Mage::getSingleton('core/session')->setProductOwner($productowner);
                        $customerid = Mage::getSingleton('core/session')->getProductOwner();

                        if ($_product->getCustomerId() === $userid) {
                            ?>
                            <li>
                                <a userid="<?php echo $userid; ?>" attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $_product->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($_product, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li>  
                        <?php } else {
                            ?>
                            <li>
                                <a attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $_product->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($_product, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li> 
                            <?php
                        }
                    endforeach;
                    ?>
                </ul>
            </div>
            <?php
        }
        ?>
        <script>
            var jqCustom = jQuery.noConflict();
            jqCustom('.getproductid').click(function () {
                var productid = jqCustom(this).attr('rel');
                jqCustom.ajax({
                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/product?productid=" + productid,
                    type: 'get',
                    dataType: "json",
                    success: function (data) {

                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');

                    }
                });
            });
        </script> 
        <?php
    }

    public function productdesignerAction() {
        $designer = Mage::app()->getRequest()->getParam('ProductIds');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('designer', $designer);
        if ($collection->count() > 0) {
            ?>
            <div class="flexsliderPopup carousel">
                <ul class="slides">
                    <?php
                    foreach ($collection as $dddd) {
                        $productowner1 = Mage::getModel('catalog/product')->load($dddd->getId())->getCustomerId();
                        Mage::getSingleton('core/session')->setProductOwner($productowner);
                        $customerid = Mage::getSingleton('core/session')->getProductOwner();

                        if ($dddd->getCustomerId() === $userid) {
                            ?>
                            <li>
                                <a userid="<?php echo $userid; ?>" attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $dddd->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($dddd, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li>  
                        <?php } else {
                            ?>
                            <li>
                                <a attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $dddd->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($dddd, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li> 
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
        ?>
        <script>
            var jqCustom = jQuery.noConflict();
            jqCustom('.getproductid').click(function () {
                var productid = jqCustom(this).attr('rel');
                jqCustom.ajax({
                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/product?productid=" + productid,
                    type: 'get',
                    dataType: "json",
                    success: function (data) {

                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');

                    }
                });
            });
        </script> 
        <?php
    }

    public function productcolorAction() {
        $designer = Mage::app()->getRequest()->getParam('ProductIds');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('color', $designer);
        if ($collection->count() > 0) {
            ?>
            <div class="flexsliderPopup carousel">
                <ul class="slides">
                    <?php
                    foreach ($collection as $dddd) {
                        $productowner1 = Mage::getModel('catalog/product')->load($dddd->getId())->getCustomerId();
                        Mage::getSingleton('core/session')->setProductOwner($productowner);
                        $customerid = Mage::getSingleton('core/session')->getProductOwner();

                        if ($dddd->getCustomerId() === $userid) {
                            ?>
                            <li>
                                <a userid="<?php echo $userid; ?>" attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $dddd->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($dddd, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li>  
                        <?php } else {
                            ?>
                            <li>
                                <a attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $dddd->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($dddd, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li> 
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
        ?>
        <script>
            var jqCustom = jQuery.noConflict();
            jqCustom('.getproductid').click(function () {
                var productid = jqCustom(this).attr('rel');
                jqCustom.ajax({
                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/product?productid=" + productid,
                    type: 'get',
                    dataType: "json",
                    success: function (data) {

                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');

                    }
                });
            });
        </script> 
        <?php
    }

    public function productstatusAction() {
        $designer = Mage::app()->getRequest()->getParam('ProductIds');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('product_status', $designer);
        if ($collection->count() > 0) {
            ?>
            <div class="flexsliderPopup carousel">
                <ul class="slides">
                    <?php
                    foreach ($collection as $dddd) {
                        $productowner1 = Mage::getModel('catalog/product')->load($dddd->getId())->getCustomerId();
                        Mage::getSingleton('core/session')->setProductOwner($productowner);
                        $customerid = Mage::getSingleton('core/session')->getProductOwner();

                        if ($dddd->getCustomerId() === $userid) {
                            ?>
                            <li>
                                <a userid="<?php echo $userid; ?>" attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $dddd->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($dddd, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li>  
                        <?php } else {
                            ?>
                            <li>
                                <a attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $dddd->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($dddd, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li> 
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
        ?>
        <script>
            var jqCustom = jQuery.noConflict();
            jqCustom('.getproductid').click(function () {
                var productid = jqCustom(this).attr('rel');
                jqCustom.ajax({
                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/product?productid=" + productid,
                    type: 'get',
                    dataType: "json",
                    success: function (data) {

                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');

                    }
                });
            });
        </script> 
        <?php
    }

    public function seasonAction() {
        $designer = Mage::app()->getRequest()->getParam('ProductIds');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('season', $designer);
        if ($collection->count() > 0) {
            ?>
            <div class="flexsliderPopup carousel">
                <ul class="slides">
                    <?php
                    foreach ($collection as $dddd) {
                        $productowner1 = Mage::getModel('catalog/product')->load($dddd->getId())->getCustomerId();
                        Mage::getSingleton('core/session')->setProductOwner($productowner);
                        $customerid = Mage::getSingleton('core/session')->getProductOwner();

                        if ($dddd->getCustomerId() === $userid) {
                            ?>
                            <li>
                                <a userid="<?php echo $userid; ?>" attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $dddd->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($dddd, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li>  
                        <?php } else {
                            ?>
                            <li>
                                <a attr="<?php echo $customerid; ?>"  class="getproductid" rel="<?php echo $dddd->getId(); ?>" >
                                    <img src="<?php echo Mage::helper('catalog/image')->init($dddd, 'small_image')->resize(135); ?>" alt=""/></a>
                            </li> 
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
        ?>
        <script>
            var jqCustom = jQuery.noConflict();
            jqCustom('.getproductid').click(function () {
                var productid = jqCustom(this).attr('rel');
                jqCustom.ajax({
                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/product?productid=" + productid,
                    type: 'get',
                    dataType: "json",
                    success: function (data) {

                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');

                    }
                });
            });
        </script> 
        <?php
    }

    public function productAction() {
        $product_id = Mage::app()->getRequest()->getParam('productid');
        $product = array();
        $model = Mage::getModel('catalog/product'); //getting product model
        $product['id'] = $model->load($product_id); //getting product object for particular product id               
        $product['ShortDescription'] = $model->load($product_id)->getShortDescription(); //product's short description
        $product['Description'] = $model->load($product_id)->getDescription(); // product's long description
        $product['Name'] = $model->load($product_id)->getName(); //product name
        $product['Price'] = $model->load($product_id)->getPrice(); //product's regular Price
        $product['SpecialPrice'] = $model->load($product_id)->getSpecialPrice(); //product's special Price
        $product['ProductUrl'] = $model->load($product_id)->getProductUrl(); //product url
        $product['ImageUrl'] = $model->load($product_id)->getImageUrl(); //product's image url
        $product['color'] = $model->load($product_id)->getAttributeText('color'); //product's thumbnail image url
        $product['sku'] = $model->load($product_id)->getSku(); //product's thumbnail image url      
        $product['size'] = $model->load($product_id)->getAttributeText('size'); //product's thumbnail image url      
        $product['designer'] = $model->load($product_id)->getAttributeText('designer'); //product's thumbnail image url
        $product['product_status'] = $model->load($product_id)->getAttributeText('product_status'); //product's thumbnail image url
        $product['season'] = $model->load($product_id)->getAttributeText('season'); //product's thumbnail image url
        $product['shipped_to'] = $model->load($product_id)->getAttributeText('shipped_to'); //product's thumbnail image url      
        echo json_encode($product);
    }

    public function inserttagAction() {
        $tagvalue = Mage::app()->getRequest()->getParam('tag');
        $productid = Mage::app()->getRequest()->getParam('productid');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('tag/tag')
//        $collection = Mage::getModel('tag/tag_relation')     
                ->setName($tagvalue)
                ->setStatus(1)
                ->setFirstCustomerId($userid)
                ->setFirstStoreId(1);


        $insertId = $collection->save()->getId();
//                


        $tag_id = $insertId;
        $collection2 = Mage::getModel('tag/tag_relation')
                ->setTagId($tag_id)
                ->setCustomerId($userid)
                ->setProductId($productid)
                ->setStoreId(1)
                ->setActive(1)
                ->save();
        return true;
    }

    public function deletetagAction() {
        $user_id = Mage::getSingleton('customer/session')->getId();
         $userid = ($user_id ? $user_id : 0);
        $tagvalue = Mage::app()->getRequest()->getParam('tag');
//         $tag = Mage::getModel('tag/tag')->load($tagvalue);
//            $tag->delete();
             $model = Mage::getModel('tag/tag_relation');
             $model->loadByTagCustomer(null, $tagvalue, $userid)->delete();;
//        $prefix = Mage::getConfig()->getTablePrefix();
//        $resource = Mage::getSingleton('core/resource');
//        $write = $resource->getConnection('core_write');
//        $write->query("delete from `'.$prefix.'tag_relation` where tag_id = " . $tagvalue);
//        $write->query("delete from `'.$prefix.'tag` where tag_id = " . $tagvalue);
        return true;
    }

}
?>

