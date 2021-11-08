<?php
/**
 * ddSendFeedback
 * @version 2.7.1 (2021-11-09)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddsendfeedback
 * 
 * @copyright 2010–2021 DD Group {@link https://DivanDesign.biz }
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