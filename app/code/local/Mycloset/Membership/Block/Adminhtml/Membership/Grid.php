<?php

class Mycloset_Membership_Block_Adminhtml_Membership_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('membershipGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setDefaultSort('membership_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('membership/membership')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('membership_id', array(
            'header' => Mage::helper('mycloset_membership')->__('ID'),
            'align' => 'right',
            'width' => '10px',
            'index' => 'membership_id',
        ));

        $this->addColumn('membership_type', array(
            'header' => Mage::helper('mycloset_membership')->__('Membership Plan'),
            'align' => 'left',
            'index' => 'membership_type',
            'width' => '50px',
        ));


        $this->addColumn('membership_price', array(
            'header' => Mage::helper('mycloset_membership')->__('Price ($)'),
            'width' => '150px',
            'index' => 'membership_price',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('mycloset_membership')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('mycloset_membership')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'membership_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
                )
        );
        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('membership_id');
        $this->getMassactionBlock()->setFormFieldName('membership_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('mycloset_membership')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('mycloset_membership')->__('Are you sure?')
        ));

        return $this;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
