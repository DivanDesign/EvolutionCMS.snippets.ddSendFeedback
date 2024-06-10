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
	 * @version 1.1.1 (2024-06-10)
	 * 
	 * @desc Send emails.
	 * 
	 * @return $result {array} — Returns the array of email statuses.
	 * @return $result[i] {0|1} — Status.
	 */
	public function send(){
		$errorData = (object) [
			'isError' => true,
			//Only 19 signs are allowed here in MODX event log :|
			'title' => 'Check required parameters',
			'message' => '',
		];
		
		$result = [];
		
		if ($this->canSend){
			$errorData->title = 'Sending error';
			
			$sendParams = (object) [
				'to' => $this->to,
				'text' => $this->text,
				'subject' => $this->subject,
			];
			
			if(!empty($this->fileInputNames)){
				$sendParams->fileInputNames = explode(
					',',
					$this->fileInputNames
				);
			}
			
			if (!empty($this->from)){
				$sendParams->from = $this->from;
			}
			
			$result = \ddTools::sendMail($sendParams);
			
			$errorData->isError = in_array(
				0,
				$result
			);
			
			if ($errorData->isError){
				$errorData->message =
					'<p>Sending result:</p><pre><code>'
						. var_export(
							$result,
							true
						)
					. '</code></pre>'
				;
			}
		}
		
		//Log errors
		if ($errorData->isError){
			$errorData->message .=
				'<p>$this:</p><pre><code>'
					. var_export(
						$this,
						true
					)
				. '</code></pre>'
			;
			
			\ddTools::logEvent([
				'message' => $errorData->message,
				'source' => 'ddSendFeedback → Email: ' . $errorData->title,
				'eventType' => 'error',
			]);
		}
		
		return $result;
	}
}
?>
