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
	 * @version 1.1.1 (2024-06-10)
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
			
			$sendParams = (object) [
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
			
			$errorData->isError = $requestResult != 'ok';
			
			if ($errorData->isError){
				$errorData->message =
					'<p>Request result:</p><pre><code>'
						. var_export(
							$requestResult,
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
}
?>