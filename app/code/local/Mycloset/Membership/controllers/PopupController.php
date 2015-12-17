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
            jqCustom('.getproductid').click(function () {
                var productid = jqCustom(this).attr('rel');
                jqCustom.ajax({
                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/product?productid=" + productid,
                    type: 'get',
                    dataType: "json",
                    success: function (data) {
                        if ((data['size'] == null) || data['size'] == false) {
                            data['size'] = "Not Applicable";
                        }
                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status'] + ' ' + data['shipped_to']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');
                        jqCustom('.tags').html('<div>' + data['tag_details'] + '<span id="remove" class="add add-text"><input type="text" class="inputz" pro="' + data['id'] + '" name="tagss" value=""></span><span id="remove pluse" class="tagpluse"><i class="tagadd">+</i></span></div></div>');
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
                                        var data = parseInt(data1);
                                        var tagname = jqCustom('.inputz').val();
                                        if (tagname != null) {
                                            jqCustom('.tags').prepend("<span>" + jqCustom('.inputz').val() + "<i class='tagclose' tagval='" + data + "' >X</i></span>");
                                            jqCustom("input.inputz").val('');
                                            jqCustom(".tagclose").on("click", function () {
                                                var tag1233 = jqCustom(this).attr("tagval");
                                                jqCustom.ajax({
                                                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/deletetag",
                                                    type: 'post',
                                                    data: {
                                                        tag: tag1233
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

    public function productdesignerAction() {
        $designer = Mage::app()->getRequest()->getParam('ProductIds');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', 4)
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
                        if ((data['size'] == null) || data['size'] == false) {
                            data['size'] = "Not Applicable";
                        }
                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status'] + ' ' + data['shipped_to']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');
                        jqCustom('.tags').html('<div>' + data['tag_details'] + '<span id="remove" class="add add-text"><input type="text" class="inputz" pro="' + data['id'] + '" name="tagss" value=""></span><span id="remove pluse" class="tagpluse"><i class="tagadd">+</i></span></div></div>');
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
                                        var tagname = jqCustom('.inputz').val();
                                        if (tagname != null) {
                                            var data = parseInt(data1);
                                            jqCustom('.tags').prepend("<span>" + jqCustom('.inputz').val() + "<i class='tagclose' tagval='" + data + "' >X</i></span>");
                                            jqCustom("input.inputz").val('');
                                            jqCustom(".tagclose").on("click", function () {
                                                var tag1233 = jqCustom(this).attr("tagval");
                                                jqCustom.ajax({
                                                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/deletetag",
                                                    type: 'post',
                                                    data: {
                                                        tag: tag1233
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

    public function productcolorAction() {
        $designer = Mage::app()->getRequest()->getParam('ProductIds');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', 4)
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
                        if ((data['size'] == null) || data['size'] == false) {
                            data['size'] = "Not Applicable";
                        }
                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status'] + ' ' + data['shipped_to']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');
                        jqCustom('.tags').html('<div>' + data['tag_details'] + '<span id="remove" class="add add-text"><input type="text" class="inputz" pro="' + data['id'] + '" name="tagss" value=""></span><span id="remove pluse" class="tagpluse"><i class="tagadd">+</i></span></div></div>');
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
                                        var tagname = jqCustom('.inputz').val();
                                        if (tagname != null) {
                                            var data = parseInt(data1);
                                            jqCustom('.tags').prepend("<span>" + jqCustom('.inputz').val() + "<i class='tagclose' tagval='" + data + "' >X</i></span>");
                                            jqCustom("input.inputz").val('');
                                            jqCustom(".tagclose").on("click", function () {
                                                var tag1233 = jqCustom(this).attr("tagval");
                                                jqCustom.ajax({
                                                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/deletetag",
                                                    type: 'post',
                                                    data: {
                                                        tag: tag1233
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

    public function productstatusAction() {
        $designer = Mage::app()->getRequest()->getParam('ProductIds');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('product_status', $designer)
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', 4);
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
                        if ((data['size'] == null) || data['size'] == false) {
                            data['size'] = "Not Applicable";
                        }
                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status'] + ' ' + data['shipped_to']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');
                        jqCustom('.tags').html('<div>' + data['tag_details'] + '<span id="remove" class="add add-text"><input type="text" class="inputz" pro="' + data['id'] + '" name="tagss" value=""></span><span id="remove pluse" class="tagpluse"><i class="tagadd">+</i></span></div></div>');
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
                                        var tagname = jqCustom('.inputz').val();
                                        if (tagname != null) {
                                            var data = parseInt(data1);
                                            jqCustom('.tags').prepend("<span>" + jqCustom('.inputz').val() + "<i class='tagclose' tagval='" + data + "' >X</i></span>");
                                            jqCustom("input.inputz").val('');
                                            jqCustom(".tagclose").on("click", function () {
                                                var tag1233 = jqCustom(this).attr("tagval");
                                                jqCustom.ajax({
                                                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/deletetag",
                                                    type: 'post',
                                                    data: {
                                                        tag: tag1233
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

    public function seasonAction() {
        $designer = Mage::app()->getRequest()->getParam('ProductIds');
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $userid)
                ->addAttributeToFilter('season', $designer)
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', 4);
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
                        if ((data['size'] == null) || data['size'] == false) {
                            data['size'] = "Not Applicable";
                        }
                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status'] + ' ' + data['shipped_to']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');
                        jqCustom('.tags').html('<div>' + data['tag_details'] + '<span id="remove" class="add add-text"><input type="text" class="inputz" pro="' + data['id'] + '" name="tagss" value=""></span><span id="remove pluse" class="tagpluse"><i class="tagadd">+</i></span></div></div>');
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
                                        var tagname = jqCustom('.inputz').val();
                                        if (tagname != null) {
                                            var data = parseInt(data1);
                                            jqCustom('.tags').prepend("<span>" + jqCustom('.inputz').val() + "<i class='tagclose' tagval='" + data + "' >X</i></span>");
                                            jqCustom("input.inputz").val('');
                                            jqCustom(".tagclose").on("click", function () {
                                                var tag1233 = jqCustom(this).attr("tagval");
                                                jqCustom.ajax({
                                                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/deletetag",
                                                    type: 'post',
                                                    data: {
                                                        tag: tag1233
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
        $product['ShortDescription'] = $model->getShortDescription(); //product's short description
        $product['Description'] = $model->getDescription(); // product's long description
        $product['Name'] = $model->getName(); //product name
        $product['Price'] = $model->getPrice(); //product's regular Price
        $product['SpecialPrice'] = $model->getSpecialPrice(); //product's special Price
        $product['ProductUrl'] = $model->getProductUrl(); //product url
        $product['ImageUrl'] = $model->getImageUrl(); //product's image url
        $product['color'] = $model->getAttributeText('color'); //product's thumbnail image url
        $product['sku'] = $model->getSku(); //product's thumbnail image url      
        $product['size'] = $model->getAttributeText('size'); //product's thumbnail image url      
        if ($product['size'] == '') {
            echo 'Not Applicable';
        } else {
            echo $product['size'];
        }
        $product['designer'] = $model->getAttributeText('designer'); //product's thumbnail image url
        $product['product_status'] = $model->getAttributeText('product_status'); //product's thumbnail image url
        $product['season'] = $model->getAttributeText('season'); //product's thumbnail image url
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
                $tag_details.= "<span class='tagitem'><a>" . $tag_name . "</a><i class='tagclose' tagval='" . $tag_id . "'>X</i></span>  ";
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
        if (!empty($tagvalue)) {
            $user_id = Mage::getSingleton('customer/session')->getId();
            $userid = ($user_id ? $user_id : 0);
            $collection = Mage::getModel('tag/tag')
                    ->setName($tagvalue)
                    ->setStatus(1)
                    ->setFirstCustomerId($userid)
                    ->setFirstStoreId(1);
            echo $insertId = $collection->save()->getId();
            $tag_id11111 = $insertId;

            $collection2 = Mage::getModel('tag/tag_relation')
                    ->setTagId($tag_id11111)
                    ->setCustomerId($userid)
                    ->setProductId($productid)
                    ->setStoreId(1)
                    ->setActive(1)
                    ->save();
            return $insertId;
        } return false;
    }

    public function deletetagAction() {
        $user_id = Mage::getSingleton('customer/session')->getId();
        $userid = ($user_id ? $user_id : 0);
        $tagvalue = Mage::app()->getRequest()->getParam('tag');
//         $tag = Mage::getModel('tag/tag')->load($tagvalue);
//            $tag->delete();
        $model = Mage::getModel('tag/tag_relation');
        $model->loadByTagCustomer(null, $tagvalue, $userid)->delete();
        $prefix = Mage::getConfig()->getTablePrefix();
        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write');
//        $write->query("delete from `'.$prefix.'tag_relation` where tag_id = ".$tagvalue);
        $write->query("delete from `" . $prefix . "tag` where tag_id = " . $tagvalue);
        return true;
    }

}
?>

