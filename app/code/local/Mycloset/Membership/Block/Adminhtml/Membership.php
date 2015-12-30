<?php

class Mycloset_Membership_Block_Adminhtml_Membership extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {

        $this->_controller = 'adminhtml_membership';
        $this->_blockGroup = 'membership';
        $this->_headerText = Mage::helper('mycloset_membership')->__('Membership Plans');
        $this->_addButtonLabel = Mage::helper('mycloset_membership')->__('Add a Membership Plans');
        parent::__construct();
    }

    protected function _prepareLayout() {
        $this->setChild('grid', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_grid', $this->_controller . '.grid')->setSaveParametersInSession(true));
        return parent::_prepareLayout();
    }

}
