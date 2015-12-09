<?php

class Mycloset_Membership_Block_Extra_Links extends Mage_Core_Block_Template
{
      public function addPhoneLink()
    {       
        $parentBlock = $this->getParentBlock();

            $text = $this->__('917.684.0609');
//            $url=$this->__('tel:917.684.0609');
            $parentBlock->addLink(
                $text, "callto:917.684.0609", $text,
                true, array('_secure' => true), 40, null,
                'onclick="javascript:return false;" class="top-link-phone" '
            );

        return $this;
    }
}
