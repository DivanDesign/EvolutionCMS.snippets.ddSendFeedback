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
	 * @version 1.1.4 (2020-03-16)
	 * 
	 * @desc Send sms via sms.ru.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		$result = [];
		
		if ($this->canSend){
			$result = [0 => 0];
			
			$url =
				$this->url .
				'&api_id=' . $this->apiId .
				'&to=' . $this->to .
				'&msg=' . urlencode($this->text)
			;
			
			if(isset($this->from)){
				$url .= '&from=' . $this->from;
			}
			
			//отсылаем смс
			$requestResult = \ddTools::$modx->runSnippet(
				'ddMakeHttpRequest',
				[
					'url' => $url,
				]
			);
			
			//разбиваем пришедшее сообщение
			$requestResult = json_decode(
				$requestResult,
				true
			);
			
			if ($requestResult['sms'][$this->to]['status'] == 'OK'){
				$result[0] = 1;
			}else{
				//Если ошибка, то залогируем
				\ddTools::logEvent([
					'message' =>
						'<code><pre>' .
						print_r(
							$requestResult,
							true
						) .
						'</pre></code>'
					,
					'source' => 'ddSendFeedback → Smsru',
					'eventType' => 'error'
				]);
			}
		}
		
		return $result;
	}
}
?>