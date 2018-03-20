<?php
namespace ddSendFeedback\Sender\Telegram;

class Sender extends \ddSendFeedback\Sender\Sender {
	/**
	 * @property $botToken {string} — Токен бота в вида “botId:HASH”.
	 * @property $chatId {string_numeric} — ID чата, в который слать сообщение.
	 */
	protected
		$botToken = '',
		$chatId = '';
	
	private
		$url = 'https://api.telegram.org/bot[+botToken+]/sendMessage?chat_id=[+chatId+]&text=[+text+]';
	
	/**
	 * send
	 * @version 1.0 (2018-03-20)
	 * 
	 * @desc Send messege to a Telegram channel.
	 * 
	 * @uses MODXEvo.snippets.ddMakeHttpRequest >= 1.3 {@link http://code.divandesign.biz/modx/ddmakehttprequest }
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	
	public function send(){
		global $modx;
		
		$result = [0 => 0];
		
		//Заполнены ли обязательные параметры
		if(
			//Передали ли botToken
			!empty($this->botToken) &&
			//Чат, в который отправлять сообщение
			!empty($this->chatId) &&
			//И сообщение
			isset($this->text)
		){
			//Отсылаем сообщение
			$requestResult = $modx->runSnippet('ddMakeHttpRequest', [
				'url' => \ddTools::parseText([
					'text' => $this->url,
					'data' => [
						'botToken' => $this->botToken,
						'chatId' => $this->chatId,
						'text' => urlencode($this->text)
					],
					'mergeAll' => false
				])
			]);
			
			//Разбиваем пришедшее сообщение
			$requestResult = json_decode(
				$requestResult,
				true
			);
			
			//Everything is ok
			if (
				is_array($requestResult) &&
				isset($requestResult['ok']) &&
				$requestResult['ok'] == true
			){
				$result[0] = 1;
			}else{
				//Если ошибка, то залогируем
				\ddTools::logEvent([
					'message' => '<code><pre>'.print_r($requestResult, true).'</pre></code>',
					'source' => 'ddSendFeedback → Telegram',
					'eventType' => 'error'
				]);
			}
		}
		
		return $result;
	}
}
?>