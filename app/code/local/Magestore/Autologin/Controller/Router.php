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
 * Autologin Controller Router
 * 
 * @category    Magestore
 * @package     Magestore_DemoHeader
 * @author      Magestore Developer
 */

class Magestore_Autologin_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard {

	/**
     * Match the request
     *
     * @param Zend_Controller_Request_Http $request
     * @return boolean
     */
    public function match(Zend_Controller_Request_Http $request)
    {
		$path = trim($request->getPathInfo(), '/');
		if ($path) {
            $p = explode('/', $path);
			$controllerName = isset($p[1]) ? $p[1] : 'index';
			$actionName = isset($p[2]) ? $p[2] : 'index';
			if(isset($p[0]) && $p[0] != 'autologin'){
				return false;
			}
			
			if($controllerName == 'backend'){
				$request->setModuleName('autologin')
				->setControllerName('adminhtml_backend')
				->setActionName('index');
				//->setDispatched(true);
				return true;
			}
			if($controllerName == 'frontend' || $controllerName == 'demo'){
				$request->setModuleName('autologin')
				->setControllerName('demo')
				->setActionName('index');
				//->setDispatched(true);
				return true;
			}
			elseif($controllerName == 'index' && $actionName == 'index'){
				echo "Backend: <a href='".Mage::helper("adminhtml")->getUrl("autologin/backend/index")."'><strong>autologin/backend/index</strong></a></br>";
				echo "Frontend: <a href='".Mage::getUrl("autologin/frontend/index")."'><strong>autologin/frontend/index</strong> OR <strong>autologin/demo/index</strong></a>";
				exit;
			}
        }
		return false;
		//return parent::match($request);
	}
	
}
