<?php
/**
 * ddSendFeedback
 * @version 2.0 (2017-02-06)
 * 
 * @desc A snippet for sending users' feedback messages to a required email. It is very useful along with ajax technology.
 * 
 * @uses PHP >= 5.4.
 * @uses MODXEvo >= 1.1.
 * @uses MODXEvo.library.ddTools >= 0.16.
 * @uses MODXEvo.snippet.ddMakeHttpRequest >= 1.3.
 * 
 * General:
 * @param $result_titleSuccess {string} — The title that will be returned if the letter sending is successful (the «title» field of the returned JSON). Default: 'Message sent successfully'.
 * @param $result_titleFail {string} — The title that will be returned if the letter sending is failed somehow (the «title» field of the returned JSON). Default: 'Unexpected error =('.
 * @param $result_messageSuccess {string} — The message that will be returned if the letter sending is successful (the «message» field of the returned JSON). Default: 'We will contact you later.'.
 * @param $result_messageFail {string} — The message that will be returned if the letter sending is failed somehow (the «message» field of the returned JSON). Default: 'Something happened while sending the message.<br />Please try again later.'.
 * 
 * Senders:
 * @param $senders {stirng_json|string_queryFormated} — JSON or query-formated string determining which senders should be used.
 * @param $senders[item] {array_associative} — Key is a sender name, value is sender parameters.
 * Email sender:
 * @param $senders['email'] {array_associative} — Sender params.
 * @param $senders['email']['to'] {array|string_commaSeparated} — Mailing addresses (to whom). @required
 * @param $senders['email']['to'][i] {string_email} — An address. @required
 * @param $senders['email']['tpl'] {string_chunkName|string} — The template of a letter (chunk name or code via “@CODE:” prefix). Available placeholders: [+docId+] — the id of a document that the request has been sent from; the array components of $_POST. Use [(site_url)][~[+docId+]~] to generate the url of a document ([(site_url)] is required because of need for using the absolute links in the emails). @required
 * @param $senders['email']['tpl_placeholders'] {array_associative} — Additional data has to be passed into “$senders['email']['tpl']”. Default: ''.
 * @param $senders['email']['tpl_placeholders'][item] {string} — Key — a placeholder name, value — a placeholder value. Default: ''.
 * @param $senders['email']['subject'] {string} — Message subject. Default: 'Feedback'.
 * @param $senders['email']['from'] {string} — Mailer address (from who). Default: $modx->getConfig('emailsender').
 * @param $senders['email']['fileInputNames'] {array|string_commaSeparated} — Input tags names separated by commas that files are required to be taken from. Used if files are sending in the request ($_FILES array). Default: ''.
 * Slack sender:
 * @param $senders['slack'] {array_associative} — Sender params.
 * @param $senders['slack']['url'] {string_url} — WebHook URL. @required
 * @param $senders['slack']['tpl'] {string_chunkName|string} — The template of a message (chunk name or code via “@CODE:” prefix). Available placeholders: [+docId+] — the id of a document that the request has been sent from; the array components of $_POST. Use [(site_url)][~[+docId+]~] to generate the url of a document ([(site_url)] is required because of need for using the absolute links in the emails). @required
 * @param $senders['slack']['tpl_placeholders'] {array_associative} — Additional data has to be passed into “$senders['slack']['tpl']”. Default: ''.
 * @param $senders['slack']['tpl_placeholders'][item] {string} — Key — a placeholder name, value — a placeholder value. Default: ''.
 * @param $senders['slack']['channel'] {string} — Channel in Slack to send. Default: Selected in Slack when you create WebHook.
 * @param $senders['slack']['botName'] {string} — Bot name. Default: 'ddSendFeedback'.
 * @param $senders['slack']['botIcon'] {string} — Bot icon. Default: ':ghost:'.
 * e.g. $senders = 'email[to]=info@divandesign.biz&email[tpl]=general_letters_feedbackToEmail&email[tpl_placeholders][testPlaceholder]=test&slack[url]=https://hooks.slack.com/services/WEBHOOK&slack[tpl]=general_letters_feedbackToSlack'.
 * 
 * @copyright 2010–2017 DivanDesign {@link http://www.DivanDesign.biz }
 */

namespace ddSendFeedback;

if(is_file($modx->config['base_path'].'vendor/autoload.php')){
	require_once($modx->config['base_path'].'vendor/autoload.php');
}

if(!class_exists('\ddTools')){
	require_once $modx->getConfig('base_path').'assets/libs/ddTools/modx.ddtools.class.php';
}

if(!class_exists('\ddSendFeedback\Sender\Sender')){
	require_once($modx->getConfig('base_path').'assets/snippets/ddSendFeedback/require.php');
}

$result = \ddTools::getResponse();
$result_meta = [
	//Bad Request (required parameters are not set)
	'code' => 400,
	'success' => false
];

//Senders is required parameter
if (isset($senders)){
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
	}else{
		$result_titleSuccess = isset($result_titleSuccess) ? $result_titleSuccess : 'Message sent successfully';
		$result_titleFail = isset($result_titleFail) ? $result_titleFail : 'Unexpected error =(';
		$result_messageSuccess = isset($result_messageSuccess) ? $result_messageSuccess : 'We will contact you later.';
		$result_messageFail = isset($result_messageFail) ? $result_messageFail : 'Something happened while sending the message.<br />Please try again later.';
	}
	
	$outputMessages = [
		'titles' => [
			0 => $result_titleFail,
			1 => $result_titleSuccess
		],
		'messages' => [
			0 => $result_messageFail,
			1 => $result_messageSuccess
		]
	];
	
	$sendResults = [];
	
	//JSON
	if (substr($senders, 0, 1) == '{'){
		$senders = json_decode($senders, true);
	}else{
		//Prepare senders
		parse_str($senders, $senders);
	}
	
	//Iterate through all senders to create their instances
	foreach($senders as $senderName => $senderParams){
		$senderClass = \ddSendFeedback\Sender\Sender::includeSenderByName($senderName);
		
		//Passing parameters to senders's constructor
		$sender = new $senderClass($senderParams);
		//Send message (items with integer keys are not overwritten)
		$sendResults = array_merge($sendResults, $sender->send());
	}
	
	//Fail by default
	$sendResults_status = 0;
	
	//Перебираем все статусы отправки
	foreach ($sendResults as $sendResults_item){
		//Запоминаем
		$sendResults_status = intval($sendResults_item);
		
		//Если не отправлось хоть на один адрес, считаем, что всё плохо
		if ($sendResults_status == 0){
			break;
		}
	}
	
	$result_meta['message'] = [
		'content' => $outputMessages['messages'][$sendResults_status],
		'title' => $outputMessages['titles'][$sendResults_status]
	];
	
	$result_meta['success'] = boolval($sendResults_status);
	
	if ($result_meta['success']){
		$result_meta['code'] = 200;
	};
}

$result->setMeta($result_meta);

return $result->toJSON();
?>