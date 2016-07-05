<?php
namespace Sync\Development;

use Sync\Resolvers\APIClient;

class Notification
{

	/**
	 * Current configurations
	 *
	 * @var array
	 */
	private $config = [];

	/**
	 * Set configurations
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->config = $config;
	}

	/**
	 * @param string $recipients
	 * @param string $subject
	 * @param string $message
	 *
	 * @return null
	 */
	public function sendMail($subject = '', $message = '', $recipients = '') {
		$mailConfig = $this->config;

		$params = [
			'to' => (!empty($recipients)) ? $recipients : $this->config['recipients'],
			'subject' => $subject,
			'message' => $message,
		];

		$APIClient = new APIClient($mailConfig['mail']);
		return $APIClient->call($mailConfig['mail']['method'], ['params' => $params]);

	}

}