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
			//Just something, a real values will be set in every call of `$this->send`
			'checkPropName' => 'sms.[+phoneNumber+].status',
			'errorMessagePropName' => 'sms.[+phoneNumber+].status_text',
			
			'isObject' => true,
		]
	;
	
	private
		$url = 'https://sms.ru/sms/send?json=1'
	;
	
	/**
	 * send
	 * @version 1.3.3 (2024-06-17)
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
		
		$requestResult = null;
		
		if ($this->canSend){
			$errorData->title = 'Unexpected API error';
			
			$this->requestResultParams->checkPropName = 'sms.' . $this->to . '.status';
			$this->requestResultParams->errorMessagePropName = 'sms.' . $this->to . '.status_text';
			
			$requestResult = $this->send_parseRequestResult(
				$this->send_request()
			);
			
			$errorData->isError = $requestResult->isError;
		}
		
		//Log errors
		if ($errorData->isError){
			if (!is_null($requestResult)){
				if (!\ddTools::isEmpty($this->requestResultParams->errorMessagePropName)){
					//Try to get error title from request result
					$errorData->title = \DDTools\ObjectTools::getPropValue([
						'object' => $requestResult->data,
						'propName' => $this->requestResultParams->errorMessagePropName,
						'notFoundResult' => $errorData->title,
					]);
				}
				
				$errorData->message =
					'<p>Request result:</p><pre><code>'
						. var_export(
							$requestResult->data,
							true
						)
					. '</code></pre>'
				;
			}
			
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
				'source' =>
					'ddSendFeedback → '
					. static::getClassName()->namespaceShort
					. ': '
					. $errorData->title
				,
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