<?php

class Mycloset_Membership_Block_Adminhtml_Membership_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('membershipGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('membership/membership')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('membership')->__('ID'),
            'align' => 'right',
            'width' => '10px',
            'index' => 'id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('membership')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'width' => '50px',
        ));


        $this->addColumn('content', array(
            'header' => Mage::helper('membership')->__('Description'),
            'width' => '150px',
            'index' => 'content',
        ));
        return parent::_prepareColumns();
    }

}
