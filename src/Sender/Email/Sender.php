<?php
namespace ddSendFeedback\Sender\Email;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$to = [],
		$from = '',
		$subject = '',
		$fileInputNames = [],
		
		$requiredProps = ['to']
	;
	
	/**
	 * __construct
	 * @version 1.0.1 (2019-12-14)
	 */
	public function __construct($params = []){
		//Call base constructor
		parent::__construct($params);
		
		//Comma separated string support
		if (is_string($this->to)){
			$this->to = explode(
				',',
				$this->to
			);
		}
	}
	
	/**
	 * send_request
	 * @version 1.0.2 (2024-06-20)
	 * 
	 * @return $result {array}
	 * @return $result[$i] {0|1}
	 */
	protected function send_request(){
		return \ddTools::sendMail(
			$this->send_request_prepareParams()
		);
	}
	
	/**
	 * send_request_prepareParams
	 * @version 1.0.1 (2024-06-11)
	 *
	 * @return $result {\stdClass}
	 */
	protected function send_request_prepareParams(): \stdClass {
		$result = (object) [
			'to' => $this->to,
			'text' => $this->text,
			'subject' => $this->subject,
		];
		
		if(!empty($this->fileInputNames)){
			$result->fileInputNames = explode(
				',',
				$this->fileInputNames
			);
		}
		
		if (!empty($this->from)){
			$result->from = $this->from;
		}
		
		return $result;
	}
}
?>