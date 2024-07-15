# (MODX)EvolutionCMS.snippets.ddSendFeedback changelog


## Version 2.9 (2024-07-15)
* \+ SenderEmail → Parameters → `senders->email->to`: Addresses validation has been added. So if you specify only invalid emails, you will receive an error in the CMS log that not all required parameters have been set.
* \+ Sender → Parameters → `senders->{$senderName}->isFailRequiredParamsDisplayedToLog`: The new optional parameter. Allows you do disable a failure message to the CMS log when required parameters are not set.


## Version 2.8 (2024-07-13)

* \* Sender:
	* \+ Parameters:
		* \+ `senders->{$senderName}->isFailDisplayedToUser`: The new optional parameter. Allows to prevent displaying a failure message to user when sending is failed.
		* \+ `senders->{$senderName}->tpl`:
			* \+ Any empty placeholders will be deleted before sending.
			* \+ Valid values → `object`: The new valid value. If the parameter set as an object, each item will be parsed as an independent template and then the result will be converted to JSON, it can be useful if you need to send a JSON object.
	* \+ CRMLiveSklad: The new sender. Allows to send orders to CRM LiveSklad.com.
	* \+ CustomHTTPRequest → Parameters → `senders->customhttprequest->requestResultParams`: The new group of parameters. Allows you to configure response parsing (see README).
	* \+ Email, Slack, CustomHTTPRequest: Sending error message in CMS log has been added.
	* \* Telegram, SMSRu, CRMLiveSklad: Sending error message in CMS log has been improved.
	* \+ SMSRu: An API error message has been added to a CMS log message.
* \* `\ddTools::getTpl` is used instead of `$modx->getTpl` (means a bit less bugs).
* \* `\ddTools::isEmpty` is used instead of `empty` to check array/object variables for less fragility.
* \* README → Parameters description → Senders:
	* \* Some text improvements.
	* \* Examples: HJSON is used for all examples.
	* \+ Links → GitHub.
* \+ CHANGELOG: Description of several old versions has been added.
* \+ Composer.json → `autoload`.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.62 is required.


## Version 2.7.1 (2021-11-09)

* \* Improved removing of empty placeholders while `senders->{$senderName}->tpl` parsing.


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


## Version 2.4 (2019-06-21)

* \* Attention! (MODX)EvolutionCMS.snippets.ddMakeHttpRequest >= 1.4 is required.
* \+ Senders → Telegram: Added the ability to work with a proxy server (`proxy` parameter).


## Version 2.3 (2018-10-09)

* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.23 is required.
* \+ Senders → Sms.ru: The `from` parameter has been added.


## Version 2.2 (2018-03-22)

* \+ Senders → Telegram: The `messageMarkupSyntax` and `disableWebPagePreview` parameters were added.


## Version 2.1 (2018-03-21)

* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.21 is required.
* \* Sender → Email: Fix problem when specifying multiple mailboxes.
* \+ Added the ability to send a message to a Telegram channel.
* \+ Added the ability to send SMS messages through the Sms.ru service.


## Version 2.0 (2017-02-06)

* \* Attention! Backward compatibility is broken.
* \* Attention!(MODX)EvolutionCMS.snippets.ddMakeHttpRequest >= 1.3 is required.
* \+ Added the ability to send a message to a Slask channel.
* \+ Added the ability to send messages through several Senders at the same time (e. g. to a Slack channel and emails).


## Version 1.11 (2016-10-30)

* \* Attention! (MODX)EvolutionCMS >= 1.1 is required.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.16 is required.
* \+ Added support of the `@CODE:` keyword prefix in the letter template.
* \* Empty placeholders will be removed from the letter template before final parsing (where snippet runs, etc).


## Version 1.5 (2011-08-18)

* \+ Parameters → `fromField`: The new parameter. `$_POST` array element with mailer name are taken (replacing `from`).


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>