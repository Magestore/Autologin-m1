<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_DemoHeader
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Autologin Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_DemoHeader
 * @author      Magestore Developer
 */
 
class Magestore_Autologin_Adminhtml_BackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction(){
		$session = Mage::getSingleton('admin/session');
		
		try {
			$account = Mage::getStoreConfig('autologin/backend/demo_account');
			if(empty($account)){
				$account = 'sandbox'; //default use sandbox account
			}
			$admin = Mage::getSingleton('admin/user')->loadByUsername($account);
			$admin->reload();
			Mage::getSingleton('admin/user')->getResource()->recordLogin($admin);
			//Mage::getResourceModel('admin/user')->recordLogin($admin);//->getResource()
			$session->renewSession();
			if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
				Mage::getSingleton('adminhtml/url')->renewSecretUrls();
			}
			$session->setIsFirstPageAfterLogin(false);
			$session->setUser($admin);
			$session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
			if (version_compare(Mage::getVersion(), '1.7.2.0', '>') === true) {
				$this->_response->clearHeaders()
						->setRedirect(Mage::helper("adminhtml")->getUrl(Mage::getStoreConfig('autologin/backend/redirect_url')))
						->sendHeadersAndExit();
			}else{
				$this->_response->clearHeaders()
						->setRedirect(Mage::helper("adminhtml")->getUrl(Mage::getStoreConfig('autologin/backend/redirect_url')));
			}
			$this->getRequest()->setDispatched(true);
			
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
			
		} catch (Mage_Core_Exception $e) {
            Mage::dispatchEvent('admin_session_user_login_failed',
                array('user_name' => $admin->getUsername(), 'exception' => $e));
            if ($request && !$request->getParam('messageSent')) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $request->setParam('messageSent', true);
            }
        }
		return;
	}
	
	public function preDispatch(){
		Mage::getDesign()
            ->setArea($this->_currentArea)
            ->setPackageName((string)Mage::getConfig()->getNode('stores/admin/design/package/name'))
            ->setTheme((string)Mage::getConfig()->getNode('stores/admin/design/theme/default'))
        ;
        foreach (array('layout', 'template', 'skin', 'locale') as $type) {
            if ($value = (string)Mage::getConfig()->getNode("stores/admin/design/theme/{$type}")) {
                Mage::getDesign()->setTheme($type, $value);
            }
        }
        $this->getLayout()->setArea($this->_currentArea);
		
		Mage::dispatchEvent('autologin_backend_action_predispatch_start', array());
		parent::preDispatch();
		$this->getRequest()->setDispatched(true);
		$session = Mage::getSingleton('admin/session');
		$session->refreshAcl();
		$session = Mage::getSingleton('admin/session');
		if ($session->isLoggedIn()) {
			$redirectUrl = Mage::getStoreConfig('autologin/backend/redirect_url');
			if($redirectUrl == ''){
				$redirectUrl = 'adminhtml/index/index';
			}
			$redirectUrl = Mage::helper("adminhtml")->getUrl($redirectUrl);
			if (version_compare(Mage::getVersion(), '1.7.2.0', '>') === true) {
				$this->_response->clearHeaders()
						->setRedirect($redirectUrl)
						->sendHeadersAndExit();
			}else{
				$this->_response->clearHeaders()
						->setRedirect($redirectUrl);
			}
        }
		return;
	}
	
	/**
     * Check if user has permissions to access this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}