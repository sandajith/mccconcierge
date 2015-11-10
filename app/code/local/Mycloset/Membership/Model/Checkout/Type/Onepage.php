<?php
class Mycloset_Membership_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage{
public function saveExcellence($data){
  
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        $this->getQuote()->setExcellenceLike($data['like']);
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
 
        $this->getCheckout()
        ->setStepData('excellence', 'allow', true)
        ->setStepData('excellence', 'complete', true)
        ->setStepData('billing', 'allow', true);
 
        return array();
    }

}