<?php
namespace ddSendFeedback\Sender\Smsru;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected 
		$apiId = '',
		$to = '',
		$from = '',
		
		$requiredProps = [
			'apiId',
			'to'
		],
		
		$requestResultParams = [
			'checkValue' => 'OK',
			'isCheckTypeSuccess' => true,
			//Just something, a real value will be set in every call of `$this->send`
			'checkPropName' => 'sms.[+phoneNumber+].status',
			
			'isObject' => true,
		]
	;
	
	private
		$url = 'https://sms.ru/sms/send?json=1'
	;
	
	/**
	 * send
	 * @version 1.2.3 (2024-06-11)
	 * 
	 * @desc Send sms via sms.ru.
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
			
			$this->requestResultParams->checkPropName = 'sms.' . $this->to . '.status';
			
			$requestResult = $this->send_parseRequestResult(
				\DDTools\Snippet::runSnippet([
					'name' => 'ddMakeHttpRequest',
					'params' => $this->send_prepareRequestParams(),
				])
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
				'source' => 'ddSendFeedback → SMSRu: ' . $errorData->title,
				'eventType' => 'error',
			]);
		}
		
		return [
			0 => !$errorData->isError
		];
	}
	
	/**
	 * send_prepareRequestParams
	 * @version 1.0 (2024-06-10)
	 * 
	 * @return $result {\stdClass}
	 */
	protected function send_prepareRequestParams(): \stdClass {
		$url =
			$this->url .
			'&api_id=' . $this->apiId .
			'&to=' . $this->to .
			'&msg=' . urlencode($this->text)
		;
		
		if(isset($this->from)){
			$url .= '&from=' . $this->from;
		}
		
		return (object) [
			'url' => $url,
		];
	}
}
?>