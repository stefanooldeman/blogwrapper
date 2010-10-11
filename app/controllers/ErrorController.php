<?php

class ErrorController extends Zend_Controller_Action {

	const LOG_SILENT = 1;
	const LOG_VERBOSE = 2;
	
	public function init() {
		if($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->viewRenderer->setNoRender();
			$this->_helper->layout->disableLayout();
		}
	}

    public function errorAction() {

		if($this->getResponse()->isException() == true) {
			list($l_oException) = $this->getResponse()->getException();
			
			if($this->getRequest()->isXmlHttpRequest()) {
				$this->logException(self::LOG_SILENT, $l_oException);
			} else {
				$this->logException(self::LOG_VERBOSE, $l_oException);
			}
		}
		if($this->getRequest()->isXmlHttpRequest()) {
			$l_oAjax = new App_Controller_Ajax();
			$l_oAjax->setError(App_Controller_Ajax::ERROR);
			//to String and DIE ! !!! !
			exit((string) $l_oAjax);
		}

		$errors = $this->_getParam('error_handler');

		$this->view->applicationEnv = APPLICATION_ENV;
		$this->view->exception = array(
			'message' => $errors->exception->getMessage(),
			'trace' => $errors->exception->getTraceAsString()
		);

		$this->view->request = print_r($errors->request->getParams(), true);

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->message = 'Page not found';
				break;

			default:
				// application error
				$this->getResponse()->setHttpResponseCode(500);
				$this->view->message = 'Application error';
				break;
		}
		
    }


	private function logException($p_iModeFlag, Exception $p_oException) {
		
		error_log($p_oException);
		static $l_oLogger;

		if(isset($l_oLogger) == false) {
			$l_oLogger = new Zend_Log();
		}

		switch($p_iModeFlag) {
			
			case self::LOG_SILENT:
				//FIXME it doesnt push to the log.. (console)
				if(DEBUG) { //#YAY! Log to firebug
					$l_oZendFireBugWriter = new Zend_Log_Writer_Firebug();
					$l_oLogger->addWriter($l_oZendFireBugWriter);
					$l_oLogger->log($p_oException, Zend_Log::INFO);
				}
				
				break;
			
			case self::LOG_VERBOSE:
				$l_oDefaultWriter = new Zend_Log_Writer_Stream('php://output');
				$l_oLogger->addWriter($l_oDefaultWriter);
				break;

			default:
				trigger_error('You forgot to call the flag for our switch case break..');
		}
		
		$l_sExceptionLogFile = Zend_Registry::get('config')->log->dir . 'Exceptions.log';
		$stream = fopen($l_sExceptionLogFile, 'a', false);

		if ($stream == false) {
			throw new ErrorException('Failed to open stream');
		}
		$l_oZendStreamWriter = new Zend_Log_Writer_Stream($stream);
		$l_oLogger->addWriter($l_oZendStreamWriter);
		$l_oLogger->log($p_oException, Zend_Log::ERR);
	}


}

