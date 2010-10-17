<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {	
	private $config;

	public function _initAutoload() {
		$l_oAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'App_',
			'basePath' => APPLICATION_PATH));

		return $l_oAutoloader;
	}

	public function _initConfig() {
		if(DEBUG) {
			//due the use of public a repository I like to ignore this config file
			$pathToFile = APPLICATION_PATH . '/config.mirror.ini';
		} else {
			$pathToFile = APPLICATION_PATH . '/config.ini';
		}

		$this->config = new Zend_Config_Ini($pathToFile, APPLICATION_ENV);
		//this almost looks like a DependencyInjection Tool.. but its not
		//use the registry as kinda replacement of using singleton.
		Zend_Registry::set('config', $this->config);

		foreach($this->config->phpSettings->toArray() as $key => $value) {
			ini_set($key, $value);
		}

	}

	public function _initCache() {

		//Zend_Cache, type: file, serialization cache data: On
		$l_oZendCache = Zend_Cache::factory('Core', 'File', array(
				'lifetime' => (int) $this->config->cache->lifetime,
				'automatic_serialization' => true
			), array('cache_dir' => $this->config->cache->dir)
		);

		Zend_Registry::set('cache', $l_oZendCache);
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
    public function _initViewHelper() {
        $this->bootstrap('Layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        $view->headMeta()->appendHttpEquiv('Content-type:', 'text/html;charset=utf-8');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('Ketchup');

		$view->doctype(Zend_View_Helper_Doctype::HTML5);
		//$view->headScript()->appendFile('/js/jquery/jquery-1.4.2.js');
		//$view->headScript()->appendFile('/js/base.js');
    }

	/*
	public function _initRoutes() {

		$this->bootstrap('frontController');
		$l_oRouter = $this->getResource('frontController')->getRouter();

		$l_oRouter->addRoute('addAccount', new Zend_Controller_Router_Route('/account/add/:service',
			array('controller' => 'account', 'action' => 'add',)));
	}*/
}

// dirty function to dump a var and die
function dumpAndDie() {

	if(extension_loaded('xdebug') == true)
		header('content-type: text/html');
	else
		header('content-type: text/plain');

	$l_aArgs = func_get_args();
	foreach($l_aArgs as $l_mArg) var_dump($l_mArg);
	die;
}


