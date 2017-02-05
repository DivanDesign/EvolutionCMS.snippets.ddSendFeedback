<?php
namespace ddSendFeedback\Sender;

abstract class Sender {
	private 
		$tpl = '',
		$tpl_placeholders = [],
		$tpl_placeholdersFromPost;
	
	protected
		$text = '';
	
	/**
	 * __construct
	 * @version 1.0 (2017-01-25)
	 */
	public function __construct($params = []){
		global $modx;
		
		//Все параметры задают свойства объекта
		foreach ($params as $paramName => $paramValue){
			//На всякий случай проверяем
			if (isset($this->{$paramName})){
				$this->{$paramName} = $paramValue;
			}
		}
		
		//If POST-placeholders is not initialized
		if (!is_array($this->tpl_placeholdersFromPost)){
			$this->initPostPlaceholders();
		}
		
		//Prepare text to send
		$this->text = \ddTools::parseSource(\ddTools::parseText([
			'text' => $modx->getTpl($this->tpl),
			'data' => array_merge($this->tpl_placeholdersFromPost, $this->tpl_placeholders),
			'removeEmptyPlaceholders' => true
		]));
	}
	
	/**
	 * includeSenderByName
	 * @version 1.0 (2017-01-25)
	 * 
	 * @param $senderName {string} — Sender name.
	 * 
	 * @return {string}
	 * @throws \Exception
	 */
	public final static function includeSenderByName($senderName){
		$senderName = ucfirst(strtolower($senderName));
		$senderPath = $senderName.DIRECTORY_SEPARATOR.'Sender.php';
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR.$senderPath)){
			require_once($senderPath);
			return __NAMESPACE__.'\\'.$senderName.'\\'.'Sender';
		}else{
			throw new \Exception('Sender '.$senderName.' not found.', 500);
		}
	}
	
	/**
	 * initPostPlaceholders
	 * @version 1.0 (2017-01-25)
	 * 
	 * @desc Init placeholders to $this->tpl_placeholdersFromPost from $_POST.
	 * 
	 * @return {void}
	 */
	private final function initPostPlaceholders(){
		//Подготавливаем плэйсхолдеры
		$this->tpl_placeholdersFromPost = [];
		
		//Перебираем пост, записываем в массив значения полей
		foreach ($_POST as $key => $val){
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