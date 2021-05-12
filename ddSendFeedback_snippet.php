<?php
/**
 * ddSendFeedback
 * @version 2.6.1 (2021-02-07)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddsendfeedback
 * 
 * @copyright 2010–2021 DD Group {@link https://DivanDesign.biz }
 */

namespace ddSendFeedback;

//# Include
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


//# Prepare params
$params = \DDTools\ObjectTools::extend([
	'objects' => [
		//Defaults
		(object) [
			'result_titleSuccess' => null,
			'result_titleFail' => null,
			'result_messageSuccess' => null,
			'result_messageFail' => null,
			'senders' => null
		],
		$params
	]
]);

//Получаем язык админки
$lang = $modx->getConfig('manager_language');

//Если язык русский
if(
	$lang == 'russian-UTF8' ||
	$lang == 'russian'
){
	if (is_null($params->result_titleSuccess)){
		$params->result_titleSuccess = 'Заявка успешно отправлена';
	}
	if (is_null($params->result_titleFail)){
		$params->result_titleFail = 'Непредвиденная ошибка =(';
	}
	if (is_null($params->result_messageSuccess)){
		$params->result_messageSuccess = 'Наш специалист свяжется с вами в ближайшее время.';
	}
	if (is_null($params->result_messageFail)){
		$params->result_messageFail = 'Во время отправки заявки что-то произошло.<br />Пожалуйста, попробуйте чуть позже.';
	}
}else{
	if (is_null($params->result_titleSuccess)){
		$params->result_titleSuccess = 'Message sent successfully';
	}
	if (is_null($params->result_titleFail)){
		$params->result_titleFail = 'Unexpected error =(';
	}
	if (is_null($params->result_messageSuccess)){
		$params->result_messageSuccess = 'We will contact you later';
	}
	if (is_null($params->result_messageFail)){
		$params->result_messageFail = 'Something happened while sending the message.<br />Please try again later.';
	}
}

$params->senders = \DDTools\ObjectTools::convertType([
	'object' => $params->senders,
	'type' => 'objectArray'
]);


//# Run
$snippetResult = \ddTools::getResponse();
$result_meta = [
	//Bad Request (required parameters are not set)
	'code' => 400,
	'success' => false
];

//Senders is required parameter
if (!empty($params->senders)){
	$outputMessages = [
		'titles' => [
			0 => $params->result_titleFail,
			1 => $params->result_titleSuccess
		],
		'messages' => [
			0 => $params->result_messageFail,
			1 => $params->result_messageSuccess
		]
	];
	
	$sendResults = [];
	
	//Iterate through all senders to create their instances
	foreach(
		$params->senders as
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

$snippetResult->setMeta($result_meta);

return $snippetResult->toJSON();
?>