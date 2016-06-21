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
        $cat = Mage::getModel('catalog/category')->load(16);
        $subcats = $cat->getChildren();
        $all_sub_cat = explode(',', $subcats);
        if ($categoryIds == 16) {
            ?>
            <div class="flexsliderPopup flexslider carousel">
                <ul class="slides">
                    <?php
                    foreach ($all_sub_cat as $cat_id) {
                        $category = Mage::getModel('catalog/category')->load($cat_id);
                        $categories = Mage::getModel('catalog/category')->load($category->getId())
                                ->getProductCollection()
                                ->addAttributeToSelect('entity_id')
                                ->addAttributeToFilter('status', 1)
                                ->addAttributeToFilter('visibility', 4);
                        $products = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addAttributeToFilter('customer_id', $userid)
                                ->addCategoryFilter($category)
                                ->load();
                        if ($products->count() > 0) {
                            ?>              
                            <li >                       
                                <a class="getcatid" rel="<?php echo $category->getId(); ?>" ><img  src="<?php echo Mage::getBaseUrl('media') . 'catalog/category/' . $category->image; ?>" alt=""/><p><?php echo ucfirst(strtolower($category->name)); ?></p></a> 
                            </li>    
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>  
        <?php
        }

        //if($userid){
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', 4)
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
           
                
//            jqCustom(".getcatid").on("click", function (event) {
//                var catid = jqCustom(this).attr('rel');
//                jqCustom.ajax({
//                    url: "<?php //echo Mage::getBaseUrl(); ?>" + "mycloset/popup/slider",
//                    type: 'post',
//                    data: {
//                        categoryId: catid
//                    },
//                    success: function (data) {
//                        jqCustom('.flexsliderPopup').remove();
//                        jqCustom(data).appendTo(".popupContent");
//
//                    }
//                });
//                jqCustom(this).off(event);
//     });
            jqCustom('.getproductid').click(function () {
                
                var productid = jqCustom(this).attr('rel');
                jqCustom.ajax({
                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/product?productid=" + productid,
                    type: 'get',
                    dataType: "json",
                    success: function (data) {
        
    
    jqCustom('.catname').html(' <h2>' + data['catname'] + '</h2>');
                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                         jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                         jqCustom('#btnDeliver').attr('onclick',data['add_to_cart']);
                         
var productstatus ='';
                        if (data['product_status'] === 'Shipped To') {
                            if (data['shipped_to'] === null) {
                                data['shipped_to'] = '';
                              productstatus = data['product_status'] + ' ' + data['shipped_to'];
                            } else {
                                productstatus = data['product_status'] + ' ' + data['shipped_to'];
                            }
                        } else {
                             productstatus = data['product_status'];
                        }
                        jqCustom('.status').html('<b>Status:</b> ' + productstatus );
                                if (data['size'] === false) {
                            jqCustom('.size').html('<b>Size: </b><i>Size Not Applicable</i>');
                        } else {
                            jqCustom('.size').html('<b>Size: </b><i>' + data['size'] + '</i>');
                        }
                        var seasons = '';
                        jqCustom('#season').html(seasons);
                        for (var i = 0; i < data['season'].length; i++) {
                            if (inArray(data['season'][i]['label'], data['season_select'])) {
                                var selected = 'selected="selected"';
                            } else {
                                var selected = '';
                            }
                            seasons = seasons + '<option ' + selected + ' value="' + data['season'][i]['value'] + '">' + data['season'][i]['label'] + '</option>';
                            jqCustom('.preselect').html('<select  attrproduct="' + data['id'] + '"  style="border: 0px solid #e4e4e4;width: 110px;" id="season" class="season123"  multiple="multiple" size="4" name="product[season][]">' + seasons + '');
                        }
                        jqCustom("select").blur(function () {
                            var seasonnames = new Array();
                            var i = 0;
                            var productid = jqCustom(this).attr('attrproduct');
                            jqCustom("select option:selected").each(function () {
                                seasonnames[i] = jqCustom(this).val();

                                i++;

                            });
                            jqCustom.ajax({
                                url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/seasoninsert",
                                type: 'post',
                                data: {
                                    seasonnames: seasonnames,
                                    productid: productid
                                },
                                success: function () {
                                }
                            });
                        });
                        var tag3 = '';
                        for (var i = 0; i < data['tag_select'].length; i++) {
                            tag3 = tag3 + '<span class="tagitem" id="tagitem"><a>' + data['tag_select'][i] + '</a><i class="tagclose" pro="' + data['id'] + '" tagval="' + data['tag_select'][i] + '">x</i> </span>';
                        }
                        jqCustom('.tagappend').html(tag3 + '<span id="remove" class="add add-text"><input type="text" class="inputz" pro="' + data['id'] + '" name="tagss" value=""></span><span id="remove pluse" class="tagpluse"><i class="tagadd">+</i></span>');
                        jqCustom('.inputz').blur(function (e) {
                            var tag = jqCustom('.inputz').val();
                            if (tag != null) {
                                var productid = jqCustom(this).attr('pro');
                                jqCustom.ajax({
                                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/inserttag",
                                    type: 'post',
                                    data: {
                                        tag: tag,
                                        productid: productid
                                    },
                                    success: function (data1) {
                                        //                                        alert(data1);
                                        //                                        var data = parseInt(data1);
                                        var tagname = jqCustom('.inputz').val();
                                        if (tagname != null) {
                                            jqCustom('.tagappend').prepend("<span>" + jqCustom('.inputz').val() + "<i class='tagclose'  pro='" + data1['product_id'] + "'  tagval='" + data1['tag_id'] + "' >x</i></span>");
                                            jqCustom("input.inputz").val('');
                                            jqCustom(".tagclose").on("click", function () {
                                                var tag1233 = jqCustom(this).attr("tagval");
                                                var productid = jqCustom(this).attr('pro');
                                                jqCustom.ajax({
                                                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/deletetag",
                                                    type: 'post',
                                                    data: {
                                                        tag: tag1233,
                                                        productid: productid
                                                    },
                                                    success: function () {
                                                    }
                                                });
                                                jqCustom(this).parent('span').remove();
                                            });
                                        }
                                    }
                                });
                            }
                        });
                        jqCustom(".tagclose").on("click", function () {
                            var tag123 = jqCustom(this).attr("tagval");
                            jqCustom.ajax({
                                url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/deletetag",
                                type: 'post',
                                data: {
                                    tag: tag123
                                },
                                success: function () {
                                }
                            });
                            jqCustom(this).parent('span').remove();
                        });
                        jqCustom(".tagadd").on("click", function () {
                            jqCustom(".add-text").show();
                            jqCustom(".tagpluse").hide();
                        });
                        jqCustom(".close,.popClose").click(function () {
                            jqCustom(".popupWrapper").fadeOut();
                            jqCustom(".popupBox").empty();
                        });
                    }
                });
            });

        </script> 
        <?php
    }





    public function productAction() {
        $product_id = Mage::app()->getRequest()->getParam('productid');
        $product = array();
        $model = Mage::getModel('catalog/product')->load($product_id); //getting product model
        $product['id'] = $product_id; //getting product object for particular product id               
//        $product['ShortDescription'] = $model->getShortDescription(); //product's short description
//        $product['Description'] = $model->getDescription(); // product's long description

        $catname = '';
        $cats = $model->getCategoryIds();
        foreach ($cats as $category_id) {
            $_cat = Mage::getModel('catalog/category')->load($category_id);
            $catname = $_cat->getName();
        }
        $product['catname'] = $catname;
        $product['Name'] = $model->getName(); //product name
        $product['Price'] = $model->getPrice(); //product's regular Price
        $product['SpecialPrice'] = $model->getSpecialPrice(); //product's special Price
        $product['ProductUrl'] = $model->getProductUrl(); //product url
        $product['ImageUrl'] = $model->getImageUrl(); //product's image url
        $product['color'] = $model->getAttributeText('color'); //product's thumbnail image url
        $product['sku'] = $model->getSku(); //product's thumbnail image url      
        $product['size'] = $model->getAttributeText('size'); //product's thumbnail image url      
        if ($product['size'] == '') {
            $product['size'] = ' Not Applicable';
        } else {
            $product['size'];
        }
        $product['designer'] = $model->getAttributeText('designer'); //product's thumbnail image url
         $add_to_cart = Mage::helper('checkout/cart')->getAddUrl($model);
       //$add_to_cart =  Mage::getUrl('checkout/cart/add/').'product/'.$product_id.'/';
        $product['add_to_cart'] = "setLocation('".$add_to_cart."')"; //product url
        
        $product['product_status'] = $model->getAttributeText('product_status'); //product's thumbnail image url
        $product['season_select'] = $model->getAttributeText('season'); //product's thumbnail image url
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'season');
        if ($attribute->usesSource()) {
            $product['season'] = $attribute->getSource()->getAllOptions(false);
        }
        $product['tag_select'] = $model->getAttributeText('tag_custom'); //product's tags
        $attribute_tag = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'tag_custom');
        if ($attribute_tag->usesSource()) {
            $product['tag_custom'] = $attribute_tag->getSource()->getAllOptions(false);
        }
        $product['shipped_to'] = $model['shipped_to']; //product's thumbnail image url  
        $prefix = Mage::getConfig()->getTablePrefix();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $prefix . 'tag_relation WHERE product_id=' . $product_id;
        $results = $readConnection->fetchAll($query);
        $tag_details = '';
        foreach ($results as $tagname) {
            $tagId = $tagname['tag_id'];
            $gg = Mage::getModel('tag/tag')->getCollection()
                    ->addFieldToFilter('tag_id', $tagId);
            $i = 0;
            foreach ($gg as $tagivalue) {
                $tag_name = $tagivalue['name'];
                $tag_id .= $tagivalue['tag_id'];
                $tag_details.= "<span class='tagitem'><a>" . $tag_name . "</a><i class='tagclose' tagval='" . $tag_id . "'>x</i></span>  ";
                $i++;
            }
        }
        $product['tag_details'] = $tag_details;
        $product['tag_id'] = $tag_id;
        echo json_encode($product);
    }

    public function inserttagAction() {
        $tagvalue = Mage::app()->getRequest()->getParam('tag');
        $productid = Mage::app()->getRequest()->getParam('productid');
        $eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
        $code = $eavAttribute->getIdByCode('catalog_product', 'tag_custom');


        $option['attribute_id'] = $code; //local
        $option['value']['tagvalue'][0] = $tagvalue;
        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        $setup->addAttributeOption($option);
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
//               
                ->setAttributeFilter($code); //live
        $newInsertedOption = $optionCollection->getLastItem();
        $_option = $newInsertedOption->getData();
        $lastId = $_option['option_id'];
        $tag_id[] = 0;
        $tag_id[] = $lastId;
        $product = Mage::getModel('catalog/product')->load($productid);
        $tags = $product->getTagCustom();
        $tag_values = implode(',', $tag_id);
        if ($tag_values) {
            $glue = ",";
        }
        $tag_values = $tag_values . $glue . $tags;
        $product->addData(array(
            'tag_custom' => $tag_values  // just putting together a comma-separated list of values
        ));
        $product->save();
        $insert_data['tag_id'] = $tag_id;
        $product_id[] = 0;
        $product_id[] = $productid;
        $insert_data['product_id'] = $productid;

        return $insert_data;
    }

    public function deletetagAction() {
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $tagname = Mage::app()->getRequest()->getParam('tag');
        $product_id = Mage::app()->getRequest()->getParam('productid');
        $model = Mage::getModel('catalog/product')->load($product_id);
        $attr = $model->getResource()->getAttribute("tag_custom");
        if ($attr->usesSource()) {
            $tagvalue = $attr->getSource()->getOptionId($tagname);
        }
        $attribute_code = 'tag_custom';
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
        $options = $attribute->getSource()->getAllOptions();
        $options['delete'][$tagvalue] = true;
        $options['value'][$tagvalue] = true;
        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        $setup->addAttributeOption($options);
        return true;
    }

    public function seasoninsertAction() {
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $seasonIds = Mage::app()->getRequest()->getParam('seasonnames');
        $seasonIds[] = 0;
        $productid = Mage::app()->getRequest()->getParam('productid');

        $season_values = implode(',', $seasonIds);
        //  echo $season_values;
        $product = Mage::getModel('catalog/product')->load($productid);
        $product->addData(array(
            'season' => $season_values  // just putting together a comma-separated list of values
        ));

        $product->save();
    }

}
?>

