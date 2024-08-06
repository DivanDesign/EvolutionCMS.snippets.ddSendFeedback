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
			'chatId',
		],
		
		/**
		 * @property $requestResultParams {stdClass}
		 * @property $requestResultParams->isObject {boolean} — Is the result of the request an object or not? It is needed to check if the request is successful. If `false`, the response will be checked as a boolean. It is computed automatically from the siblings values.
		 */
		$requestResultParams = [
			'checkValue' => true,
			'isCheckTypeSuccess' => true,
			'checkPropName' => 'ok',
			'errorMessagePropName' => 'description',
			
			'isObject' => true,
		]
	;
	
	private
		$url = 'https://api.telegram.org/bot[+botToken+]/sendMessage?chat_id=[+chatId+]&text=[+text+]&parse_mode=[+textMarkupSyntax+]&disable_web_page_preview=[+disableWebPagePreview+]'
	;
	
	/**
	 * __construct
	 * @version 1.0.4 (2024-08-06)
	 */
	public function __construct($params = []){
		// Backward compatibility
		$params = \ddTools::verifyRenamedParams([
			'params' => $params,
			'compliance' => [
				'textMarkupSyntax' => 'messageMarkupSyntax',
			],
			'returnCorrectedOnly' => false,
		]);
		
		// Call base constructor
		parent::__construct($params);
		
		// Prepare “textMarkupSyntax”
		if (!in_array(
			$this->textMarkupSyntax,
			// Allowable values
			[
				'markdown',
				'html',
				'',
			]
		)){
			$this->textMarkupSyntax = '';
		}
		
		// Prepare “disableWebPagePreview”
		$this->disableWebPagePreview = boolval($this->disableWebPagePreview);
	}
	
	/**
	 * send_request_prepareParams
	 * @version 1.0.3 (2024-08-06)
	 * 
	 * @return $result {\stdClass}
	 */
	protected function send_request_prepareParams(): \stdClass {
		return (object) [
			'url' => \ddTools::parseText([
				'text' => $this->url,
				'data' => [
					'botToken' => $this->botToken,
					'chatId' => $this->chatId,
					'text' => urlencode($this->text),
					'textMarkupSyntax' => $this->textMarkupSyntax,
					'disableWebPagePreview' => intval($this->disableWebPagePreview),
				],
				// TODO: Why is it disabled? Add a comment or enable.
				'isCompletelyParsingEnabled' => false,
			]),
			'proxy' => $this->proxy,
		];
	}
}
?>