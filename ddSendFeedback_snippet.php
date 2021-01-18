<?php
/**
 * ddSendFeedback
 * @version 2.6 (2021-01-18)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddsendfeedback
 * 
 * @copyright 2010–2021 DD Group {@link https://DivanDesign.biz }
 */

namespace ddSendFeedback;

$snippetPath =
	$modx->getConfig('base_path') .
	'assets/snippets/ddSendFeedback/'
;

//TODO: Remove it
if(
	is_file(
		$modx->config['base_path'] .
		'vendor/autoload.php'
	)
){
	require_once(
		$modx->getConfig('base_path') .
		'vendor/autoload.php'
	);
}

//Include (MODX)EvolutionCMS.libraries.ddTools
if(!class_exists('\ddTools')){
	require_once(
		$modx->getConfig('base_path') .
		'assets/libs/ddTools/modx.ddtools.class.php'
	);
}

if(!class_exists('\ddSendFeedback\Sender\Sender')){
	require_once(
		$snippetPath .
		'require.php'
	);
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
		$result_titleSuccess =
			isset($result_titleSuccess) ?
			$result_titleSuccess :
			'Заявка успешно отправлена'
		;
		$result_titleFail =
			isset($result_titleFail) ?
			$result_titleFail :
			'Непредвиденная ошибка =('
		;
		$result_messageSuccess =
			isset($result_messageSuccess) ?
			$result_messageSuccess :
			'Наш специалист свяжется с вами в ближайшее время.'
		;
		$result_messageFail =
			isset($result_messageFail) ?
			$result_messageFail :
			'Во время отправки заявки что-то произошло.<br />Пожалуйста, попробуйте чуть позже.'
		;
	}else{
		$result_titleSuccess =
			isset($result_titleSuccess) ?
			$result_titleSuccess :
			'Message sent successfully'
		;
		$result_titleFail =
			isset($result_titleFail) ?
			$result_titleFail :
			'Unexpected error =('
		;
		$result_messageSuccess =
			isset($result_messageSuccess) ?
			$result_messageSuccess :
			'We will contact you later.'
		;
		$result_messageFail =
			isset($result_messageFail) ?
			$result_messageFail :
			'Something happened while sending the message.<br />Please try again later.'
		;
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
	
	$senders = \ddTools::encodedStringToArray($senders);
	
	//Iterate through all senders to create their instances
	foreach(
		$senders as
		$senderName =>
		$senderParams
	){
		$sender = \ddSendFeedback\Sender\Sender::createChildInstance([
			'name' => $senderName,
			'parentDir' =>
				$snippetPath .
				'src' .
				DIRECTORY_SEPARATOR .
				'Sender'
			,
			//Passing parameters to senders's constructor
			'params' => $senderParams
		]);
		
		//Send message (items with integer keys are not overwritten)
		$sendResults = array_merge(
			$sendResults,
			$sender->send()
		);
	}
	
	//Fail by default
	$sendResults_status = 0;
	
	//Перебираем все статусы отправки
	foreach (
		$sendResults as
		$sendResults_item
	){
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