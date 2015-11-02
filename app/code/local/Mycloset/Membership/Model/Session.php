<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mycloset_Membership_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct() {
        $this->init('mycloset');
    }
}