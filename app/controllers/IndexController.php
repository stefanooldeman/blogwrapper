<?php
class IndexController extends Zend_Controller_Action {

	public function indexAction() {
		$l_oPostReader = new App_Post_Reader();
		$l_aPosts = $l_oPostReader->fetchOverview();
		if($l_aPosts !== false) {
			krsort($l_aPosts);
			$this->view->posts = $l_aPosts;
		} else {
			//@todo throw an error mesage to the view!
		}
	}

	public function checkinsAction() {
		$l_oFoursqaure = new App_Service_FoursquareClient();
		$this->view->checkins = $l_oFoursqaure->getRecentCheckins(5);
	}
}

