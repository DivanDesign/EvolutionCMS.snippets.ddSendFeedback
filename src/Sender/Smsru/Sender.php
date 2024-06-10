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
		]
	;
	
	private
		$url = 'https://sms.ru/sms/send?json=1'
	;
	
	/**
	 * send
	 * @version 1.2.2 (2024-06-10)
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
			
			//отсылаем смс
			$requestResult = \DDTools\Snippet::runSnippet([
				'name' => 'ddMakeHttpRequest',
				'params' => $this->send_prepareRequestParams(),
			]);
			
			$requestResult = \DDTools\ObjectTools::convertType([
				'object' => $requestResult,
				'type' => 'objectStdClass',
			]);
			
			$errorData->isError =
				\DDTools\ObjectTools::getPropValue([
					'object' => $requestResult,
					'propName' => 'sms.' . $this->to . '.status',
				])
				!= 'OK'
			;
			
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