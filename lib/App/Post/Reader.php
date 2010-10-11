<?php

class App_Post_Reader {

	/**
	 * @var App_Post_Post
	 */
	private $m_oPost;

	private $m_oAdapter;

	public function __construct() {
		$this->setAdapter(); //hard coded so far.
	}

	public function fetchPost($id) {
		
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

			$l_sBody = $l_oStd->body;
			
			$l_sBody = preg_replace_callback(
				'/(\[\[)(posterous-content\:)([A-z]+)(\]\])/',
				create_function('$p_aMatches','return $p_aMatches[3];'),
				$l_sBody
			);

			$l_oPost->setBody($l_sBody);

			$l_aCollection[] = $l_oPost->toArray();
		}
		return $l_aCollection;
	}

	public function setAdapter() {
		$this->m_oAdapter = new App_Service_PosterousClient();
	}

	public function getAdapter() {
		return $this->m_oAdapter;
	}

}