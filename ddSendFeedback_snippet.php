<?php
/**
 * ddSendFeedback
 * @version 2.10 (2026-09-10)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddsendfeedback
 * 
 * @copyright 2010–2026 https://Ronef.me
 */

// Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddSendFeedback',
	'params' => $params,
]);
?>