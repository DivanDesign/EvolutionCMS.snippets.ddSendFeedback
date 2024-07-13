# (MODX)EvolutionCMS.snippets.ddSendFeedback changelog


## Версия 2.7.1 (2021-11-09)
* \* Улучшено удаление пустых плейсхолдеров при парсинге `senders->{$senderName}->tpl`.


## Версия 2.7 (2021-05-12)
* \* Внимание! Требуется PHP >= 5.6.
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.50.
* \* Внимание! Требуется (MODX)EvolutionCMS.snippets.ddMakeHttpRequest >= 2.3.1.
* \+ Параметры:
	* \+ `senders`: Также может быть задан, как [HJSON](https://hjson.github.io/) или как нативный PHP объект или массив (например, для вызовов через `$modx->runSnippet`).
	* \+ `senders->customhttprequest->sendRawPostData`: Новый параметр (см. README).
* \+ Запустить сниппет без DB и eval можно через `\DDTools\Snippet::runSnippet` (см. примеры в README).
* \+ `\ddSendFeedback\Snippet`: Новый класс. Весь код сниппета перенесён туда.
* \* `\DDTools\Snippet::runSnippet` используется вместо `$modx->runSnippet` для запуска (MODX)EvolutionCMS.snippets.ddMakeHttpRequest без DB и eval.
* \+ README → Документация → Установка → Используя (MODX)EvolutionCMS.libraries.ddInstaller.
* \+ Composer.json:
	* \+ `support`.
	* \+ `authors`: Добавлены недостающие ссылки.


## Версия 2.6.1 (2021-02-07)
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.41 (не тестировался с более ранними версиями).
* \+ `\ddSendFeedback\Sender\Sender::__construct`: Менее хрупкий код, `\DDTools\ObjectTools::extend` используется вместо `array_merge`.
* \* Сниппет: `\DDTools\ObjectTools::convertType` используется вместо `\ddTools::encodedStringToArray`.


## Версия 2.6 (2021-01-18)
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.32.
* \+ Параметры → `senders`: Также может быть задан, как нативный PHP объект или массив (например, для вызовов через `$modx->runSnippet`).
* \+ REAMDE:
	* \+ Описание результатов сниппета.
	* \+ Использует.
	* \+ Документация.
	* \+ Ссылки.
	* \+ Улучшения стиля.
* \+ CHANGELOG.
* \+ CHANGELOG_ru.
* \+ Composer.json:
	* \+ `keywords`: Дополнительные ключевые слова.
	* \+ `homepage`.
	* \+ `authors`.
	* \+ `require`.


## Версия 2.5 (2019-12-15)
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.25.
* \+ Параметры → `senders->customhttprequest`: Новый сендер.
* \* Параметры → `senders->telegram->textMarkupSyntax`: Переименован из `senders->telegram->messageMarkupSyntax` с обратной совместимостью.
* \* `\ddTools::$modx` используется вместо `$modx` во всех методах.
* \+ `\ddSendFeedback\Sender\`: Добавлена проверка обязательных параметров.
* \+ `\ddSendFeedback\Sender\`: Если обязательные параметры не заданы или заданы пустыми, не будет вызывана ошибка PHP. Чувствуйте себя свободно и не беспокойтесь об этом.
* \* `\ddSendFeedback\Sender\Sender::__construct`: Используется `DDTools\BaseClass::setExistingProps`.
* \- `\ddSendFeedback\Sender\Sender::includeSenderByName`: Метод удалён. Используется `\DDTools\BaseClass::createChildInstance` вместо него.
* \+ `\ddSendFeedback\Sender\Sender::initPostPlaceholders`: Поля POST обрабатываются с помощью `nl2br` только если `$this->textMarkupSyntax` == `'html'`.
* \+ Composer.json.


## Версия 2.4 (2019-06-21)
* \* Внимание! Требуется (MODX)EvolutionCMS.snippets.ddMakeHttpRequest >= 1.4.
* \+ Senders → Telegram: Добавлена возможность работы через прокси сервер (см. параметр `proxy`).


## Версия 2.3 (2018-10-09)
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.23.
* \+ Сендеры → Sms.ru: Добавлен параметр  `from`.


## Версия 2.2 (2018-03-22)
* \+ Сендеры → Telegram: Добавлены параметры  `messageMarkupSyntax` и `disableWebPagePreview`.


## Версия 2.1 (2018-03-21)
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.21.
* \* Сендеры → Email: Исправлены проблемы с отправкой на несколько ящиков.
* \+ Добавлена возможность отправки сообщения в канал Telegram.
* \+ Добавлена возможность отправки SMS сообщения через сервис Sms.ru.


## Версия 2.0 (2017-02-06)
* \* Внимание! Обратная совместимость нарушена!
* \* Внимание! Требуется (MODX)EvolutionCMS.snippets.ddMakeHttpRequest >= 1.3.
* \+ Добавлена возможность отправки сообщений в канал Slack.
* \+ Добавлена возможность отправлять сообщения через несколько сендеров одновременно (в данный момент в канал Slack и на email).


## Версия 1.11 (2016-10-30)
* \* Внимание! Требуется (MODX)EvolutionCMS >= 1.1.
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.16.
* \+ Добавлена поддержка указания шаблона письма без чанка, через префикс `@CODE:`.
* \* Пустые плэйсхолдеры удаляются из шаблона письма перед финальным парсингом (в котором запускаются снипппеты и т. п.).


## Версия 1.5 (2011-08-18)
* \+ Параметры → `fromField`: Новый параметр. Из него берётся элемент массива `$_POST` с именем отправителя, перекрывает параметр `from`.


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>