<?php
namespace ddSendFeedback\Sender\Slack;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$url = '',
		$channel = '',
		$botName = 'ddSendFeedback',
		$botIcon = ':ghost:',
		
		$requiredProps = ['url']
	;
	
	/**
	 * send
	 * @version 1.0.7 (2024-06-07)
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
			
			$sendParams = [
				'url' => $this->url,
				'method' => 'post',
				'postData' => json_encode([
					'text' => $this->text,
					'channel' => $this->channel,
					'username' => $this->botName,
					'icon_emoji' => $this->botIcon
				]),
				'sendRawPostData' => true,
				'headers' => 'application/json'
			];
			
			$requestResult = \DDTools\Snippet::runSnippet([
				'name' => 'ddMakeHttpRequest',
				'params' => $sendParams,
			]);
			
			if ($requestResult == 'ok'){
				$result[0] = 1;
			}
		}
		
		return $result;
	}
}
?>