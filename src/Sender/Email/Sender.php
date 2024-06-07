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
	 * send
	 * @version 1.0.3 (2024-06-07)
	 * 
	 * @desc Send emails.
	 * 
	 * @return $result {array} — Returns the array of email statuses.
	 * @return $result[i] {0|1} — Status.
	 */
	public function send(){
		$result = [];
		
		if ($this->canSend){
			$sendParams = [
				'to' => $this->to,
				'text' => $this->text,
				'subject' => $this->subject,
			];
			
			if(!empty($this->fileInputNames)){
				$sendParams['fileInputNames'] = explode(
					',',
					$this->fileInputNames
				);
			}
			
			if (!empty($this->from)){
				$sendParams['from'] = $this->from;
			}
			
			$result = \ddTools::sendMail($sendParams);
		}
		
		return $result;
	}
}
?>
