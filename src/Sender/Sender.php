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
		$isFailDisplayedToUser = true,
		$isFailRequiredParamsDisplayedToLog = true,
		
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
	 * @version 1.7.2 (2024-08-06)
	 */
	public function __construct($params = []){
		$this->setExistingProps($params);
		
		$this->construct_prepareProps();
		
		// If all required properties are set
		if ($this->canSend){
			// If POST-placeholders is not initialized
			if (is_null($this->tpl_placeholdersFromPost)){
				$this->initPostPlaceholders();
			}
			
			// Prepare “textMarkupSyntax”
			$this->textMarkupSyntax = trim(strtolower($this->textMarkupSyntax));
			
			$text_data = \DDTools\ObjectTools::extend([
				'objects' => [
					$this->tpl_placeholdersFromPost,
					$this->tpl_placeholders,
				],
			]);
			
			if (
				is_object($this->tpl)
				|| is_array($this->tpl)
			){
				$this->tpl = (object) $this->tpl;
				
				foreach (
					$this->tpl
					as $tpl_itemKey
					=> $tpl_itemValue
				){
					$this->tpl->{$tpl_itemKey} = \ddTools::parseText([
						'text' => $tpl_itemValue,
						'data' => $text_data,
						'removeEmptyPlaceholders' => true,
					]);
				}
				
				$this->text = \DDTools\ObjectTools::convertType([
					'object' => $this->tpl,
					'type' => 'stringJsonAuto',
				]);
			}else{
				// Prepare text to send
				$this->text = \ddTools::parseText([
					'text' => \ddTools::getTpl($this->tpl),
					'data' => $text_data,
					'removeEmptyPlaceholders' => true,
				]);
				
				$this->text = trim($this->text);
			}
			
			// Text must not be empty for sending
			if (\ddTools::isEmpty($this->text)){
				$this->canSend = false;
			}
		}
	}
	
	/**
	 * construct_prepareProps
	 * @version 1.0.1 (2024-08-06)
	 * 
	 * @return {void}
	 */
	protected function construct_prepareProps(){
		$this->requestResultParams = (object) $this->requestResultParams;
		
		if (!\ddTools::isEmpty($this->requestResultParams->checkPropName)){
			$this->requestResultParams->isObject = true;
		}
		
		// $this->tpl is always required in all senders
		array_unshift(
			$this->requiredProps,
			'tpl'
		);
		
		// Check required props
		foreach (
			$this->requiredProps
			as $requiredPropName
		){
			// If one of required properties is not set
			if (\ddTools::isEmpty($this->{$requiredPropName})){
				// We can't send
				$this->canSend = false;
				
				break;
			}
		}
	}
	
	/**
	 * initPostPlaceholders
	 * @version 1.2.2 (2024-08-06)
	 * 
	 * @desc Init placeholders to $this->tpl_placeholdersFromPost from $_POST.
	 * 
	 * @return {void}
	 */
	private final function initPostPlaceholders(){
		// Подготавливаем плэйсхолдеры
		$this->tpl_placeholdersFromPost = [];
		
		// Перебираем пост, записываем в массив значения полей
		foreach (
			$_POST
			as $key
			=> $val
		){
			if(is_array($val)){
				$this->tpl_placeholdersFromPost[$key] = implode(
					',',
					$val
				);
			}
			
			if (
				// Если это строка или число (может быть массив, например, в случае с файлами)
				is_string($_POST[$key])
				|| is_numeric($_POST[$key])
			){
				$this->tpl_placeholdersFromPost[$key] =
					$this->textMarkupSyntax == 'html' ?
					nl2br($_POST[$key]) :
					$_POST[$key]
				;
			}
		}
		
		// Добавим адрес страницы, с которой пришёл запрос
		$this->tpl_placeholdersFromPost['docId'] = \ddTools::getDocumentIdByUrl($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * send
	 * @version 1.8.1 (2024-08-06)
	 * 
	 * @desc Sends a message.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return [$result[$i]] {boolean} — Status.
	 */
	public function send(){
		$errorData = (object) [
			'isError' => true,
			// Only 19 signs are allowed here in MODX event log :|
			'title' => '',
			'message' => '',
		];
		
		$requestResult = null;
		
		if (!$this->canSend){
			$errorData->title = 'Check required parameters';
			
			if (!$this->isFailRequiredParamsDisplayedToLog){
				$errorData->isError = false;
			}
		}else{
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
					],
				]);
			}
		}
		
		// Log errors
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
			// No need to return sending error if required parameters were not set
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
	 * @version 2.0.1 (2024-07-13)
	 * 
	 * @return $result {array} — Returns an array of request results (some senders can do several requests).
	 * @return $result[$i] {mixed} — A raw request result.
	 */
	protected function send_request(){
		return [
			0 => \DDTools\Snippet::runSnippet([
				'name' => 'ddMakeHttpRequest',
				'params' => $this->send_request_prepareParams(),
			]),
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
	 * @version 4.2.1 (2024-08-06)
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
				'isError' => false,
				'title' => [],
				'message' => [],
			],
		];
		
		// For each request result (some senders can do several requests)
		foreach (
			$rawResults
			as $rawResults_requestIndex
			=> $rawResults_requestResult
		){
			// Check successful status by raw result by default
			$requestResult_checkValue = $rawResults_requestResult;
			
			if ($this->requestResultParams->isObject){
				$rawResults_requestResult = \DDTools\ObjectTools::convertType([
					'object' => $rawResults_requestResult,
					'type' => 'objectStdClass',
				]);
				
				// Get required property value for checking successful status
				$requestResult_checkValue = \DDTools\ObjectTools::getPropValue([
					'object' => $rawResults_requestResult,
					'propName' => $this->requestResultParams->checkPropName,
				]);
			}
			
			// Is the request successful?
			$isRequestSuccess =
				$this->requestResultParams->isCheckTypeSuccess
				// Check if it's a success
				? $requestResult_checkValue == $this->requestResultParams->checkValue
				// Check if it's not a fail
				: $requestResult_checkValue != $this->requestResultParams->checkValue
			;
			
			if (
				$isRequestSuccess
				// Return unsuccessful status only it's need to be displayed to user
				|| $this->isFailDisplayedToUser
			){
				$result->sendSuccessStatuses[$rawResults_requestIndex] = $isRequestSuccess;
			}
			
			// If error
			if (!$isRequestSuccess){
				$result->errorData->isError = true;
				
				if (!\ddTools::isEmpty($this->requestResultParams->errorMessagePropName)){
					// Try to get error title from request result
					$result->errorData->title[$rawResults_requestIndex] = \DDTools\ObjectTools::getPropValue([
						'object' => $rawResults_requestResult,
						'propName' => $this->requestResultParams->errorMessagePropName,
					]);
				}
				
				$result->errorData->message[$rawResults_requestIndex] = $rawResults_requestResult;
			}
		}
		
		if (\ddTools::isEmpty($result->errorData->title)){
			unset($result->errorData->title);
		}else{
			$result->errorData->title = implode(
				', ',
				$result->errorData->title
			);
		}
		
		if (\ddTools::isEmpty($result->errorData->message)){
			unset($result->errorData->message);
		}else{
			$result->errorData->message =
				'<p>Request result:</p><pre><code>'
					. var_export(
						$result->errorData->message,
						true
					)
				. '</code></pre>'
			;
		}
		
		return $result;
	}
}
?>