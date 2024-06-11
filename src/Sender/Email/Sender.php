<?php
namespace ddSendFeedback\Sender\Email;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$to = [],
		$from = '',
		$subject = '',
		$fileInputNames = [],
		
		$requiredProps = ['to']
	;
	
	/**
	 * __construct
	 * @version 1.0.1 (2019-12-14)
	 */
	public function __construct($params = []){
		//Call base constructor
		parent::__construct($params);
		
		//Comma separated string support
		if (is_string($this->to)){
			$this->to = explode(
				',',
				$this->to
			);
		}
	}
	
	/**
	 * send
	 * @version 1.1.5 (2024-06-11)
	 * 
	 * @desc Send emails.
	 * 
	 * @return $result {array} — Returns the array of email statuses.
	 * @return $result[i] {0|1} — Status.
	 */
	public function send(){
		$errorData = (object) [
			'isError' => true,
			//Only 19 signs are allowed here in MODX event log :|
			'title' => 'Check required parameters',
			'message' => '',
		];
		
		$requestResult = [];
		
		if ($this->canSend){
			$errorData->title = 'Sending error';
			
			$requestResult = $this->send_parseRequestResult(
				$this->send_request()
			);
			
			$errorData->isError = $requestResult->isError;
			
			if ($errorData->isError){
				$errorData->message =
					'<p>Request result:</p><pre><code>'
						. var_export(
							$requestResult->data,
							true
						)
					. '</code></pre>'
				;
			}
		}
		
		//Log errors
		if ($errorData->isError){
			$errorData->message .=
				'<p>$this:</p><pre><code>'
					. var_export(
						$this,
						true
					)
				. '</code></pre>'
			;
			
			\ddTools::logEvent([
				'message' => $errorData->message,
				'source' => 'ddSendFeedback → Email: ' . $errorData->title,
				'eventType' => 'error',
			]);
		}
		
		return $requestResult->data;
	}
	
	/**
	 * send_request
	 * @version 1.0 (2024-06-11)
	 * 
	 * @return $result {array}
	 */
	protected function send_request(){
		return \ddTools::sendMail(
			$this->send_prepareRequestParams()
		);
	}
	
	/**
	 * send_prepareRequestParams
	 * @version 1.0 (2024-06-10)
	 *
	 * @return $result {\stdClass}
	 */
	protected function send_prepareRequestParams(): \stdClass {
		$result = (object) [
			'to' => $this->to,
			'text' => $this->text,
			'subject' => $this->subject,
		];
		
		if(!empty($this->fileInputNames)){
			$result->fileInputNames = explode(
				',',
				$this->fileInputNames
			);
		}
		
		if (!empty($this->from)){
			$result->from = $this->from;
		}
		
		return $result;
	}
	
	/**
	 * send_parseRequestResult
	 * @version 1.0 (2024-06-11)
	 * 
	 * @return $result {\stdClass}
	 * @return $result->data {mixed}
	 * @return $result->isError {boolean}
	 */
	protected function send_parseRequestResult($rawData): \stdClass {
		$result = (object) [
			'data' => $rawData,
			'isError' => true,
		];
		
		$result->isError = in_array(
			0,
			$result->data
		);
		
		return $result;
	}
}
?>