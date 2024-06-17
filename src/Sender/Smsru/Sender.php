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
			//Just something, a real values will be set in every call of `$this->send_request_prepareParams`
			'checkPropName' => 'sms.[+phoneNumber+].status',
			'errorMessagePropName' => 'sms.[+phoneNumber+].status_text',
			
			'isObject' => true,
		]
	;
	
	private
		$url = 'https://sms.ru/sms/send?json=1'
	;
	
	/**
	 * send_request_prepareParams
	 * @version 1.1 (2024-06-17)
	 * 
	 * @return $result {\stdClass}
	 */
	protected function send_request_prepareParams(): \stdClass {
		$this->requestResultParams->checkPropName = 'sms.' . $this->to . '.status';
		$this->requestResultParams->errorMessagePropName = 'sms.' . $this->to . '.status_text';
		
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