<?php

class App_Service_PosterousClient extends Zend_Http_Client {

	/**
	 * @see http://apibeta.posterous.com
	 * @var <type> 
	 */
	private $m_sBaseUrl = 'http://posterous.com/api/2';

	private $m_sApiToken;

	/**
	 * Set a Site id to get data from
	 * every `posterous` account can have multiple sites.
	 * if site id empty, primary account ill be used.
	 * @var int
	 */
	private $m_iSiteId;

	/**
	 * sites value: 'primary' or an id
	 * used in the uri
	 * @var mixed
	 */
	private $m_mSite = 'primary';

	/**
	 * users vaue: 'me' or an id
	 * used in the uri
	 * @var mixed
	 */
	private $m_mUser = 'me';

	private $m_oBody;

	public function __construct() {
		parent::__construct(null, array(
			'useragent' => 'Stefano_Oldeman_s_Ketchup_Http',
			'timeout' => 10,
		));

		$l_aConfig = Zend_Registry::get('config')->posterous->toArray();
		
		$this->setApiToken($l_aConfig['api']['token']);
		$this->setAuth($l_aConfig['user']['username'], $l_aConfig['user']['password']);
    }

	public function fetchPosts() {
		$this->doRequest(self::GET, 'posts');
		//insert comment here, so far; nothing else to do here.
		return $this->getBody();
	}




	// ---------- Getters and setters ----------- \\

	public function setUser($p_mVal) { $this->m_mUser = $p_mVal; }
	public function getUser() { return $this->m_mUser; }

	public function setSite($p_mVal) { $this->m_mSite = $p_mVal; }
	public function getSite() { return $this->m_mSite; }


	// ---------- System wise methods ------------ \\

	/**
	 * doRequest
	 * @param int $p_iMethod use constants: POST or GET
	 * @param string $p_sMethodUri
	 * @param array $p_aParams request key => value
	 */
	protected function doRequest($p_iMethod, $p_sMethodUri, $p_aParams = array()) {
		
		$l_aRequestValues = $p_aParams;
		$l_aRequestValues['api_token'] = $this->getApiToken();

		$l_sMethod = 'setParameter' . ($p_iMethod == self::POST ? 'Post' : 'Get');
		foreach($l_aRequestValues as $l_sName => $l_mValue) {
			$this->$l_sMethod($l_sName, $l_mValue); //magic ;)
		}

		switch($p_sMethodUri) {
			case 'users':
				$l_sUri = $this->getBaseUrl();
				break;

			case 'sites':
				$l_sUri = $this->getBaseUrl() . '/users/' . $this->getUser();
				break;

			case 'posts':
			case 'pages':
			case 'theme':
				$l_sUri = $this->getBaseUrl() . '/users/' . $this->getUser() . '/sites/' . $this->getSite();
			break;
		}

		$this->setUri($l_sUri . '/' . $p_sMethodUri)->request($p_iMethod);

		//@fixme PosterousClient::doReqeust() just trow something if we don't have results.. and tell me why
		//the casting here smells!!!
		$l_oResult = (object) json_decode($this->getLastResponse()->getBody()); 
		$this->setBody($l_oResult);
		
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

}