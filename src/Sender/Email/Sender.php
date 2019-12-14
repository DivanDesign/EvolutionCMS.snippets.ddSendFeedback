<?php
namespace ddSendFeedback\Sender\Email;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$to = NULL,
		$from = '',
		$subject = '',
		$fileInputNames = []
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
	 * @version 1.0.2 (2019-12-14)
	 * 
	 * @desc Send emails.
	 * 
	 * @return $result {array} — Returns the array of email statuses.
	 * @return $result[i] {0|1} — Status.
	 */
	public function send(){
		$result = [];
		
		if ($this->canSend){
			$sendMailParams = [
				'to' => $this->to,
				'text' => $this->text,
				'subject' => $this->subject,
			];
			
			if(!empty($this->fileInputNames)){
				$sendMailParams['fileInputNames'] = explode(
					',',
					$this->fileInputNames
				);
			}
			
			if (!empty($this->from)){
				$sendMailParams['from'] = $this->from;
			}
			
			$result = \ddTools::sendMail($sendMailParams);
		}
		
		return $result;
	}
}
?>
