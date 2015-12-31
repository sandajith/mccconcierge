<?php

class Mycloset_Membership_Block_Adminhtml_Membership_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'membership';
        $this->_controller = 'adminhtml_membership';
        $this->_mode = 'edit';
        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('mycloset_membership')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
          $this->_updateButton('save', 'label', Mage::helper('mycloset_membership')->__('Save Membership Plan'));
        $this->_formScripts[] = "
            function toggleEditor()
            {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
            function saveAndContinueEdit()
            {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Make the header text
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('membership_data') && Mage::registry('membership_data')->getMembershipId()) {

            return Mage::helper('mycloset_membership')->__('Edit Membership Plan') . Mage::helper('mycloset_membership')->__("%s", $this->htmlEscape(Mage::registry('membership_data')->getMembershipId()));
        } else {
            return Mage::helper('mycloset_membership')->__('New Membership Plan');
        }
    }

}
