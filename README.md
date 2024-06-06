# (MODX)EvolutionCMS.snippets.ddSendFeedback

A snippet for sending users' feedback messages to you. It is very useful along with ajax technology.

Supports sending messages to:
* Email
* Telegram
* Slack
* Sms.ru
* Custom HTTP request

The snippet returns a JSON string with the following fields:
```json
{
	"meta": {
		"code": 200,
		"success": true,
		"message": {
			//Message content (from “result_messageSuccess” / “result_messageFail” respectively).
			"content": "We will contact you later.",
			//Message title (from “result_titleSuccess” / “result_titleFail” respectively).
			"title": "Message sent successfully"
		}
	}
}
```


## Requires

* PHP >= 5.6
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.62
* [(MODX)EvolutionCMS.snippets.ddMakeHttpRequest](https://code.divandesign.biz/modx/ddmakehttprequest) >= 2.3.1


## Documentation


### Installation


#### Manually


##### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddSendFeedback`.
2. Description: `<b>2.7.1</b> A snippet for sending users' feedback messages to you. It is very useful along with ajax technology.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddSendFeedback_snippet.php` file from the archive.


##### 2. Elements → Manage Files

1. Create a new folder `assets/snippets/ddSendFeedback/`.
2. Extract the archive to the folder (except `ddSendFeedback_snippet.php`).


#### Using [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Just run the following PHP code in your sources or [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Include (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddInstaller/require.php'
);

//Install (MODX)EvolutionCMS.snippets.ddSendFeedback
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddSendFeedback',
	'type' => 'snippet'
]);
```

* If `ddSendFeedback` is not exist on your site, `ddInstaller` will just install it.
* If `ddSendFeedback` is already exist on your site, `ddInstaller` will check it version and update it if needed.


### Parameters description


#### General

* `result_titleSuccess`
	* Desctription: The title that will be returned if the letter sending is successful (the `title` field of the returned JSON).
	* Valid values: `string`
	* Default value: `'Message sent successfully'`
	
* `result_titleFail`
	* Desctription: The title that will be returned if the letter sending is failed somehow (the `title` field of the returned JSON).
	* Valid values: `string`
	* Default value: `'Unexpected error =('`
	
* `result_messageSuccess`
	* Desctription: The message that will be returned if the letter sending is successful (the `message` field of the returned JSON).
	* Valid values: `string`
	* Default value: `'We will contact you later.'`
	
* `result_messageFail`
	* Desctription: The message that will be returned if the letter sending is failed somehow (the `message` field of the returned JSON).
	* Valid values: `string`
	* Default value: `'Something happened while sending the message.<br />Please try again later.'`


#### Senders

* `senders`
	* Desctription: Senders and their params. You can use several senders at the same time.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
	* **Required**
	
* `senders->{$senderName}`
	* Desctription: A sender, when the key is the sender name and the value is the sender parameters.
	* Valid values: `object`
	* **Required**


##### Senders → Email

* `senders->email`
	* Desctription: The sender parameters.  
		Send method (PHP `mail()` or SMTP) sets up in CMS config.
	* Valid values: `object`
	* Default value: —
	
* `senders->email->to`
	* Desctription: Mailing addresses (to whom).
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* **Required**
	
* `senders->email->to[i]`
	* Desctription: An address.
	* Valid values: `stringEmail`
	* **Required**
	
* `senders->email->tpl`
	* Desctription: The template of a letter.  
		Use `[(site_url)][~[+docId+]~]` to generate the url of a document (`[(site_url)]` is required because of need for using the absolute links in the emails).  
		
		Available placeholders:
		* `[+docId+]` — the ID of a document that the request has been sent from
		* all array items of `$_POST`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* **Required**
	
* `senders->email->tpl_placeholders`
	* Desctription:
		Additional data has to be passed into the `senders->email->tpl`.  
		Nested objects and arrays are supported too:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Valid values: `object`
	* Default value: —
	
* `senders->email->tpl_placeholders->{$placeholderName}`
	* Desctription: The key is a placeholder name, the value is a placeholder value.
	* Valid values: `mixed`
	* **Required**
	
* `senders->email->subject`
	* Desctription: Message subject.
	* Valid values: `string`
	* Default value: `'Feedback'`
	
* `senders->email->from`
	* Desctription: Mailer address (from who).
	* Valid values: `stringEmail`
	* Default value: `$modx->getConfig('emailsender')`
	
* `senders->email->fileInputNames`
	* Desctription: Input tags names separated by commas that files are required to be taken from.  
		Used if files are sending in the request (`$_FILES` array).
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: —


##### Senders → Telegram

* `senders->telegram`
	* Desctription: The sender parameters.
	* Valid values: `object`
	* Default value: —
	
* `senders->telegram->botToken`
	* Desctription: The bot token that will send a message, like `botId:HASH`.
	* Valid values: `string`
	* **Required**
	
* `senders->telegram->chatId`
	* Desctription: ID of the chat to which the message will be sent.
	* Valid values: `string`
	* **Required**
	
* `senders->telegram->tpl`
	* Desctription: The template of a message.  
		Use `[(site_url)][~[+docId+]~]` to generate the url of a document (`[(site_url)]` is required because of need for using the absolute links in the messages).  
		
		Available placeholders:
		* `[+docId+]` — the ID of a document that the request has been sent from
		* all array items of `$_POST`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* **Required**
	
* `senders->telegram->textMarkupSyntax`
	* Desctription: The syntax in which the message is written.
	* Valid values:
		* `'markdown'`
		* `'html'`
		* `''`
	* Default value: `''`
	
* `senders->telegram->disableWebPagePreview`
	* Desctription: Disables link previews for links in this message.
	* Valid values: `boolean`
	* Default value: `false`
	
* `senders->telegram->proxy`
	* Desctription: Proxy server in format `'protocol://user:password@ip:port'`.  
		E. g. `'theuser:qwerty123@11.22.33.44:5555'` or `'socks5://someuser:somepassword@11.22.33.44:5555'`.
	* Valid values: `string`
	* Default value: —


##### Senders → Slack

* `senders->slack`
	* Desctription: The sender parameters.
	* Valid values: `object`
	* Default value: —
	
* `senders->slack->url`
	* Desctription: WebHook URL.
	* Valid values: `stringUrl`
	* **Required**
	
* `senders->slack->tpl`
	* Desctription: The template of a message.  
		Use `[(site_url)][~[+docId+]~]` to generate the url of a document (`[(site_url)]` is required because of need for using the absolute links in the messages).  
		
		Available placeholders:
		* `[+docId+]` — the ID of a document that the request has been sent from
		* all array items of `$_POST`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* **Required**
	
* `senders->slack->tpl_placeholders`
	* Desctription:
		Additional data has to be passed into the `senders->slack->tpl`.  
		Nested objects and arrays are supported too:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Valid values: `object`
	* Default value: —
	
* `senders->slack->tpl_placeholders->{$placeholderName}`
	* Desctription: The key is a placeholder name, the value is a placeholder value.
	* Valid values: `mixed`
	* **Required**
	
* `senders->slack->channel`
	* Desctription: Channel in Slack to send.
	* Valid values: `string`
	* Default value: Selected in Slack when you create WebHook.
	
* `senders->slack->botName`
	* Desctription: Bot name.
	* Valid values: `string`
	* Default value: `'ddSendFeedback'`
	
* `senders->slack->botIcon`
	* Desctription: Bot icon.
	* Valid values: `string`
	* Default value: `':ghost:'`


##### Senders → Sms.ru

* `senders->smsru`
	* Desctription: The sender parameters.
	* Valid values: `object`
	* Default value: —
	
* `senders->smsru->apiId`
	* Desctription: Secret code from sms.ru.
	* Valid values: `string`
	* **Required**
	
* `senders->smsru->tpl`
	* Desctription: The template of a message.  
		Use `[(site_url)][~[+docId+]~]` to generate the url of a document (`[(site_url)]` is required because of need for using the absolute links in the messages).  
		
		Available placeholders:
		* `[+docId+]` — the ID of a document that the request has been sent from
		* all array items of `$_POST`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* **Required**
	
* `senders->smsru->to`
	* Desctription: A phone.
	* Valid values: `string`
	* **Required**
	
* `senders->smsru->from`
	* Desctription: Sms sender name/phone.
	* Valid values: `string`
	* Default value: —


##### Senders → Custom HTTP request

* `senders->customhttprequest`
	* Desctription: The sender parameters.
	* Valid values: `object`
	* Default value: —
	
* `senders->customhttprequest->url`
	* Desctription: The URL to request.
	* Valid values: `stringUrl`
	* **Required**
	
* `senders->customhttprequest->tpl`
	* Desctription: The template of a data.  
		Use `[(site_url)][~[+docId+]~]` to generate the url of a document (`[(site_url)]` is required because of need for using the absolute links in the messages).  
		
		Available placeholders:
		* `[+docId+]` — the ID of a document that the request has been sent from
		* all array items of `$_POST`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* **Required**
	
* `senders->customhttprequest->tpl_placeholders`
	* Desctription:
		Additional data has to be passed into the `senders->customhttprequest->tpl`.  
		Nested objects and arrays are supported too:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Valid values: `object`
	* Default value: —
	
* `senders->customhttprequest->tpl_placeholders->{$placeholderName}`
	* Desctription: The key is a placeholder name, the value is a placeholder value.
	* Valid values: `mixed`
	* **Required**
	
* `senders->customhttprequest->method`
	* Desctription: Request type.
	* Valid values:
		* `'get'`
		* `'post'`
	* Default value: `'post'`
	
* `senders->customhttprequest->sendRawPostData`
	* Desctription: Send raw post data. E. g. if you need JSON in request payload.
	* Valid values: `boolean`
	* Default value: `false`
	
* `senders->customhttprequest->headers`
	* Desctription: An array of HTTP header fields to set.  
		E. g. `['Accept: application/vnd.api+json', 'Content-Type: application/vnd.api+json']`.
	* Valid values: `array`
	* Default value: —
	
* `senders->customhttprequest->userAgent`
	* Desctription: The contents of the 'User-Agent: ' header to be used in a HTTP request.
	* Valid values: `string`
	* Default value: —
	
* `senders->customhttprequest->timeout`
	* Desctription: The maximum number of seconds for execute request.
	* Valid values: `integer`
	* Default value: `60`
	
* `senders->telegram->proxy`
	* Desctription: Proxy server in format `'protocol://user:password@ip:port'`.  
		E. g. `'theuser:qwerty123@11.22.33.44:5555'` or `'socks5://someuser:somepassword@11.22.33.44:5555'`.
	* Valid values: `string`
	* Default value: —


### Examples


#### Send to email

```
[!ddSendFeedback?
	&senders=`{
		"email": {
			"to": "robert@awesome.org",
			"tpl": "@CODE: Message from document with ID [+docId+].",
			"subject": "Example sending feedback",
			"from": "sitebot@awesome.org",
			"filesInputNames": "file"
		}
	}`
	&result_titleSuccess=`All successfully! Message send!`
	&result_titleFail=`Something went wrong! Message was not send!`
	&result_messageSuccess=`Thanks for your feedback! We will contact you later.`
	&result_messageFail=`Please try again later or call our support! Sorry for the inconvenience.`
!]
```


#### Send to Telegram

```
[!ddSendFeedback?
	&senders=`{
	 	"telegram": {
	 		"botToken": "123:AAAAAA",
			"chatId": "-11111",
			"tpl": "@CODE:Test message from [(site_url)]!",
			"proxy": "http://asan:gd324ukl@11.22.33.44:5555"
	 	}
	}`
!]
```


#### Send to Slack

```
[!ddSendFeedback?
	&senders=`{
	 	"slack": {
	 		"url": "https://hooks.slack.com/services/WEBHOOK",
			"tpl": "@CODE: Message from document with id [+docId+]."
	 	}
	}`
!]
```


#### Send to Sms.ru

```
[!ddSendFeedback?
	&senders=`{
	 	"smsru": {
	 		"apiId": "00000000-0000-0000-0000-000000000000",
			"to": "89999999999",
			"tpl": "general_letters_feedbackToSMS"
	 	}
	}`
!]
```


#### Run the snippet through `\DDTools\Snippet::runSnippet` without DB and eval

```php
//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

//Run (MODX)EvolutionCMS.snippets.ddSendFeedback
\DDTools\Snippet::runSnippet([
	'name' => 'ddSendFeedback',
	'params' => [
		'senders' => [
		 	'telegram' => [
		 		'botToken' => '123:AAAAAA',
				'chatId' => '-11111',
				'tpl' => '@CODE:Test message from [(site_url)]!'
		 	]
	 	]
	]
]);
```


## Links

* [Home page](https://code.divandesign.biz/modx/ddsendfeedback)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddsendfeedback)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />