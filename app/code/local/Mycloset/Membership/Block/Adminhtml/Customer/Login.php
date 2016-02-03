<?php

class Mycloset_Membership_Block_Adminhtml_Customer_Login extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

  public function render(Varien_Object $row)
{
if(!Mage::registry('adminauth')){
	Mage::register('adminauth', 1);
}
$customerId =  $row->getId();
$value = '<a href="'.Mage::getUrl().'membership/memberaccount/memberlogin/id/'.$customerId.'" target="_blank">Login</a>';
return $value;
}
}