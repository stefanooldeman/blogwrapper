<?php

class App_Post_Post {

	private $m_iId;
	private $m_sDate;
	private	$m_sTitle;
	private $m_sBody;

	const OUTPUT_MARKDOWN	= 'markdown';
	const OUTPUT_TEXTILE	= 'textile';
	const OUTPUT_RAW		= 'raw';

	private $m_sOutputType;

	public function __construct($p_sMarkUpType) {

		switch($p_sMarkUpType) {
			case self::OUTPUT_MARKDOWN:
			case self::OUTPUT_TEXTILE:
			case self::OUTPUT_RAW:
				$this->setOutputType($p_sMarkUpType);
			break;

			default:
				throw new ErrorException('check your config. invalid option on "post.markup" (unsupported markup type was set)');
		}
	}

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

	public function formatBody() {
		$l_sBody = $this->getBody();
		switch($this->getOutputType()) {
			case self::OUTPUT_MARKDOWN:
				$l_sBody = htmlspecialchars_decode(strip_tags($l_sBody));
				$l_sBody = Markdown($l_sBody);
				break;

			case self::OUTPUT_TEXTILE:
				throw new ErrorException('This type of output markup is not implemented yet');
				break;

			case self::OUTPUT_RAW:
				//do nothing....
		}
		$this->setBody($l_sBody);
	}

	public function setOutputType($p_sFlag) {
		$this->m_sOutputType = $p_sFlag;
	}
	public function getOutputType() {
		return $this->m_sOutputType;
	}

}