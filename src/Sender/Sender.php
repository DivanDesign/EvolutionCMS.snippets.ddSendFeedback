<?php
namespace ddSendFeedback\Sender;

abstract class Sender extends \DDTools\Base\Base {
	use \DDTools\Base\AncestorTrait;
	
	private
		$tpl = '',
		$tpl_placeholders = [],
		$tpl_placeholdersFromPost = NULL
	;
	
	protected
		$text = '',
		$textMarkupSyntax = 'html',
		
		$requiredProps = ['tpl'],
		$canSend = true,
		
		/**
		 * @property $requestResultParams {stdClass}
		 * @property $requestResultParams->isObject {boolean} — Is the result of the request an object or not? It is needed to check if the request is successful. If `false`, the response will be checked as a boolean. It is computed automatically from the siblings values.
		 */
		$requestResultParams = [
			'checkValue' => true,
			'isCheckTypeSuccess' => true,
			'checkPropName' => null,
			'errorMessagePropName' => null,
			
			'isObject' => false,
		]
	;
	
	/**
	 * __construct
	 * @version 1.5 (2024-06-10)
	 */
	public function __construct($params = []){
		$this->setExistingProps($params);
		
		$this->requestResultParams = (object) $this->requestResultParams;
		
		if (!\ddTools::isEmpty($this->requestResultParams->checkPropName)){
			$this->requestResultParams->isObject = true;
		}
		
		//$this->tpl is always required in all senders
		array_unshift(
			$this->requiredProps,
			'tpl'
		);
		
		//Check required props
		foreach (
			$this->requiredProps as
			$requiredPropName
		){
			//If one of required properties is not set
			if (empty($this->{$requiredPropName})){
				//We can't send
				$this->canSend = false;
				
				break;
			}
		}
		
		//If all required properties are set
		if ($this->canSend){
			//If POST-placeholders is not initialized
			if (is_null($this->tpl_placeholdersFromPost)){
				$this->initPostPlaceholders();
			}
			
			//Prepare “textMarkupSyntax”
			$this->textMarkupSyntax = trim(strtolower($this->textMarkupSyntax));
			
			//Prepare text to send
			$this->text = trim(
				\ddTools::parseText([
					'text' => \ddTools::getTpl($this->tpl),
					'data' => $params = \DDTools\ObjectTools::extend([
						'objects' => [
							$this->tpl_placeholdersFromPost,
							$this->tpl_placeholders
						]
					])
				])
			);
			
			//Text must not be empty for sending
			if (empty($this->text)){
				$this->canSend = false;
			}
		}
	}
	
	/**
	 * initPostPlaceholders
	 * @version 1.2 (2019-12-14)
	 * 
	 * @desc Init placeholders to $this->tpl_placeholdersFromPost from $_POST.
	 * 
	 * @return {void}
	 */
	private final function initPostPlaceholders(){
		//Подготавливаем плэйсхолдеры
		$this->tpl_placeholdersFromPost = [];
		
		//Перебираем пост, записываем в массив значения полей
		foreach (
			$_POST as
			$key =>
			$val
		){
			if(is_array($val)){
				$this->tpl_placeholdersFromPost[$key] = implode(
					',',
					$val
				);
			}
			
			if (
				//Если это строка или число (может быть массив, например, в случае с файлами)
				is_string($_POST[$key]) ||
				is_numeric($_POST[$key])
			){
				$this->tpl_placeholdersFromPost[$key] =
					$this->textMarkupSyntax == 'html' ?
					nl2br($_POST[$key]) :
					$_POST[$key]
				;
			}
		}
		
		//Добавим адрес страницы, с которой пришёл запрос
		$this->tpl_placeholdersFromPost['docId'] = \ddTools::getDocumentIdByUrl($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * send
	 * @version 1.7.4 (2024-06-20)
	 * 
	 * @desc Sends a message.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return [$result[$i]] {boolean} — Status.
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
			
			if (!$this->send_auth()){
				$errorData->title = 'Authorization failed';
			}else{
				$requestResult = $this->send_parseRequestResults(
					$this->send_request()
				);
				
				$errorData = \DDTools\ObjectTools::extend([
					'objects' => [
						$errorData,
						$requestResult->errorData,
					]
				]);
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
				'source' =>
					'ddSendFeedback → '
					. static::getClassName()->namespaceShort
					. ': '
					. $errorData->title
				,
				'eventType' => 'error',
			]);
		}
		
		return \DDTools\ObjectTools::getPropValue([
			'object' => $requestResult,
			'propName' => 'sendSuccessStatuses',
			//No need to return sending error if required parameters were not set
			'notFoundResult' => [],
		]);
	}
	
	/**
	 * send_auth
	 * @version 1.0 (2024-06-17)
	 * 
	 * @return {boolean}
	 */
	protected function send_auth(): bool {
		return true;
	}
	
	/**
	 * send_request
	 * @version 2.0 (2024-06-11)
	 * 
	 * @return $result {array} — Returns an array of request results (some senders can do several requests).
	 * @return $result[$i] {mixed} — A raw request result.
	 */
	protected function send_request(){
		return [
			0 => \DDTools\Snippet::runSnippet([
				'name' => 'ddMakeHttpRequest',
				'params' => $this->send_request_prepareParams(),
			])
		];
	}
	
	/**
	 * send_request_prepareParams
	 * @version 1.0.1 (2024-06-11)
	 * 
	 * @return $result {\stdClass}
	 */
	abstract protected function send_request_prepareParams(): \stdClass;
	
	/**
	 * send_parseRequestResults
	 * @version 4.0 (2024-06-20)
	 * 
	 * @param $rawResults {array} — An array of raw request results (some senders can do several requests).
	 * @param $rawResults[$i] {mixed} — A raw request result.
	 * 
	 * @return $result {\stdClass}
	 * @return $result->sendSuccessStatuses {array}
	 * @return $result->sendSuccessStatuses[$i] {boolean}
	 * @return $result->errorData {\stdClass}
	 * @return $result->errorData->isError {boolean}
	 * @return [$result->errorData->title] {string}
	 * @return [$result->errorData->message] {string}
	 */
	protected function send_parseRequestResults($rawResults): \stdClass {
		$result = (object) [
			'sendSuccessStatuses' => [],
			'errorData' => (object) [
				'isError' => true,
			],
		];
		
		$requestResult_checkValue = $rawResults[0];
		
		if ($this->requestResultParams->isObject){
			$rawResults[0] = \DDTools\ObjectTools::convertType([
				'object' => $rawResults[0],
				'type' => 'objectStdClass',
			]);
			
			$requestResult_checkValue = \DDTools\ObjectTools::getPropValue([
				'object' => $rawResults[0],
				'propName' => $this->requestResultParams->checkPropName,
			]);
		}
		
		$result->errorData->isError =
			$this->requestResultParams->isCheckTypeSuccess
			? $requestResult_checkValue != $this->requestResultParams->checkValue
			: $requestResult_checkValue == $this->requestResultParams->checkValue
		;
		
		$result->sendSuccessStatuses[0] = !$result->errorData->isError;
		
		if ($result->errorData->isError){
			if (!\ddTools::isEmpty($this->requestResultParams->errorMessagePropName)){
				//Try to get error title from request result
				$result->errorData->title = \DDTools\ObjectTools::getPropValue([
					'object' => $rawResults[0],
					'propName' => $this->requestResultParams->errorMessagePropName,
				]);
				
				if (is_null($result->errorData->title)){
					unset($result->errorData->title);
				}
			}
			
			$result->errorData->message =
				'<p>Request result:</p><pre><code>'
					. var_export(
						$rawResults,
						true
					)
				. '</code></pre>'
			;
		}
		
		return $result;
	}
}
?>