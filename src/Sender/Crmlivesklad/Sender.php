<?php
namespace ddSendFeedback\Sender\Crmlivesklad;

class Sender extends \ddSendFeedback\Sender\Sender {
	protected
		$login = '',
		$password = '',
		$shopId = '',
		
		$requiredProps = [
			'login',
			'password',
			'shopId',
		],
		
		$requestResultParams = [
			//LiveSklad API returns an object like `{"error": {"statusCode": 401, name: "Error",  message: "Access denied"}}` but any object will equal `true`
			'checkValue' => true,
			'isCheckTypeSuccess' => false,
			'checkPropName' => 'error',
			'errorMessagePropName' => 'error.message',
			
			'isObject' => true,
		]
	;
	private
		/**
		 * @property $urls {stdClass}
		 */
		$urls = [
			'auth' => 'https://api.livesklad.com/auth',
			'orders' => 'https://api.livesklad.com/shops/[+shopId+]/orders',
		],
		
		/**
		 * @property $authTokenData {stdClass}
		 * @property $authTokenData->token {string}
		 * @property $authTokenData->expireDate {integer}
		 */
		$authTokenData = [
			'token' => '',
			'expireDate' => 0,
		]
	;
	
	/**
	 * __construct
	 * @version 1.0 (2024-06-04)
	 */
	public function __construct($params = []){
		//Call base constructor
		parent::__construct($params);
		
		$this->urls = (object) $this->urls;
		$this->authTokenData = (object) $this->authTokenData;
		
		$this->urls->orders = \ddTools::parseText([
			'text' => $this->urls->orders,
			'data' => [
				'shopId' => $this->shopId,
			],
		]);
	}
	
	/**
	 * fillAuthToken
	 * @version 1.0 (2024-06-04)
	 * 
	 * @return {void}
	 */
	private function fillAuthToken(): void {
		if (!$this->canSend){
			$this->authTokenData->token = '';
			$this->authTokenData->expireDate = 0;
		}elseif (
			empty($this->authTokenData->token)
			|| $this->authTokenData->expireDate < time() + 10
		){
			$requestResult = \DDTools\Snippet::runSnippet([
				'name' => 'ddMakeHttpRequest',
				'params' => [
					'url' => $this->urls->auth,
					'method' => 'post',
					'postData' => [
						'login' => $this->login,
						'password' => $this->password,
					],
				]
			]);
			
			$requestResult = \DDTools\ObjectTools::convertType([
				'object' => $requestResult,
				'type' => 'objectStdClass',
			]);
			
			if (
				!empty(
					\DDTools\ObjectTools::getPropValue([
						'object' => $requestResult,
						'propName' => 'token',
					])
				)
			){
				$this->authTokenData->token = $requestResult->token;
				$this->authTokenData->expireDate = strtotime($requestResult->expireDate);
			}else{
				$this->authTokenData->token = '';
				$this->authTokenData->expireDate = 0;
			}
		}
	}
	
	/**
	 * send
	 * @version 1.1.7 (2024-06-17)
	 * 
	 * @desc Creates an order in LiveSklad.com.
	 * 
	 * @return $result {array} — Returns the array of send status.
	 * @return $result[0] {0|1} — Status.
	 */
	public function send(){
		$errorData = (object) [
			'isError' => true,
			//Only 19 signs are allowed here in MODX event log :|
			'title' => 'Check required parameters',
			'message' => '',
		];
		
		if ($this->canSend){
			$errorData->title = 'Unexpected API error';
			
			$this->fillAuthToken();
			
			if (empty($this->authTokenData->token)){
				$errorData->title = 'Authorization failed';
			}else{
				$requestResult = $this->send_parseRequestResult(
					$this->send_request()
				);
				
				$errorData->isError = $requestResult->isError;
				
				if ($errorData->isError){
					if (!\ddTools::isEmpty($this->requestResultParams->errorMessagePropName)){
						//Try to get error title from request result
						$errorData->title = \DDTools\ObjectTools::getPropValue([
							'object' => $requestResult->data,
							'propName' => $this->requestResultParams->errorMessagePropName,
							'notFoundResult' => $errorData->title,
						]);
					}
					
					$errorData->message =
						'<p>Request result:</p><pre><code>'
							. var_export(
								$requestResult->data,
								true
							)
						. '</code></pre>'
					;
				}
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
				'source' =>
					'ddSendFeedback → '
					. static::getClassName()->namespaceShort
					. ': '
					. $errorData->title
				,
				'eventType' => 'error',
			]);
		}
		
		return [
			0 => !$errorData->isError
		];
	}
	
	/**
	 * send_request_prepareParams
	 * @version 1.0.1 (2024-06-11)
	 * 
	 * @return $result {\stdClass}
	 */
	protected function send_request_prepareParams(): \stdClass {
		return (object) [
			'url' => $this->urls->orders,
			'method' => 'post',
			'headers' => [
				'Authorization: ' . $this->authTokenData->token
			],
			'postData' => $this->text,
		];
	}
}
?>