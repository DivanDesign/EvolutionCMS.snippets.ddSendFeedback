<?php
namespace ddSendFeedback;

class Snippet extends \DDTools\Snippet {
	protected
		$version = '2.7.1',
		
		$params = [
			//Defaults
			'result_titleSuccess' => null,
			'result_titleFail' => null,
			'result_messageSuccess' => null,
			'result_messageFail' => null,
			'senders' => null,
		],
		
		$paramsTypes = [
			'senders' => 'objectArray',
		]
	;
	
	/**
	 * prepareParams
	 * @version 1.0.1 (2024-07-13)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 * 
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		//Call base method
		parent::prepareParams($params);
		
		//Получаем язык админки
		$lang = \ddTools::$modx->getConfig('manager_language');
		
		//Если язык русский
		if(
			$lang == 'russian-UTF8'
			|| $lang == 'russian'
		){
			if (is_null($this->params->result_titleSuccess)){
				$this->params->result_titleSuccess = 'Заявка успешно отправлена';
			}
			if (is_null($this->params->result_titleFail)){
				$this->params->result_titleFail = 'Непредвиденная ошибка =(';
			}
			if (is_null($this->params->result_messageSuccess)){
				$this->params->result_messageSuccess = 'Наш специалист свяжется с вами в ближайшее время.';
			}
			if (is_null($this->params->result_messageFail)){
				$this->params->result_messageFail = 'Во время отправки заявки что-то произошло.<br />Пожалуйста, попробуйте чуть позже.';
			}
		}else{
			if (is_null($this->params->result_titleSuccess)){
				$this->params->result_titleSuccess = 'Message sent successfully';
			}
			if (is_null($this->params->result_titleFail)){
				$this->params->result_titleFail = 'Unexpected error =(';
			}
			if (is_null($this->params->result_messageSuccess)){
				$this->params->result_messageSuccess = 'We will contact you later';
			}
			if (is_null($this->params->result_messageFail)){
				$this->params->result_messageFail = 'Something happened while sending the message.<br />Please try again later.';
			}
		}
		
	}
	
	/**
	 * run
	 * @version 1.0.3 (2024-07-13)
	 * 
	 * @return {string}
	 */
	public function run(){
		$result = new \DDTools\Response();
		
		//Senders is required parameter
		if (\ddTools::isEmpty($this->params->senders)){
			$result->setMeta([
				'success' => false,
			]);
		}else{
			$outputMessages = [
				'titles' => [
					0 => $this->params->result_titleFail,
					1 => $this->params->result_titleSuccess,
				],
				'messages' => [
					0 => $this->params->result_messageFail,
					1 => $this->params->result_messageSuccess,
				],
			];
			
			$sendResults = [];
			
			//Iterate through all senders to create their instances
			foreach(
				$this->params->senders
				as $senderName
				=> $senderParams
			){
				$sender = \ddSendFeedback\Sender\Sender::createChildInstance([
					'name' => $senderName,
					'params' => $senderParams,
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
				$sendResults
				as $sendResults_item
			){
				//Запоминаем
				$sendResults_status = intval($sendResults_item);
				
				//Если не отправлось хоть на один адрес, считаем, что всё плохо
				if ($sendResults_status == 0){
					break;
				}
			}
			
			$result->setMeta([
				'success' => boolval($sendResults_status),
				'message' => [
					'content' => $outputMessages['messages'][$sendResults_status],
					'title' => $outputMessages['titles'][$sendResults_status],
				],
			]);
		}
		
		return $result->toJSON();
	}
}