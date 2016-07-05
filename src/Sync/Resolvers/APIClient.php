<?php
namespace Sync\Resolvers;

use Sync\Exceptions\APIException;

class APIClient
{

	/**
	 * @var string
	 */
	private $version = '2.0';

	/**
	 * @var string
	 */
	private $apiUrl;

	/**
	 * Token tracking
	 * @var boolean
	 */
	private $apiToken = null;

	/**
	 * @var bool
	 */
	private $verifySsl = false;

	/**
	 * Default to a 300 second timeout on server calls
	 * @var int
	 */
	private  $timeout = 200;

	/**
	 * @var null
	 */
	private $requestData = null;

	/**
	 * @var null
	 */
	private $responseData = null;

	/**
	 * @var string
	 */
	private $url = '';

	/**
	 * Construct function
	 */
	function __construct($config) {
        $this->setApiUrl($config['url']);
        $this->setApiToken($config['token']);
	}

	/**
	 * @param $apiToken
	 *
	 * @return $this
	 */
	public function setApiToken($apiToken)
	{
		$this->apiToken = $apiToken;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getApiToken()
	{
		return $this->apiToken;
	}

	/**
	 * @param $apiUrl
	 *
	 * @return $this
	 */
	public function setApiUrl($apiUrl)
	{
		$this->apiUrl = $apiUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getApiUrl()
	{
		return $this->apiUrl;
	}

	/**
	 * @param $verifySsl
	 *
	 * @return $this
	 */
	public function setVerifySsl($verifySsl)
	{
		$this->verifySsl = $verifySsl;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getVerifySsl()
	{
		return $this->verifySsl;
	}

	/**
	 * @param $method
	 * @param $params
	 *
	 * @return $this
	 */
	public function setRequestData($method,$params)
	{
		array_unshift($params,$this->getApiToken());
		$requestData = [
			'method' 	=> $method,
			'params' 	=> $params
		];
		$this->requestData = json_encode($requestData);
		return $this;
	}

	/**
	 * @return null
	 */
	public function getRequestData()
	{
		return $this->requestData;
	}

	/**
	 * @param $response
	 *
	 * @return $this
	 */
	public function setResponseData($response)
	{
		$this->responseData = json_decode($response, true);
		return $this;
	}

	/**
	 * @return null
	 */
	public function getResponseData()
	{
		return $this->responseData;
	}

	/**
	 * @param string $url
	 *
	 * @return $this
	 */
	public function setUrl($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param $method
	 *
	 * @return array
	 * @throws APIException
	 */
	private function prepareResult($method) {
		$responseData = $this->getResponseData();
		if(isset($responseData['error']) ) {
			throw new APIException($method.': '.$responseData['error']);
		} else {
			return $responseData['result'];
		}
	}

	/**
	 * Connect to the server using CURL
	 *
	 * @return null
	 */
	private function useCurl() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getUrl());
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getVerifySsl());
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getRequestData());
		$this->setResponseData(curl_exec($ch));
		curl_close($ch);

		return $this->prepareResult('curl');
	}

	/**
	 * Connect to the server using Stream
	 *
	 * @return null
	 */
	private function useStream() {
		$this->setResponseData(
			file_get_contents($this->getUrl(), null, stream_context_create(array(
				'http' => array(
					'protocol_version' => 1.1,
					'user_agent'       => 'PHP-MCAPI/'. $this->version,
					'method'           => 'POST',
					'header'           => "Content-type: application/json\r\n".
						"Connection: close\r\n" .
						"Content-length: " . strlen($this->getRequestData()) . "\r\n",
					'content'          => $this->getRequestData(),
				),
			)))
		);

		return $this->prepareResult('stream');
	}

	/**
	 *
	 *
	 * $APIClient->setApiUrl($url)->call($method, []);
	 *
	 * @return null
	 * @throws APIException
	 */
	public function call($method, array $params = []) {
		try {
			$this->setUrl($this->getApiUrl());

			$this->setRequestData($method,$params);

			if (function_exists('curl_init') && function_exists('curl_setopt')) {
				return $this->useCurl();
			} else {
				return $this->useStream();
			}

		} catch (\Exception $e) {
			throw new APIException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
		}
	}

}

