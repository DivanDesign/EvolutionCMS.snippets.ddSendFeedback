<?php
namespace ddSendFeedback\Sender\Slack;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$url = '',
		$channel = '',
		$botName = 'ddSendFeedback',
		$botIcon = ':ghost:';
	
	/**
	 * send
	 * @version 1.0 (2017-01-16)
	 * 
	 * @uses ddMakeHttpRequest >= 1.3.
	 * 
	 * @desc Send message to Slack.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */

	public function send(){
		global $modx;
		$result = [0 => 0];
		
		$requestResult = $modx->runSnippet('ddMakeHttpRequest', [
			'url' => $this->url,
			'method' => 'post',
			'postData' => json_encode([
				'text' => $this->text,
				'channel' => $this->channel,
				'username' => $this->botName,
				'icon_emoji' => $this->botIcon
			]),
			'headers' => 'application/json'
		]);
		
		if ($requestResult == 'ok'){
			$result[0] = 1;
		}
		
		return $result;
	}
}
?>