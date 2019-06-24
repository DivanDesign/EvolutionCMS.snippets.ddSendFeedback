<?php
namespace ddSendFeedback\Sender\Customhttprequest;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$url,
		$method,
		$headers,
		$userAgent,
		$timeout,
		$proxy
	;
	
	private 
		$postData
	;
	
	/**
	 * send
	 * @version 1.0.2 (2019-06-22)
	 * 
	 * @desc Send message to Slack.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		$result = [0 => 0];
		
		//If method == 'get' need to append url. Else need to set postData
		if (
			$method == 'get'
		){
			$this->url = 
				$this->url . 
				'?' . 
				$this->text
			;
		}else{
			$this->postData = $this->text;
		}
		
		$requestResult = \ddTools::$modx->runSnippet(
			'ddMakeHttpRequest',
			[
				'url' => $this->url,
				'method' => $this->method,
				'postData' => $this->postData,
				'headers' => $this->headers,
				'userAgent' => $this->userAgent,
				'timeout' => $this->timeout,
				'proxy' => $this->proxy
			]
		);
		
		//TODO: Improve it
		$result[0] = boolval($requestResult);
		
		return $result;
	}
}
?>