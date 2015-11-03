<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Mycloset_Membership_Block_Adminhtml_Customer_Edit_Tab_Addresses
    extends Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mycloset_membership/tab/addresses.phtml');
    }
}
