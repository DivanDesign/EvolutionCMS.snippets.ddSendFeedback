# (MODX)EvolutionCMS.snippets.ddSendFeedback changelog


## Version 2.6 (2021-01-18)
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.32 is required.
* \+ Parameters → `senders`: Can be set as a native PHP array or object (e. g. for calls through `$modx->runSnippet`).
* \+ REAMDE:
	* \+ The snippet results description.
	* \+ Requires.
	* \+ Documentation.
	* \+ Links.
	* \+ Style improvements.
* \+ CHANGELOG.
* \+ CHANGELOG_ru.
* \+ Composer.json:
	* \+ `keywords`: Additional keywords.
	* \+ `homepage`.
	* \+ `authors`.
	* \+ `require`.


## Version 2.5 (2019-12-15)
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.25 is required.
* \+ Parameters → `senders->customhttprequest`: The new sender.
* \* Parameters → `senders->telegram->textMarkupSyntax`: Was renamed from `senders->telegram->messageMarkupSyntax` with backward compatibility.
* \* `\ddTools::$modx` is used instead of `$modx` in all methods.
* \+ `\ddSendFeedback\Sender\`: Added required parameters checking.
* \+ `\ddSendFeedback\Sender\`: Will not throws an error if required parameters are not set or set but empty. Feel free and don't care about that.
* \* `\ddSendFeedback\Sender\Sender::__construct`: `DDTools\BaseClass::setExistingProps` is used.
* \- `\ddSendFeedback\Sender\Sender::includeSenderByName`: The method was removed. `\DDTools\BaseClass::createChildInstance` is used instead.
* \+ `\ddSendFeedback\Sender\Sender::initPostPlaceholders`: POST fields prepared through `nl2br` only if `$this->textMarkupSyntax` == `'html'`.
* \+ Composer.json.


## Version 1.5 (2011-08-18)
* \+ Parameters → `fromField`: The new parameter. `$_POST` array element with mailer name are taken (replacing `from`).


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />
<style>ul{list-style:none;}</style>