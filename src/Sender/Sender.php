<?php
namespace ddSendFeedback\Sender;

abstract class Sender extends \DDTools\BaseClass {
	private 
		$tpl = NULL,
		$tpl_placeholders = [],
		$tpl_placeholdersFromPost = false
	;
	
	protected
		$text = '',
		$canSend = true
	;
	
	/**
	 * __construct
	 * @version 1.1 (2019-12-14)
	 */
	public function __construct($params = []){
		$this->setExistingProps($params);
		
		//Check required props
		foreach (
			$this as
			$propValue
		){
			//If one of required properties is not set
			if ($propValue === NULL){
				//We can't send
				$this->canSend = false;
				
				break;
			}
		}
		
		//If all required properties are set
		if ($this->canSend){
			//If POST-placeholders is not initialized
			if ($this->tpl_placeholdersFromPost === false){
				$this->initPostPlaceholders();
			}
			
			//Prepare text to send
			$this->text = trim(\ddTools::parseSource(\ddTools::parseText([
				'text' => \ddTools::$modx->getTpl($this->tpl),
				'data' => array_merge(
					$this->tpl_placeholdersFromPost,
					$this->tpl_placeholders
				),
				'removeEmptyPlaceholders' => true
			])));
			
			//Text must not be empty for sending
			if (empty($this->text)){
				$this->canSend = false;
			}
		}
	}
	
	/**
	 * initPostPlaceholders
	 * @version 1.1.2 (2019-06-24)
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
				$this->tpl_placeholdersFromPost[$key] = nl2br($_POST[$key]);
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
}
?>