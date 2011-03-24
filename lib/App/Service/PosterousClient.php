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

	protected $cache;

	/**
	 * @var App_Post_Post
	 */
	public $m_oPost;

	public function __construct() {
		parent::__construct(null, array(
			'useragent' => 'Stefano_Oldeman_s_Ketchup_Http',
			'timeout' => 10,
		));

		$l_aConfig = Zend_Registry::get('config')->posterous->toArray();

		$this->setApiToken($l_aConfig['api']['token']);
		$this->setAuth($l_aConfig['user']['username'], $l_aConfig['user']['password']);

		$this->cache = Zend_Registry::get('cache');
    }

	public function fetchPosts() {
		$this->doRequest(self::GET, 'posts');
		//insert comment here, so far; nothing else to do here.
		return $this->getBody();
	}


	public function fillPost($p_oStdResponse) {

		$l_oPost = $this->getPost();

		$l_oPost->setId($p_oStdResponse->id);
		$l_oPost->setTitle($p_oStdResponse->title);
		$l_sDate = date('d-m-Y', strtotime($p_oStdResponse->display_date));
		$l_oPost->setDate($l_sDate);
		$l_sBody = $p_oStdResponse->body_full;

		$l_sBody = preg_replace_callback(
			'/(\[\[)(posterous-content\:)([A-z]+)(\]\])/',
			create_function('$p_aMatches','return $p_aMatches[3];'),
			$l_sBody
		);
		$l_oPost->setBody($l_sBody);
		return $l_oPost;
	}

	// ---------- Getters and setters ----------- \\

	public function setUser($p_mVal) { $this->m_mUser = $p_mVal; }
	public function getUser() { return $this->m_mUser; }

	public function setSite($p_mVal) { $this->m_mSite = $p_mVal; }
	public function getSite() { return $this->m_mSite; }


	// ---------- System wise methods ------------ \\

	//dependency injection
	public function setPost($p_oPost) { $this->m_oPost = $p_oPost; }
	public function getPost() { return $this->m_oPost; }

	/**
	 * doRequest
	 * @param int $p_iMethod use constants: POST or GET
	 * @param string $p_sMethodUri
	 * @param array $p_aParams request key => value
	 */
	protected function doRequest($p_iMethod, $p_sMethodUri, $p_aParams = array()) {

		$l_aRequestValues = $p_aParams;
		$l_aRequestValues['api_token'] = $this->getApiToken();

		$l_oCache = $this->getCache();
		$l_sCacheKey = pathinfo(__FILE__, PATHINFO_FILENAME) . '_' . $p_sMethodUri . '_' . md5($p_iMethod . serialize($p_aParams));

		//GO HOME EARLY
		$l_oData = $l_oCache->load($l_sCacheKey);
		if($l_oData !== false) {
			$this->setBody($l_oData);
			return $l_oData;
		}

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
		$l_oResult = json_decode($this->getLastResponse()->getBody());
		$this->setBody($l_oResult);
		$l_oCache->save($l_oResult, $l_sCacheKey);
		
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