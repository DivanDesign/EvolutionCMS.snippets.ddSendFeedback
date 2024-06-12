<?php
namespace ddSendFeedback\Sender;

abstract class Sender extends \DDTools\BaseClass {
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
	 * @version 1.0 (2017-01-25)
	 * 
	 * @return $result {array} — Send statuses.
	 * @return $result[i] {0|1} — Success or fail.
	 */
	abstract public function send();
	
	/**
	 * send_request
	 * @version 1.0.1 (2024-06-11)
	 * 
	 * @return $result {mixed}
	 */
	protected function send_request(){
		return \DDTools\Snippet::runSnippet([
			'name' => 'ddMakeHttpRequest',
			'params' => $this->send_request_prepareParams(),
		]);
	}
	
	/**
	 * send_request_prepareParams
	 * @version 1.0.1 (2024-06-11)
	 * 
	 * @return $result {\stdClass}
	 */
	abstract protected function send_request_prepareParams(): \stdClass;
	
	/**
	 * send_parseRequestResult
	 * @version 1.0 (2024-06-11)
	 * 
	 * @return $result {\stdClass}
	 * @return $result->data {mixed}
	 * @return $result->isError {boolean}
	 */
	protected function send_parseRequestResult($rawData): \stdClass {
		$result = (object) [
			'data' => $rawData,
			'isError' => true,
		];
		
		$requestResult_checkValue = $result->data;
		
		if ($this->requestResultParams->isObject){
			$result->data = \DDTools\ObjectTools::convertType([
				'object' => $result->data,
				'type' => 'objectStdClass',
			]);
			
			$requestResult_checkValue = \DDTools\ObjectTools::getPropValue([
				'object' => $result->data,
				'propName' => $this->requestResultParams->checkPropName,
			]);
		}
		
		$result->isError =
			$this->requestResultParams->isCheckTypeSuccess
			? $requestResult_checkValue != $this->requestResultParams->checkValue
			: $requestResult_checkValue == $this->requestResultParams->checkValue
		;
		
		return $result;
	}
}
?>