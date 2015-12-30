<?php

class Mycloset_Membership_Adminhtml_MembershipController extends Mage_Adminhtml_Controller_Action {

//    public function indexAction() {
//        $this->loadLayout();
//        $this->renderLayout();
//    }


    public function indexAction() {
        $this->_title($this->__('Sales'))->_title($this->__('Membership Plans'));
        $this->loadLayout();

        $this->_setActiveMenu('customer');
        $this->_setActiveMenu('customer/membership');
        $this->_addContent($this->getLayout()->createBlock('membership/adminhtml_membership'));
        $this->renderLayout();
    }

    public function gridAction() {

        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('membership/adminhtml_membership_grid')->toHtml()
        );
    }

    public function newAction() {
        $this->_title($this->__('Membership'))->_title($this->__('New Membership Plan'));
        $this->loadLayout();
        $this->_setActiveMenu('customer');
        $this->renderLayout();
    }

    public function editAction() {
        $this->_title($this->__('Membership'))->_title($this->__('Edit Membership Plan'));

        $id = $this->getRequest()->getParam('membership_id', null);
        $model = Mage::getModel('membership/membership');
        if ($id) {
            $model->load((int) $id);

            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mycloset_membership')->__('Membership plan does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('membership_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('customer');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function searchAction() {
        $model = Mage::getModel('membership/membership');
        Mage::register('membership_data', $model);
        $this->loadLayout();
        $this->_setActiveMenu('customer');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * membership delete action
     * Delete membership 
     */
    public function deleteAction() {
        $membershipId = $this->getRequest()->getParam('id');
        if ($membershipId) {
            try {
                $membership = Mage::getModel('membership/membership');

                $membership->load($membershipId)->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mycloset_membership')->__('The membeship has been deleted.'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     * Delete multiple membership
     */
    public function massDeleteAction() {
        $membershipIds = $this->getRequest()->getParam('membership_id');

        if (!is_array($membershipIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mycloset_membership')->__('Please select membeships(s).'));
        } else {
            try {
                $membership = Mage::getModel('membership/membership');
                foreach ($membershipIds as $membershipId) {
                    $membership->load($membershipId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('mycloset_membership')->__('Total of %d record(s) were deleted.', count($membershipIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * membership save action
     */
    public function saveAction() {
        try {
            $params = $this->getRequest()->getPost();
            $get = $this->getRequest()->getParams();
            $id = $params['membership_id'];
            $edit = false;
            if ($id) {
                $membership = Mage::getModel('membership/membership')
                        ->load($id);
                $action = Mage::helper('mycloset_membership')->__('Edited');
                $edit = true;
            } else {
                $membership = Mage::getModel('membership/membership');
                $action = Mage::helper('mycloset_membership')->__('Added');
            }



            $membership
                    ->setMembershipType($params['membership_type'])
                    ->setMembershipPrice($params['membership_price'])
                    ->save();
            $this->_getSession()->addSuccess($this->__('The Membership has been successfully ' . $action));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }
        if ($get['back']) {
            $this->_redirect('*/*/' . $get['back'], array('_current' => true, 'id' => $id));
        } else {
            $this->_redirect('*/*/index');
        }
    }

}
