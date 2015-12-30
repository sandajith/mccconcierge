<?php

class Mycloset_Membership_Block_Adminhtml_Membership_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @var Batch code entity id
     */
    protected $_entityid;

    /**
     * Class constructor
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Set value to @var Batch code entity id
     */
    public function setEntityId($entity) {
        $this->_entityid = $entity;
    }

    /**
     * Get value of @var Batch code entity id
     * @return integer
     */
    public function getEntityId() {
        return $this->_entityid;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Bridge_Batchcode_Block_Adminhtml_Batchcode_Edit_Form
     */
    protected function _prepareForm() {
        $id = $this->getRequest()->getParam('membership_id');

        $this->setEntityId($id);
        $data = $this->_prepareCollection();

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('membership_id' => $id)),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('edit_form_fieldset', array(
            'legend' => Mage::helper('mycloset_membership')->__('Membership plan information')
        ));
        $fieldset->addField('membership_id', 'hidden', array(
            'label' => Mage::helper('mycloset_membership')->__('Plan Id'),
            'class' => 'textbox',
            'required' => false,
            'name' => 'membership_id',
        ));
        $fieldset->addField('membership_type', 'text', array(
            'label' => Mage::helper('mycloset_membership')->__('Plan name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'membership_type',
        ));
        $fieldset->addField('membership_price', 'text', array(
            'label' => Mage::helper('mycloset_membership')->__('Membership Price'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'membership_price',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * Prepare form collection object
     *
     * @return this
     */
    protected function _prepareCollection() {
        $data = Mage::getModel('membership/membership')->load($this->getEntityId());
        return $data;
    }

}
