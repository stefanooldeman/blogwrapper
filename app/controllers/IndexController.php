<?php
class IndexController extends Zend_Controller_Action {

	public function indexAction() {
		$l_oPostReader = new App_Post_Reader();
		$l_aPosts = $l_oPostReader->fetchOverview();
		krsort($l_aPosts);

		$this->view->posts = $l_aPosts;
	}
}

