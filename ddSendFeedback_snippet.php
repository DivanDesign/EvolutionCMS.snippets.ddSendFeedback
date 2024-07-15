<?php
/**
 * ddSendFeedback
 * @version 2.9 (2024-07-15)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddsendfeedback
 * 
 * @copyright 2010–2024 Ronef {@link https://Ronef.ru }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddSendFeedback',
	'params' => $params
]);
?>