<?php
namespace ddSendFeedback\Sender\Telegram;

class Sender extends \ddSendFeedback\Sender\Sender {
	/**
	 * @property $botToken {string} — Токен бота в вида 'botId:HASH'. @required
	 * @property $chatId {string_numeric} — ID чата, в который слать сообщение. @required
	 * @property $textMarkupSyntax {'markdown'|'html'|''} — Синтаксис, в котором написано сообщение. Default: ''.
	 * @property $disableWebPagePreview {boolean} — Disables link previews for links in this message. Default: false.
	 * @property $proxy {string} — Proxy server in format 'protocol://user:password@ip:port'. E. g. 'asan:gd324ukl@11.22.33.44:5555' or 'socks5://asan:gd324ukl@11.22.33.44:5555'. Default: —.
	 */
	protected
		$botToken = '',
		$chatId = '',
		$disableWebPagePreview = false,
		$proxy = '',
		$textMarkupSyntax = '',
		
		$requiredProps = [
			'botToken',
			'chatId'
		]
	;
	
	private
		$url = 'https://api.telegram.org/bot[+botToken+]/sendMessage?chat_id=[+chatId+]&text=[+text+]&parse_mode=[+textMarkupSyntax+]&disable_web_page_preview=[+disableWebPagePreview+]'
	;
	
	/**
	 * __construct
	 * @version 1.0.2 (2021-01-18)
	 */
	public function __construct($params = []){
		//Backward compatibility
		$params = \ddTools::verifyRenamedParams([
			'params' => $params,
			'compliance' => [
				'textMarkupSyntax' => 'messageMarkupSyntax'
			],
			'returnCorrectedOnly' => false
		]);
		
		//Call base constructor
		parent::__construct($params);
		
		//Prepare “textMarkupSyntax”
		if (!in_array(
			$this->textMarkupSyntax,
			//Allowable values
			[
				'markdown',
				'html',
				''
			]
		)){
			$this->textMarkupSyntax = '';
		}
		
		//Prepare “disableWebPagePreview”
		$this->disableWebPagePreview = boolval($this->disableWebPagePreview);
	}
	
	/**
	 * send
	 * @version 1.4.1 (2024-06-10)
	 * 
	 * @desc Send messege to a Telegram chat.
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
				'url' => \ddTools::parseText([
					'text' => $this->url,
					'data' => [
						'botToken' => $this->botToken,
						'chatId' => $this->chatId,
						'text' => urlencode($this->text),
						'textMarkupSyntax' => $this->textMarkupSyntax,
						'disableWebPagePreview' => intval($this->disableWebPagePreview)
					],
					//TODO: Why is it disabled? Add a comment or enable.
					'isCompletelyParsingEnabled' => false
				]),
				'proxy' => $this->proxy
			];
			
			//Отсылаем сообщение
			$requestResult = \DDTools\Snippet::runSnippet([
				'name' => 'ddMakeHttpRequest',
				'params' => $sendParams,
			]);
			
			$requestResult = \DDTools\ObjectTools::convertType([
				'object' => $requestResult,
				'type' => 'objectStdClass',
			]);
			
			$errorData->isError =
				\DDTools\ObjectTools::getPropValue([
					'object' => $requestResult,
					'propName' => 'ok',
				])
				!= true
			;
			
			if ($errorData->isError){
				//Try to get error title from LiveSklad API
				$errorData->title = \DDTools\ObjectTools::getPropValue([
					'object' => $requestResult,
					'propName' => 'description',
					'notFoundResult' => $errorData->title,
				]);
				
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
				'source' => 'ddSendFeedback → Telegram: ' . $errorData->title,
				'eventType' => 'error',
			]);
		}
		
		return [
			0 => !$errorData->isError
		];
	}
}
?>