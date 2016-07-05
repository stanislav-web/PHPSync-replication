<?php
namespace Sync\Exceptions;

use Sync\Development\Logger;
use Sync\Development\Notification;

/**
 * Class BaseException. Exception handler
 *
 * @package Sync\Exceptions
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Exceptions/BaseException.php
 */
class BaseException extends \RuntimeException {

    /**
     * @const DELIMITER default
     */
    const DELIMITER = ' : ';

    /**
     * Constructor
     *
     * @param string $message If no message is given default from child
     * @param string $code Status code, default from child
     */
    public function __construct($message = null) {

        global $config;

        $message = static::TYPE.self::DELIMITER.$message; // use as late state binding

        $target = getopt('t:target:');
        $target = $target[key($target)];


        $logger = new Logger($config['entities'][strtolower($target)]['logger']);
        $logger->critical($message);

	    $notification =  new Notification($config['notification']);
	    $notification->sendMail('Error: exception was occurred in Syncronize of Back & Front', $message);

        parent::__construct($message, static::CODE);
    }
}