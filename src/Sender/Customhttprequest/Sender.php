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
	 * send_request_prepareParams
	 * @version 1.0.2 (2024-07-13)
	 * 
	 * @return $result {\stdClass}
	 */
	protected function send_request_prepareParams(): \stdClass {
		$result = (object) [
			'url' => $this->url,
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