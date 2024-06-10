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
	 * @version 1.4.1 (2024-06-10)
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
			
			$sendParams = (object) [
				'url' => $this->url
			];
			
			//If method == 'get' need to append url. Else need to set postData
			if ($this->method == 'get'){
				$sendParams->url .=
					'?' . 
					$this->text
				;
			}else{
				$sendParams->postData = $this->text;
				$sendParams->sendRawPostData = $this->sendRawPostData;
			}
			
			if (!empty($this->headers)){
				$sendParams->headers = $this->headers;
			}
			
			if (!empty($this->userAgent)){
				$sendParams->userAgent = $this->userAgent;
			}
			
			if (!empty($this->timeout)){
				$sendParams->timeout = $this->timeout;
			}
			
			if (!empty($this->proxy)){
				$sendParams->proxy = $this->proxy;
			}
			
			$requestResult = \DDTools\Snippet::runSnippet([
				'name' => 'ddMakeHttpRequest',
				'params' => $sendParams,
			]);
			
			//TODO: Improve it
			$errorData->isError = boolval($requestResult);
			
			if ($errorData->isError){
				$errorData->message =
					'<p>Request result:</p><pre><code>'
						. var_export(
							$requestResult,
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
}
?>