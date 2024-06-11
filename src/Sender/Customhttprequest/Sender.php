<?php
namespace ddSendFeedback\Sender\Customhttprequest;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$url = '',
		$method = 'post',
		$sendRawPostData = false,
		$headers = '',
		$userAgent = '',
		$timeout = '',
		$proxy = '',
		
		$requiredProps = ['url']
	;
	
	/**
	 * send
	 * @version 1.5.2 (2024-06-11)
	 * 
	 * @desc Send message to custom URL.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		$errorData = (object) [
			'isError' => true,
			//Only 19 signs are allowed here in MODX event log :|
			'title' => 'Check required parameters',
			'message' => '',
		];
		
		if ($this->canSend){
			$errorData->title = 'Unexpected API error';
			
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
				'source' => 'ddSendFeedback → CRMLiveSklad: ' . $errorData->title,
				'eventType' => 'error',
			]);
		}
		
		return [
			0 => !$errorData->isError
		];
	}
	
	/**
	 * send_request_prepareParams
	 * @version 1.0.1 (2024-06-11)
	 * 
	 * @return $result {\stdClass}
	 */
	protected function send_request_prepareParams(): \stdClass {
		$result = (object) [
			'url' => $this->url
		];
		
		//If method == 'get' need to append url. Else need to set postData
		if ($this->method == 'get'){
			$result->url .=
				'?' . 
				$this->text
			;
		}else{
			$result->postData = $this->text;
			$result->sendRawPostData = $this->sendRawPostData;
		}
		
		if (!empty($this->headers)){
			$result->headers = $this->headers;
		}
		
		if (!empty($this->userAgent)){
			$result->userAgent = $this->userAgent;
		}
		
		if (!empty($this->timeout)){
			$result->timeout = $this->timeout;
		}
		
		if (!empty($this->proxy)){
			$result->proxy = $this->proxy;
		}
		
		return $result;
	}
}
?>