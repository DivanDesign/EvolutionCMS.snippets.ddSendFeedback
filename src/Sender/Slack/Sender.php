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
			
			'isObject' => false,
		]
	;
	
	/**
	 * send
	 * @version 1.1.4 (2024-06-11)
	 * 
	 * @desc Send message to Slack.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		$errorData = (object) [
			'isError' => true,
			//Only 19 signs are allowed here in MODX event log :|
			'title' => 'Check required parameters',
			'message' => '',
		];
		
		if ($this->canSend){
			$errorData->title = 'Unexpected API error';
			
			$requestResult = $this->send_parseRequestResult(
				$this->send_request()
			);
			
			$errorData->isError = $requestResult->isError;
			
			if ($errorData->isError){
				$errorData->message =
					'<p>Request result:</p><pre><code>'
						. var_export(
							$requestResult->data,
							true
						)
					. '</code></pre>'
				;
			}
		}
		
		//Log errors
		if ($errorData->isError){
			$errorData->message .=
				'<p>$this:</p><pre><code>'
					. var_export(
						$this,
						true
					)
				. '</code></pre>'
			;
			
			\ddTools::logEvent([
				'message' => $errorData->message,
				'source' => 'ddSendFeedback → Slack: ' . $errorData->title,
				'eventType' => 'error',
			]);
		}
		
		return [
			0 => !$errorData->isError
		];
	}
	
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