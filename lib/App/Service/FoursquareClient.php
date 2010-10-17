<?php

class App_Service_FoursquareClient extends Zend_Http_Client {

	/**
	 * @see
	 * @var string
	 */
	private $m_sBaseUrl = 'http://feeds.foursquare.com/';

	private $m_sApiToken;

	protected $cache;

	public function __construct() {
		parent::__construct(null, array(
			'useragent' => 'Stefano_Oldeman_s_Ketchup_Http',
			'timeout' => 10,
		));

		$config = Zend_Registry::get('config');
		if($this->getApiToken() == null) {
			$this->setApiToken($config->foursquare->feed->token);
		}

		$this->cache = Zend_Registry::get('cache');
	}

	public function getRecentCheckins($p_iLimit) {
		$this->doRequest('history', $p_iLimit);
		$l_oSimpleXml = simplexml_load_string($this->getBody());

		$arr = array();
		foreach($l_oSimpleXml->channel->item as $row) {
			//to string each atribute in the row by reference
			$arr[] = array(
				'title'			=> (string) htmlentities($row->title),
				'date'			=> (string) $row->pubDate,
				'url'			=> (string) $row->link,
				'guid'			=> (string) $row->guid,
				'description'	=> (string) htmlentities($row->description)
			);
		}
		return $arr;
	}

	/**
	 * doRequest
	 * @param int $p_iMethod use constants: POST or GET
	 * @param string $p_sMethodUri
	 */
	protected function doRequest($p_sMethodUri, $p_iLimit) {

		$l_sCacheKey = pathinfo(__FILE__, PATHINFO_FILENAME) . '_' . $p_sMethodUri . '_count_' . $p_iLimit;

		$l_oCache = $this->getCache();
		if(isset($l_oCache)) {
			$l_oData = $l_oCache->load($l_sCacheKey);
			if($l_oData !== false) {
				$this->setBody($l_oData);
				return $l_oData;
			}
			//GO HOME EARLY
		}
		
		switch($p_sMethodUri) {
			case 'history':
				$l_sUri = $this->getBaseUrl() . $p_sMethodUri . '/' . $this->getApiToken() . '.rss' . (isset($p_iLimit) ? '?count=' . $p_iLimit : '');
				break;
			
			default:
				throw new ErrorException('invalid uri method. cought in default switch case.');
		}

		$this->setUri($l_sUri)->request(self::GET);
		$l_oResult = $this->getLastResponse()->getBody();
		$this->setBody($l_oResult);

		if(isset($l_oCache)) {
			$l_oCache->save($l_oResult, $l_sCacheKey);
		}

		return $l_oResult;
	}

	public function setBody($p_oStd) {
		$this->m_oBody = $p_oStd;
	}

	public function getBody() {
		return $this->m_oBody;
	}

	public function setApiToken($p_sApiToken) {
		$this->m_sApiToken = $p_sApiToken;
	}

	public function getApiToken() {
		return $this->m_sApiToken;
	}
	
	public function getBaseUrl() {
		return $this->m_sBaseUrl;
	}

	//---- extra -----
	protected function getCache() {
		return $this->cache;
	}

	
}