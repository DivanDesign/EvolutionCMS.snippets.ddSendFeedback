<?php
namespace ddSendFeedback\Sender\Email;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$to = [],
		$from = '',
		$subject = '',
		$fileInputNames = [];
	
	public function __construct($params = []){
		//Call base constructor
		parent::__construct($params);
		
		//Comma separated string support
		if (!is_array($this->to)){
			$this->to = explode(',', $this->to);
		}
	}
	
	/**
	 * send
	 * @version 1.0.1 (2017-04-16)
	 * 
	 * @desc Send emails.
	 * 
	 * @return $result {array} — Returns the array of email statuses.
	 * @return $result[i] {0|1} — Status.
	 */
	public function send(){
		$sendMailParams = [
			'to' => $this->to,
			'text' => $this->text,
			'subject' => $this->subject,
		];
		
		if(!empty($this->fileInputNames)){
			$sendMailParams['fileInputNames'] = explode(',', $this->fileInputNames);
		}
		
		if (!empty($this->from)){
			$sendMailParams['from'] = $this->from;
		}
		
		return \ddTools::sendMail($sendMailParams);
	}
}
?>