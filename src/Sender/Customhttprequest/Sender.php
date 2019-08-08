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
	
	/**
	 * send
	 * @version 1.2.2 (2019-08-08)
	 * 
	 * @desc Send message to Slack.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		$result = [0 => 0];
		
		$requestParams =
			[
				'url' => $this->url,
				'method' => $this->method,
				'headers' => $this->headers,
				'userAgent' => $this->userAgent,
				'timeout' => $this->timeout,
				'proxy' => $this->proxy
			]
		;
		
		//If method == 'get' need to append url. Else need to set postData
		if ($method == 'get'){
			$requestParams['url'] .=
				'?' . 
				$this->text
			;
		}else{
			$requestParams['postData'] = $this->text;
		}
		
		$requestResult = \ddTools::$modx->runSnippet(
			'ddMakeHttpRequest',
			$requestParams
		);
		
		//TODO: Improve it
		$result[0] = boolval($requestResult);
		
		return $result;
	}
}
?>