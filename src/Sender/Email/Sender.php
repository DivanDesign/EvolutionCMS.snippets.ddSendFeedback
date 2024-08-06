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
	 * construct_prepareProps
	 * @version 1.0.1 (2024-08-06)
	 *
	 * @return {void}
	 */
	protected function construct_prepareProps(){
		// Comma separated string support
		if (is_string($this->to)){
			$this->to = explode(
				',',
				$this->to
			);
		}
		
		// Delete invalid emails
		foreach(
			$this->to
			as $itemIndex
			=> $itemlValue
		){
			if (
				!filter_var(
					$itemlValue,
					FILTER_VALIDATE_EMAIL
				)
			){
				unset($this->to[$itemIndex]);
			}
		}
		
		parent::construct_prepareProps();
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