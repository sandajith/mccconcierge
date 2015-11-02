
<?php
$p = $this->getProductdetails();
$cat = Mage::getModel('catalog/category')->load(16);
$subcats = $cat->getChildren();
$all_sub_cat = explode(',', $subcats);
 $_product = Mage::registry('current_product'); 
?>

<div class="popupHead">
    <?php echo $p['sku']; ?> &nbsp;&nbsp; <a class="close">X</a>
</div>
<div class="popupContent">
    <div class="row">
        <div class="col-sm-6 image">
            <img alt="" src="<?php echo $p['ImageUrl']; ?>"/>                                                                                
        </div>
        <div class="col-sm-6 details">
            <h2><?php echo'Dress' ?></h2>
            <p class="signature"><?php echo $p['designer']; ?></p>
            <div class="colorBox">
                <a style=" background:<?php echo $p['color']; ?>"></a>
            </div>
            <div class="size"><i>Size <?php echo $p['size']; ?></i></div>
            <div class="ssn"><i><?php echo $p['season']; ?></i></div>
            <div class="tags">
                <b>Tags</b>
                <span class="tagitem"><a>Formal</a> <i class='tagclose'>X</i></span>
                <span class="tagitem"><a>Formal</a> <i class='tagclose'>X</i></span>
                <span class="tagitem"><a>Formal</a> <i class='tagclose'>X</i></span>
                <span id="remove" class="add"><input type="text" class="inputz" name="tagss" value=""></span>

            </div>
            <p class="description">
                <b>Description</b> :   <?php echo $p['ShortDescription']; ?>
            </p>
            <p class="status">
                <b>Status</b> : <?php echo $p['product_status']; ?>
            </p>
            <?php
            
            
            if ($p['product_status'] == 'We Have It') { ?>
                <button type="button" title="<?php echo $this->__('Deliver Me') ?>" class="button btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($_product) ?>')"><span><span><?php echo $this->__('Deliver Me') ?></span></span></button>                   
            <?php } else if ($p['product_status'] == 'You Have It') { ?>
                <a  title="<?php echo $this->__('Pick Me Up') ?>" class="button btn-cart" href="'<?php echo $this->getAddToCartUrl($_product) ?>"><?php echo $this->__('Pick Me Up') ?></a>  
            <?php } ?>

        </div>
    </div>
    <div>
        <div class="customDropDown pull-left">
            <div class="dropContentBox">
                <b>SORT BY</b>
                <span>Category</span>
                <ul>
                    <li> <b>Category</b>
                        <ul>                         
                            <?php
                            foreach ($all_sub_cat as $cat_id) {
                                $category = Mage::getModel('catalog/category')->load($cat_id);
                                $products = Mage::getModel('catalog/category')->load($category->getId())
                                        ->getProductCollection()
                                        ->addAttributeToSelect('entity_id')
                                        ->addAttributeToFilter('status', 1)
                                        ->addAttributeToFilter('visibility', 4);
                                ?>
                                <li><?php echo $category->name; ?></li> 
                            <?php } ?>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <script type="text/javascript">
//        function SubmitRequest(productid)
//    {
//        
//    }
    </script>


    <!--slider-->


    <div class="flexsliderPopup carousel">
        <ul class="slides">
            <?php
            foreach ($all_sub_cat as $cat_id1) {
                $category = Mage::getModel('catalog/category')->load($cat_id1);
                $products = Mage::getModel('catalog/category')->load($category->getId())
                        ->getProductCollection()
                        ->addAttributeToSelect('entity_id')
                        ->addAttributeToFilter('status', 1)
                        ->addAttributeToFilter('visibility', 4);
                if ($products->count() > 0):   // active product count
                    ?>               
                    <!--            <li>-->
                                    <!--<a  onclick="SubmitRequest(<?php // echo $category->getId();    ?>);"> <img  src="<?php // echo Mage::getBaseUrl('media') . 'catalog/category/' . $category->image;    ?>" alt=""/></a>--> 
                    <li>
                        <!--<a onclick="setSelectedTestPlan(<?php echo $category->getId(); ?>);" href="javascript:void(0);"><img  src="<?php echo Mage::getBaseUrl('media') . 'catalog/category/' . $category->image; ?>" alt=""/></a>-->
                        <a class="getcatid" rel="<?php echo $category->getId(); ?>" ><img  src="<?php echo Mage::getBaseUrl('media') . 'catalog/category/' . $category->image; ?>" alt=""/></a>
                        <!--               </li>  -->
                    </li>    
                    <?php
                endif;
            }
            ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    var jqCustom = jQuery.noConflict();

    jqCustom(document).ready(function () {
        //slider change images of product
        jqCustom('.getcatid').click(function () {
            var catid = jqCustom(this).attr('rel');
            jqCustom.ajax({
                url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/slider",
                type: 'post',
                data: {categoryId: catid},
                success: function (data) {
                    var data = 'ppp'
                    //row
//                    jqCustom('.slides').remove().append(data);
                    jqCustom('.flexsliderPopup').remove();
                    jqCustom(data).appendTo(".popupContent");
                    setTimeout(function () {
                        console.log(data);
                        console.log(jqCustom('.flexsliderPopup'));
                        jqCustom('.flexsliderPopup').flexslider({
                            animation: "slide",
                            animationLoop: false,
                            itemWidth: 150,
                            itemMargin: 5
                        });

                    }, 100);



                }
            });
        });
           jqCustom('.getproductid').click(function () {
           alert('sbdjh');
//            var productid = jqCustom(this).attr('rel');
        
        });

        var footHeight = jqCustom("footer").outerHeight();
        jqCustom(".footFix").css({"paddingBottom": footHeight + "px"});
        jqCustom("header .loginDetails span").click(function () {
            jqCustom("header .loginDetails .links").fadeToggle(function () {
                jqCustom("header .loginDetails .search").fadeToggle();
            });
        });
        jqCustom(".small-accordion h4").click(function () {
            if (jqCustom(this).next("div").css('display') == 'none')
            {
                jqCustom(".small-accordion > div").slideUp();
                jqCustom(this).next("div").slideDown();
            }
            else
            {
                jqCustom(this).next("div").slideUp();
            }

        })
        jqCustom(".customDropDown").click(function () {
            if (jqCustom(this).children().children("ul").css('display') == 'none')
            {
                jqCustom(this).children().children("ul").slideDown();
            }

        });
        jqCustom(".customDropDown ul li > b").click(function () {
            if (jqCustom(this).next("ul").is(":visible"))
            {
                jqCustom(this).next("ul").slideUp();
            }
            else
            {
                jqCustom(".customDropDown ul li > ul").slideUp();
                jqCustom(this).siblings("ul").slideDown();
            }
        })
        jqCustom(".customDropDown ul li ul li").click(function () {
            jqCustom(".customDropDown ul li ul li.selected").removeClass("selected");
            var thisHtml = jqCustom(this).html();
            console.log(thisHtml);
            jqCustom(".customDropDown ul").fadeOut(100);
            jqCustom(this).addClass("selected").parents("ul").siblings("span").html(thisHtml);

        });
        jqCustom('.inputz').keypress(function (e) {
            var key = e.which;
            if (key == 13)  // the enter key code
            {
                jqCustom(".tags").append("<span>" + jqCustom('.inputz').val() + "<i class='tagclose'>X</i></span>");

                jqCustom("input.inputz").val('');
            }
        });
        jqCustom(".tagclose").click(function () {

//            jqCustom(this).fadeOut(100);
            jqCustom(this).find('span:last').remove();
        });
        jqCustom(".close").click(function () {
            jqCustom(".popupWrapper").fadeOut();
        });
    });

    jqCustom(window).resize(function () {
        var footHeight = jqCustom("footer").outerHeight();
        jqCustom(".footFix").css({"paddingBottom": footHeight + "px"})
        jqCustom('.flexsliderPopup').flexslider({
            animation: "slide",
            animationLoop: false,
            itemWidth: 150,
            itemMargin: 5
        });
    });

    jqCustom(window).load(function () {
        jqCustom('.flexslider').flexslider({
            animation: "slide",
            animationLoop: false,
            itemWidth: 350,
            itemMargin: 5
        });
    });

</script>