<?php
/**
 * ddSendFeedback.php
 * @version 1.8.2 (2014-01-15)
 * 
 * @desc A snippet for sending users' feedback messages to a required email. It is very useful along with ajax technology.
 * 
 * @uses Library MODX.ddTools 0.13.
 * @uses Snippet ddGetDocumentField 2.4 might be used if field getting is required.
 * 
 * @param email {comma separated string} - Mailing addresses (to whom). @required
 * @param getEmail {string} - Field name/TV containing the address to mail.
 * @param getId {integer} - ID of a document with the required field contents.
 * @param tpl {string: chunkName} - The template of a letter (chunk name). Available placeholders: [+docId+] — the id of a document that the request has been sent from; the array components of $_POST. Use [(site_url)][~[+docId+]~] to generate the url of a document ([(site_url)] is required because of need for using the absolute links in the emails). @required
 * @param text {string} - Message text. The template parameter will be ignored if the text is defined. It is useful when $modx->runSnippets() uses. Default: ''.
 * @param subject {string} - Message subject. Default: 'Обратная связь'.
 * @param from {string} - Mailer address (from who). Default: 'info@divandesign.ru'.
 * @param fromField {string} - An element of $_POST containing mailer address. The “from” parameter will be ignored if “fromField” is defined and is not empty. Default: ''.
 * @param titleTrue {string} - The title that will be returned if the letter sending is successful (the «title» field of the returned JSON). Default: 'Заявка успешно отправлена'.
 * @param titleFalse {string} - The title that will be returned if the letter sending is failed somehow (the «title» field of the returned JSON). Default: 'Непредвиденная ошибка =('.
 * @param msgTrue {string} - The message that will be returned if the letter sending is successful (the «message» field of the returned JSON). Default: 'Наш специалист свяжется с вами в ближайшее время.'.
 * @param msgFalse {string} - The message that will be returned if the letter sending is failed somehow (the «message» field of the returned JSON). Default: 'Во время отправки заявки что-то произошло.<br />Пожалуйста, попробуйте чуть позже.'.
 * @param filesFields {comma separated string} - Input tags names separated by commas that files are required to be taken from. Used if files are sending in the request ($_FILES array). Default: ''.
 * 
 * @link http://code.divandesign.biz/modx/ddsendfeedback/1.8.2
 * 
 * @copyright 2014, DivanDesign
 * http://www.DivanDesign.biz
 */

//Подключаем modx.ddTools
require_once $modx->config['base_path'].'assets/snippets/ddTools/modx.ddtools.class.php';

//Если задано имя поля почты, которое необходимо получить
if (isset($getEmail)){
	$email = $modx->runSnippet('ddGetDocumentField', array(
		'id' => $getId,
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
	if (isset($fromField) && $_POST[$fromField] != ''){
		$from = $_POST[$fromField];
	}
	
	//Проверяем передан ли текст сообщения
	if (!isset($text)){
		$param = array();
		
		//Перебираем пост, записываем в массив значения полей
		foreach ($_POST as $key => $val){
			//Если это строка (может быть массив, например, в случае с файлами)
			if (is_string($_POST[$key])){
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