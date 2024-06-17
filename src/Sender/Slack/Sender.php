<?php
namespace ddSendFeedback\Sender\Slack;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$url = '',
		$channel = '',
		$botName = 'ddSendFeedback',
		$botIcon = ':ghost:',
		
		$requiredProps = ['url'],
		
		$requestResultParams = [
			'checkValue' => 'ok',
			'isCheckTypeSuccess' => true,
			'checkPropName' => null,
			'errorMessagePropName' => null,
			
			'isObject' => false,
		]
	;
	
	/**
	 * send_request_prepareParams
	 * @version 1.0.1 (2024-06-11)
	 * 
	 * @return $result {\stdClass}
	 */
	protected function send_request_prepareParams(): \stdClass {
		return (object) [
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
	}
}
?>