<?php

class App_Post_Post {

	private $m_iId;
	private $m_sDate;
	private	$m_sTitle;
	private $m_sBody;

	public function setId($p_iId) {
		$this->m_iId = $p_iId;
	}

	public function getId() {
		return $this->m_iId;
	}

	public function setDate($p_sDate) {
		$this->m_sDate = $p_sDate;
	}

	public function getDate() {
		return $this->m_sDate;
	}

	function setTitle($p_sTitle) {
		$this->m_sTitle = $p_sTitle;
	}

	public function getTitle() {
		return $this->m_sTitle;
	}

	public function setBody($p_sBody) {
		$this->m_sBody = $p_sBody;
	}

	public function getBody() {
		return $this->m_sBody;
	}

	public function toArray() {
		return array(
			'id' => $this->getId(),
			'title' => $this->getTitle(),
			'date' => $this->getDate(),
			'body' => $this->getBody()
		);
	}
}