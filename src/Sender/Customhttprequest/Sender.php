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
	 * @version 1.3 (2021-05-12)
	 * 
	 * @desc Send message to Slack.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		$result = [];
		
		if ($this->canSend){
			$result = [0 => 0];
			
			$requestParams =
				[
					'url' => $this->url
				]
			;
			
			//If method == 'get' need to append url. Else need to set postData
			if ($this->method == 'get'){
				$requestParams['url'] .=
					'?' . 
					$this->text
				;
			}else{
				$requestParams['postData'] = $this->text;
				$requestParams['sendRawPostData'] = $this->sendRawPostData;
			}
			
			if (!empty($this->headers)){
				$requestParams['headers'] = $this->headers;
			}
			
			if (!empty($this->userAgent)){
				$requestParams['userAgent'] = $this->userAgent;
			}
			
			if (!empty($this->timeout)){
				$requestParams['timeout'] = $this->timeout;
			}
			
			if (!empty($this->proxy)){
				$requestParams['proxy'] = $this->proxy;
			}
			
			$requestResult = \ddTools::$modx->runSnippet(
				'ddMakeHttpRequest',
				$requestParams
			);
			
			//TODO: Improve it
			$result[0] = boolval($requestResult);
		}
		
		return $result;
	}
}
?>