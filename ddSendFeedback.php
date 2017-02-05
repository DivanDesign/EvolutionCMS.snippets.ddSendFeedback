<?php
/**
 * ddSendFeedback
 * @version 1.11 (2016-10-30)
 * 
 * @desc A snippet for sending users' feedback messages to a required email. It is very useful along with ajax technology.
 * 
 * @uses PHP >= 5.4.
 * @uses MODXEvo >= 1.1.
 * @uses MODXEvo.library.ddTools >= 0.16.
 * 
 * @param $email {string_commaSeparated} — Mailing addresses (to whom). @required
 * @param $email_docField {string} — Field name/TV containing the address to mail. Default: —.
 * @param $email_docId {integer} — ID of a document with the required field contents. Default: —.
 * @param $tpl {string_chunkName|string} — The template of a letter (chunk name or code via “@CODE:” prefix). Available placeholders: [+docId+] — the id of a document that the request has been sent from; the array components of $_POST. Use [(site_url)][~[+docId+]~] to generate the url of a document ([(site_url)] is required because of need for using the absolute links in the emails). @required
 * @param $tpl_placeholders {string_queryString} — Additional data as query string (https://en.wikipedia.org/wiki/Query_string) has to be passed into “tpl”. E. g. “pladeholder1=value1&pagetitle=My awesome pagetitle!”. Arrays are supported too: “some[a]=one&some[b]=two” => “[+some.a+]”, “[+some.b+]”; “some[]=one&some[]=two” => “[+some.0+]”, “[some.1]”. Default: ''.
 * @param $text {string} — Message text. The template parameter will be ignored if the text is defined. It is useful when $modx->runSnippets() uses. Default: ''.
 * @param $subject {string} — Message subject. Default: 'Feedback'.
 * @param $from {string} — Mailer address (from who). Default: 'info@divandesign.biz'.
 * @param $filesFields {string_commaSeparated} — Input tags names separated by commas that files are required to be taken from. Used if files are sending in the request ($_FILES array). Default: ''.
 * @param $result_titleSuccess {string} — The title that will be returned if the letter sending is successful (the «title» field of the returned JSON). Default: 'Message sent successfully'.
 * @param $result_titleFail {string} — The title that will be returned if the letter sending is failed somehow (the «title» field of the returned JSON). Default: 'Unexpected error =('.
 * @param $result_messageSuccess {string} — The message that will be returned if the letter sending is successful (the «message» field of the returned JSON). Default: 'We will contact you later.'.
 * @param $result_messageFail {string} — The message that will be returned if the letter sending is failed somehow (the «message» field of the returned JSON). Default: 'Something happened while sending the message.<br />Please try again later.'.
 * 
 * @link http://code.divandesign.biz/modx/ddsendfeedback/1.11
 * 
 * @copyright 2010–2016 DivanDesign {@link http://www.DivanDesign.biz }
 */

//Подключаем MODX.ddTools
require_once $modx->getConfig('base_path').'assets/libs/ddTools/modx.ddtools.class.php';

//Для обратной совместимости
extract(ddTools::verifyRenamedParams($params, [
	'email_docField' => ['docField', 'getEmail'],
	'email_docId' => ['docId', 'getId'],
	'result_titleSuccess' => 'titleTrue',
	'result_titleFail' => 'titleFalse',
	'result_messageSuccess' => 'msgTrue',
	'result_messageFail' => 'msgFalse'
]));

//Если задано имя поля почты, которое необходимо получить
if (isset($email_docField)){
	$email = ddTools::getTemplateVarOutput([$email_docField], $email_docId);
	$email = $email[$email_docField];
}

//Если всё хорошо
if (
	(isset($tpl) || isset($text)) &&
	isset($email) &&
	($email != '')
){
	//Получаем язык админки
	$lang = $modx->getConfig('manager_language');
	
	//Если язык русский
	if(
		$lang == 'russian-UTF8' ||
		$lang == 'russian'
	){
		$result_titleSuccess = isset($result_titleSuccess) ? $result_titleSuccess : 'Заявка успешно отправлена';
		$result_titleFail = isset($result_titleFail) ? $result_titleFail : 'Непредвиденная ошибка =(';
		$result_messageSuccess = isset($result_messageSuccess) ? $result_messageSuccess : 'Наш специалист свяжется с вами в ближайшее время.';
		$result_messageFail = isset($result_messageFail) ? $result_messageFail : 'Во время отправки заявки что-то произошло.<br />Пожалуйста, попробуйте чуть позже.';
		$subject = isset($subject) ? $subject : 'Обратная связь';
	}else{
		$result_titleSuccess = isset($result_titleSuccess) ? $result_titleSuccess : 'Message sent successfully';
		$result_titleFail = isset($result_titleFail) ? $result_titleFail : 'Unexpected error =(';
		$result_messageSuccess = isset($result_messageSuccess) ? $result_messageSuccess : 'We will contact you later.';
		$result_messageFail = isset($result_messageFail) ? $result_messageFail : 'Something happened while sending the message.<br />Please try again later.';
		$subject = isset($subject) ? $subject : 'Feedback';
	}
	
	$titles = [$result_titleFail, $result_titleSuccess];
	$messages = [$result_messageFail, $result_messageSuccess];
	
	$from = isset($from) ? $from : 'info@divandesign.biz';
	
	//Проверяем передан ли текст сообщения
	if (!isset($text)){
		//Данные, которые необоходимо передать в шаблон
		if (isset($tpl_placeholders)){
			//Parse a query string
			parse_str($tpl_placeholders, $tpl_placeholders);
			//Unfold for arrays support (e. g. “some[a]=one&some[b]=two” => “[+some.a+]”, “[+some.b+]”; “some[]=one&some[]=two” => “[+some.0+]”, “[some.1]”)
			$tpl_placeholders = ddTools::unfoldArray($tpl_placeholders);
		}
		//Корректно инициализируем при необходимости
		if (
			!isset($tpl_placeholders) ||
			!is_array($tpl_placeholders)
		){
			$tpl_placeholders = [];
		}
		
		//Перебираем пост, записываем в массив значения полей
		foreach ($_POST as $key => $val){
			if (
				!isset($tpl_placeholders[$key]) &&
				//Если это строка или число (может быть массив, например, в случае с файлами)
				(
					is_string($_POST[$key]) ||
					is_numeric($_POST[$key])
				)
			){
				$tpl_placeholders[$key] = nl2br($_POST[$key]);
			}
		}
		
		//Добавим адрес страницы, с которой пришёл запрос
		$tpl_placeholders['docId'] = ddTools::getDocumentIdByUrl($_SERVER['HTTP_REFERER']);
		$text = ddTools::parseSource(ddTools::parseText([
			'text' => $modx->getTpl($tpl),
			'data' => $tpl_placeholders,
			'removeEmptyPlaceholders' => true
		]));
	}
	
	//Отправляем письмо
	$sendMailResult = ddTools::sendMail([
		'to' => explode(',', $email),
		'text' => $text,
		'from' => $from,
		'subject' => $subject,
		'fileInputNames' => explode(',', $filesFields)
	]);
	
	//Fail by default
	$result_status = 0;
	
	//Перебираем все статусы отправки
	foreach ($sendMailResult as $sendMailResult_item){
		//Запоминаем
		$result_status = $sendMailResult_item;
		
		//Если не отправлось хоть на один адрес, считаем, что всё плохо
		if ($result_status == 0){
			break;
		}
	}
	
	return json_encode([
		'status' => (bool) $result_status,
		'title' => $titles[$result_status],
		'message' => $messages[$result_status]
	]);
}
?>