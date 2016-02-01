<?php

class Mycloset_Membership_Block_Adminhtml_Customer_Login extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

  public function render(Varien_Object $row)
{
//$customerId =  $row->getData($this->getColumn()->getIndex());

$value = '<a href="http://localhost/neenu/index.php/admin/customer/memberlogin" target="_blank">Login</a>';
return $value;
}

}
