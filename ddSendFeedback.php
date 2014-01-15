<?php
/**
 * ddSendFeedback.php
 * @version 1.6 (2012-10-30)
 * 
 * @desc Snippet for sending feedback.
 * 
 * @uses snippet ddSendMail 1.3
 * @uses snippet ddGetDocumentField 2.2 might be used if field getting is required
 * 
 * @param email {string} - Mailing address. @required
 * @param getEmail {string} - Field name with required mail address.
 * @param getId {integer} - ID of the document with required field name with mail address.
 * @param getPublished {0; 1} - Document with required mail field publication status. Default: 1.
 * @param tpl {string: chunkName} - Using template (chunk name). @required
 * @param text {string} - Message text. It`s sending makes ignoring of template using.
 * @param subject {string} - Message subject. Default: 'Обратная связь'.
 * @param from {string} - Mailer address. Default: 'info@divandesign.ru'.
 * @param fromField {string} - $_POST array element with mailer name.
 * @param titleTrue {string} - Informating message title if everything is ok. Default: 'Заявка успешно отправлена'.
 * @param titleFalse {string} - Informating message title if everything is not ok. Default: 'Непредвиденная ошибка =('.
 * @param msgTrue {string} - Informating message if everything is ok. Default: 'Наш специалист свяжется с вами в ближайшее время.'.
 * @param msgFalse {string} - Informating message if everything is not ok. Default: 'Во время отправки заявки что-то произошло.<br />Пожалуйста, попробуйте чуть позже.'.
 * @param filesFields {comma separated string} - Separated by comma input tags names from which files for sending are taken. Default: ''.
 * 
 * @link http://code.divandesign.biz/modx/ddsendfeedback/1.6
 * 
 * @copyright 2012, DivanDesign
 * http://www.DivanDesign.biz
 */

//Если задано имя поля почты, которое необходимо получить
if (isset($getEmail)){
	$email = $modx->runSnippet('ddGetDocumentField', array(
		'id' => $getId,
		'published' => $getPublished,
		'field' => $getEmail
	));
}

//Если всё хорошо
if ((isset($tpl) || isset($text)) && isset($email) && ($email != '')){
	$titles = array(); $message = array();
	
	$titles[1] = isset($titleTrue) ? $titleTrue : 'Заявка успешно отправлена';
	$titles[0] = isset($titleFalse) ? $titleFalse : 'Непредвиденная ошибка =(';
	$message[1] = isset($msgTrue) ? $msgTrue : 'Наш специалист свяжется с вами в ближайшее время.';
	$message[0] = isset($msgFalse) ? $msgFalse : 'Во время отправки заявки что-то произошло.<br />Пожалуйста, попробуйте чуть позже.';
	$from = isset($from) ? $from : 'info@divandesign.ru';
	$subject = isset($subject) ? $subject : 'Обратная связь';
	
	//Проверяем нужно ли брать имя отправителя из поста
	if (isset($fromField) && $_POST[$fromField] != '') $from = $_POST[$fromField];
	
	//Проверяем передан ли текст сообщения
	if (!isset($text)){
		$param = array();
		//Перебираем пост, записываем в массив значения полей
		foreach ($_POST as $key=>$val){
			$param[$key] = nl2br($_POST[$key]);
		}
		//Добавим адрес страницы, с которой пришёл запрос
		$param['userUrl'] = $_SERVER['HTTP_REFERER'];
		$text = $modx->evalSnippets($modx->parseChunk($tpl, $param, '[+','+]'));
	}
	
	$res = $modx->runSnippet('ddSendMail', array(
		'email' => $email,
		'from' => $from,
		'subject' => $subject,
		'text' => $text,
		'inputName' => $filesFields
	));
	
	return json_encode(array('status' => $res, 'title' => $titles[$res], 'message' => $message[$res]));
}
?>