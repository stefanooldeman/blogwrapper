<?php

class App_Post_Reader {

	private $m_oAdapter;

	const ADAPTER_POSTEROUS = 'posterous';
	const ADAPTER_TUMBLR	= 'tumblr';
	const ADAPTER_FILE		= 'tumblr';


	public function __construct() {
		//load config and set some values.
		//as setting the adapter, and markup language.
		$this->loadSettings();
	}

	public function fetchPost($id) {
		throw new ErrorException('method not implemented yet. on call to App_Post_Reader::fetchPost($id)..');
	}

	public function fetchOverview() {

		$l_oAdapter = $this->getAdapter();
		try {
			$l_aResult = $l_oAdapter->fetchPosts(); //for testing the rest client
		} catch(Zend_Http_Client_Adapter_Exception $exception) {
			return false;
		}

		$config = Zend_Registry::get('config');
		$l_sMarkup = $config->post->markup;


		$l_aCollection = array();
		foreach($l_aResult as $l_oStd) {
			$l_oAdapter->setPost(new App_Post_Post($l_sMarkup));
			$l_oPost = $l_oAdapter->fillPost($l_oStd);
			$l_oPost->formatBody();
			$l_aCollection[] = $l_oPost->toArray();
		}
		return $l_aCollection;
	}

	protected function loadSettings() {
		$config = Zend_Registry::get('config');
		$l_sServiceName = $config->post->service;
		switch($l_sServiceName) {
			case self::ADAPTER_POSTEROUS:
			case self::ADAPTER_TUMBLR:
			case self::ADAPTER_FILE:
				$l_sClass = $config->autoloaderNamespaces->app . 'Service_' . ucfirst($l_sServiceName) . 'Client';
				$this->setAdapter(new $l_sClass());
			break;

			default:
				throw new ErrorException('check your config. invalid option on "post.service" (this service is not yet implemented nor suported)');
		}
	}

	public function setAdapter($p_oService) {
		$this->m_oAdapter = $p_oService;
	}

	public function getAdapter() {
		return $this->m_oAdapter;
	}
}