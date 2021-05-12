# (MODX)EvolutionCMS.snippets.ddSendFeedback changelog


## Version 2.7 (2021-05-12)
* \* Attention! PHP >= 5.6 is required.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.50 is required.
* \* Attention! (MODX)EvolutionCMS.snippets.ddMakeHttpRequest >= 2.3.1 is required.
* \+ Parameters:
	* \+ `senders`: Can also be set as [HJSON](https://hjson.github.io/) or as a native PHP object or array (e. g. for calls through `$modx->runSnippet`).
	* \+ `senders->customhttprequest->sendRawPostData`: The new parameter (see README).
* \+ You can just call `\DDTools\Snippet::runSnippet` to run the snippet without DB and eval (see README → Examples).
* \+ `\ddSendFeedback\Snippet`: The new class. All snippet code was moved here.
* \* `\DDTools\Snippet::runSnippet` is used instead of `$modx->runSnippet` to run (MODX)EvolutionCMS.snippets.ddMakeHttpRequest without DB and eval.
* \+ README → Documentation → Installation → Using (MODX)EvolutionCMS.libraries.ddInstaller.
* \+ Composer.json:
	* \+ `support`.
	* \+ `authors`: Added missed homepages.


## Version 2.6.1 (2021-02-07)
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.41 is required (not tested with older versions).
* \+ `\ddSendFeedback\Sender\Sender::__construct`: Less fragile code, `\DDTools\ObjectTools::extend` is used instead of `array_merge`.
* \* Snippet: `\DDTools\ObjectTools::convertType` is used istead of `\ddTools::encodedStringToArray`.


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