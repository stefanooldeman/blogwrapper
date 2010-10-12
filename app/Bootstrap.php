<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	private $config;
	
	public function _initAutoload()
	{
		$l_oAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath' => APPLICATION_PATH));
		
		return $l_oAutoloader;
	}

	public function _initConfig() {
		Zend_Registry::set('config',
			new Zend_Config_Ini(APPLICATION_PATH . '/config.ini', APPLICATION_ENV));
			$this->config = Zend_Registry::get('config');

		foreach($this->config->phpSettings->toArray() as $key => $value) {
			ini_set($key, $value);
		}
		var_dump(ini_get('html_errors'));
	}

	public function _initViewRender() {

		$this->bootstrap('View');
		$l_oViewInterface = new Dwoo_Adapters_ZendFramework_View(array(
			'engine' => array(
				'cacheDir' => $this->config->dwoo->dirs->cached,
				'compileDir' => $this->config->dwoo->dirs->compiled
			)
		));
		$l_oViewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($l_oViewInterface);
		
		Zend_Controller_Action_HelperBroker::addHelper($l_oViewRenderer);
	}
	
	/**
     * _initLayout sets the doctype, headMeta and headTitle
     */
	/*
    public function _initViewHelper()
    {
        $this->bootstrap('Layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        $view->headMeta()->appendHttpEquiv('Content-type:', 'text/html;charset=utf-8');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('Zend Framework');

		$view->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);
		$view->headScript()->appendFile('/js/jquery/jquery-1.4.2.js');
		$view->headScript()->appendFile('/js/base.js');
    }

	public function _initRoutes() {
		
		$this->bootstrap('frontController');
		$l_oRouter = $this->getResource('frontController')->getRouter();

		$l_oRouter->addRoute('addAccount', new Zend_Controller_Router_Route('/account/add/:service',
			array('controller' => 'account', 'action' => 'add',)));
		
	}*/
}

// dirty function to dump a var and die
function dumpAndDie() {
	if(extension_loaded('xdebug') == true) {
		header('content-type: html/plain');
	} else {
		header('content-type: text/plain');
	}
	
	$l_aArgs = func_get_args();
	foreach($l_aArgs as $l_mArg) var_dump($l_mArg);
	die;
}


