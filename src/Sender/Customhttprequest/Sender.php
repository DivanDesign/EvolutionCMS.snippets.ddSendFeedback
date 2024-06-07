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
	 * @version 1.3.3 (2024-06-07)
	 * 
	 * @desc Send message to Slack.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		$result = [];
		
		if ($this->canSend){
			$result[0] = 0;
			
			$sendParams =
				[
					'url' => $this->url
				]
			;
			
			//If method == 'get' need to append url. Else need to set postData
			if ($this->method == 'get'){
				$sendParams['url'] .=
					'?' . 
					$this->text
				;
			}else{
				$sendParams['postData'] = $this->text;
				$sendParams['sendRawPostData'] = $this->sendRawPostData;
			}
			
			if (!empty($this->headers)){
				$sendParams['headers'] = $this->headers;
			}
			
			if (!empty($this->userAgent)){
				$sendParams['userAgent'] = $this->userAgent;
			}
			
			if (!empty($this->timeout)){
				$sendParams['timeout'] = $this->timeout;
			}
			
			if (!empty($this->proxy)){
				$sendParams['proxy'] = $this->proxy;
			}
			
			$requestResult = \DDTools\Snippet::runSnippet([
				'name' => 'ddMakeHttpRequest',
				'params' => $sendParams,
			]);
			
			//TODO: Improve it
			$result[0] = boolval($requestResult);
		}
		
		return $result;
	}
}
?>