<?php
class Magestore_Autologin_DemoController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
		$email = Mage::getStoreConfig('autologin/general/demo_account');
		$customer = Mage::getModel('customer/customer')->getCollection()
					->addFieldToFilter('email', $email)
					->getFirstItem();
		
		if(!$email){
			return $this->_redirect('');
		}
		
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		$this->_redirect(Mage::getStoreConfig('autologin/general/redirect_url'));
    }
}