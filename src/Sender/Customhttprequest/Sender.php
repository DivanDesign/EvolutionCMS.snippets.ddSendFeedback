<?php
namespace ddSendFeedback\Sender\Customhttprequest;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$url,
		$method,
		$postData,
		$headers,
		$userAgent,
		$timeout,
		$proxy
	;
	
	/**
	 * send
	 * @version 1.0 (2019-06-22)
	 * 
	 * @desc Send message to Slack.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		global $modx;
		
		$result = [0 => 0];
		
		$requestResult = $modx->runSnippet(
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
		
		$result[0] = 1;
		
		return $result;
	}
}
?>