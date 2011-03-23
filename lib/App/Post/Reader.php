<?php

class App_Post_Reader {

	/**
	 * @var App_Post_Post
	 */
	private $m_oPost;

	private $m_oAdapter;

	private $m_sOutputType;

	const OUTPUT_MARKDOWN	= 'markdown';
	const OUTPUT_TEXTILE	= 'textile';
	const OUTPUT_RAW		= 'raw';

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
		$l_aResult = $this->getAdapter()->fetchPosts(); //for testing the rest client

		$l_aCollection = array();
		foreach($l_aResult as $l_oStd) {
			$l_oPost = new App_Post_Post();
			//@todo abstract filling in a post object. this migh be tottaly different when using other services..
			$l_oPost->setId($l_oStd->id);
			$l_oPost->setTitle($l_oStd->title);
			$l_sDate = date('d-m-Y', strtotime($l_oStd->display_date));
			$l_oPost->setDate($l_sDate);

			$l_sBody = $l_oStd->body_html;
			
			$l_sBody = preg_replace_callback(
				'/(\[\[)(posterous-content\:)([A-z]+)(\]\])/',
				create_function('$p_aMatches','return $p_aMatches[3];'),
				$l_sBody
			);

			//we decode htmldecode for the markup parsers.. don't trust api results
			$l_sBody = htmlspecialchars_decode(strip_tags($l_sBody));

			switch($this->getOutputType()) {
				case self::OUTPUT_MARKDOWN:
					$l_sBody = Markdown($l_sBody);
					break;

				case self::OUTPUT_TEXTILE:
					throw new ErrorException('This type of output markup is not implemented yet');
					break;

				case self::OUTPUT_RAW:
					//do nothing....
			}
			$l_oPost->setBody($l_sBody);

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

		$l_sMarkUpType = $config->post->markup;
		switch($l_sMarkUpType) {
			case self::OUTPUT_MARKDOWN:
			case self::OUTPUT_TEXTILE:
			case self::OUTPUT_RAW:
				$this->setOutputType($l_sMarkUpType);
			break;

			default:
				throw new ErrorException('check your config. invalid option on "post.markup" (unsupported markup type was set)');
		}
	}

	public function setAdapter($p_oService) {
		$this->m_oAdapter = $p_oService;
	}

	public function getAdapter() {
		return $this->m_oAdapter;
	}

	public function setOutputType($p_sFlag) {
		$this->m_sOutputType = $p_sFlag;
	}
	public function getOutputType() {
		return $this->m_sOutputType;
	}


}