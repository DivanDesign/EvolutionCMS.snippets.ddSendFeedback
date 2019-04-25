<?php
namespace ddSendFeedback\Sender\Telegram;

class Sender extends \ddSendFeedback\Sender\Sender {
	/**
	 * @property $botToken {string} — Токен бота в вида “botId:HASH”.
	 * @property $chatId {string_numeric} — ID чата, в который слать сообщение.
	 * @property $messageMarkupSyntax {'Markdown'|'HTML'|''} — Синтаксис, в котором написано сообщение. Default: ''.
	 * @property $disableWebPagePreview {boolean} — Disables link previews for links in this message. Default: false.
	 */
	protected
		$botToken = '',
		$chatId = '',
		$messageMarkupSyntax = '',
		$disableWebPagePreview = false
	;
	
	private
		$url = 'https://api.telegram.org/bot[+botToken+]/sendMessage?chat_id=[+chatId+]&text=[+text+]&parse_mode=[+messageMarkupSyntax+]&disable_web_page_preview=[+disableWebPagePreview+]'
	;
	
	public function __construct($params = []){
		//Call base constructor
		parent::__construct($params);
		
		//Prepare “messageMarkupSyntax”
		$this->messageMarkupSyntax = trim($this->messageMarkupSyntax);
		//Allowable values
		if (!in_array(
			$this->messageMarkupSyntax,
			[
				'Markdown',
				'HTML',
				''
			]
		)){
			$this->messageMarkupSyntax = '';
		}
		
		//Prepare “disableWebPagePreview”
		$this->disableWebPagePreview = boolval($this->disableWebPagePreview);
	}
	
	/**
	 * send
	 * @version 1.1.1 (2019-04-25)
	 * 
	 * @desc Send messege to a Telegram channel.
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
			$requestResult = $modx->runSnippet(
				'ddMakeHttpRequest',
				[
					'url' => \ddTools::parseText([
						'text' => $this->url,
						'data' => [
							'botToken' => $this->botToken,
							'chatId' => $this->chatId,
							'text' => urlencode($this->text),
							'messageMarkupSyntax' => $this->messageMarkupSyntax,
							'disableWebPagePreview' => intval($this->disableWebPagePreview)
						],
						'mergeAll' => false
					])
				]
			);
			
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
					'message' => '<code><pre>' . print_r(
						$requestResult,
						true
					) . '</pre></code>',
					'source' => 'ddSendFeedback → Telegram',
					'eventType' => 'error'
				]);
			}
		}
		
		return $result;
	}
}
?>