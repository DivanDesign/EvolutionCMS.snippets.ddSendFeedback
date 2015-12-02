<?php
/**
 * ddSendFeedback.php
 * @version 1.9 (2014-07-13)
 * 
 * @desc A snippet for sending users' feedback messages to a required email. It is very useful along with ajax technology.
 * 
 * @uses The library MODX.ddTools 0.13.
 * 
 * @param $email {comma separated string} - Mailing addresses (to whom). @required
 * @param $docField {string} - Field name/TV containing the address to mail. Default: —.
 * @param $docId {integer} - ID of a document with the required field contents. Default: —.
 * @param $tpl {string: chunkName} - The template of a letter (chunk name). Available placeholders: [+docId+] — the id of a document that the request has been sent from; the array components of $_POST. Use [(site_url)][~[+docId+]~] to generate the url of a document ([(site_url)] is required because of need for using the absolute links in the emails). @required
 * @param $text {string} - Message text. The template parameter will be ignored if the text is defined. It is useful when $modx->runSnippets() uses. Default: ''.
 * @param $subject {string} - Message subject. Default: 'Feedback'.
 * @param $from {string} - Mailer address (from who). Default: 'info@divandesign.biz'.
 * @param $fromField {string} - An element of $_POST containing mailer address. The “from” parameter will be ignored if “fromField” is defined and is not empty. Default: ''.
 * @param $titleTrue {string} - The title that will be returned if the letter sending is successful (the «title» field of the returned JSON). Default: 'Message sent successfully'.
 * @param $titleFalse {string} - The title that will be returned if the letter sending is failed somehow (the «title» field of the returned JSON). Default: 'Unexpected error =('.
 * @param $msgTrue {string} - The message that will be returned if the letter sending is successful (the «message» field of the returned JSON). Default: 'We will contact you later.'.
 * @param $msgFalse {string} - The message that will be returned if the letter sending is failed somehow (the «message» field of the returned JSON). Default: 'Something happened while sending the message.<br />Please try again later.'.
 * @param $filesFields {comma separated string} - Input tags names separated by commas that files are required to be taken from. Used if files are sending in the request ($_FILES array). Default: ''.
 * 
 * @link http://code.divandesign.biz/modx/ddsendfeedback/1.9
 * 
 * @copyright 2014, DivanDesign
 * http://www.DivanDesign.biz
 */

//Подключаем modx.ddTools
require_once $modx->getConfig('base_path').'assets/snippets/ddTools/modx.ddtools.class.php';

//Для обратной совместимости
extract(ddTools::verifyRenamedParams($params, array(
	'docField' => 'getEmail',
	'docId' => 'getId'
)));

//Если задано имя поля почты, которое необходимо получить
if (isset($docField)){
	$email = ddTools::getTemplateVarOutput(array($docField), $docId);
	$email = $email[$docField];
}

//Если всё хорошо
if ((isset($tpl) || isset($text)) && isset($email) && ($email != '')){
	//Получаем язык админки
	$lang = $modx->getConfig('manager_language');
	
	//Если язык русский
	if($lang == 'russian-UTF8' || $lang == 'russian'){
		$titleTrue = isset($titleTrue) ? $titleTrue : 'Заявка успешно отправлена';
		$titleFalse = isset($titleFalse) ? $titleFalse : 'Непредвиденная ошибка =(';
		$msgTrue = isset($msgTrue) ? $msgTrue : 'Наш специалист свяжется с вами в ближайшее время.';
		$msgFalse = isset($msgFalse) ? $msgFalse : 'Во время отправки заявки что-то произошло.<br />Пожалуйста, попробуйте чуть позже.';
		$subject = isset($subject) ? $subject : 'Обратная связь';
	}else{
		$titleTrue = isset($titleTrue) ? $titleTrue : 'Message sent successfully';
		$titleFalse = isset($titleFalse) ? $titleFalse : 'Unexpected error =(';
		$msgTrue = isset($msgTrue) ? $msgTrue : 'We will contact you later.';
		$msgFalse = isset($msgFalse) ? $msgFalse : 'Something happened while sending the message.<br />Please try again later.';
		$subject = isset($subject) ? $subject : 'Feedback';
	}
	
	$titles = array($titleFalse, $titleTrue);
	$message = array($msgFalse, $msgTrue);
	
	$from = isset($from) ? $from : 'info@divandesign.biz';
	
	//Проверяем нужно ли брать имя отправителя из поста
	if (isset($fromField) && $_POST[$fromField] != ''){
		$from = $_POST[$fromField];
	}
	
	//Проверяем передан ли текст сообщения
	if (!isset($text)){
		$param = array();
		
		//Перебираем пост, записываем в массив значения полей
		foreach ($_POST as $key => $val){
			//Если это строка или число (может быть массив, например, в случае с файлами)
			if (is_string($_POST[$key]) || is_numeric($_POST[$key])){
				$param[$key] = nl2br($_POST[$key]);
			}
		}
		
		//Добавим адрес страницы, с которой пришёл запрос
		$param['docId'] = ddTools::getDocumentIdByUrl($_SERVER['HTTP_REFERER']);
		$text = ddTools::parseSource($modx->parseChunk($tpl, $param, '[+','+]'));
	}
	
	//Отправляем письмо
	$sendMailRes = ddTools::sendMail(explode(',', $email), $text, $from, $subject, explode(',', $filesFields));
	
	$res = 0;
	
	//Перебираем все статусы отправки
	foreach ($sendMailRes as $res_elem){
		//Запоминаем
		$res = $res_elem;
		
		//Если не отправлось хоть на один адрес, считаем, что всё плохо
		if ($res == 0){
			break;
		}
	}
	
	return json_encode(array(
		'status' => (bool) $res,
		'title' => $titles[$res],
		'message' => $message[$res]
	));
}
?>