<?php
namespace ddSendFeedback\Sender\Slack;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected $url = '';
	protected $channel = '';
	protected $botName = 'ddSendFeedback';
	protected $botIcon = ':ghost:';
	protected $requiredProps = ['url'];
	protected $requestResultParams = [
		'checkValue' => 'ok',
		'isCheckTypeSuccess' => true,
		'checkPropName' => null,
		'errorMessagePropName' => null,
		
		'isObject' => false,
	];
	
	/**
	 * send_request_prepareParams
	 * @version 1.0.2 (2024-07-13)
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
				'icon_emoji' => $this->botIcon,
			]),
			'sendRawPostData' => true,
			'headers' => 'application/json',
		];
	}
}
?>