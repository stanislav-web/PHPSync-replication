<?php
namespace Sync\Development;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Logger class. Log data handler
 *
 * @package Sync\Development
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Development/Logger.php
 */
class Logger extends LogLevel implements LoggerInterface {

    /**
     * Default date format
     *
     * @const DEFAULT_DATE_FORMAT
     */
    const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Default log record format
     *
     * @const DEFAULT_LOG_FORMAT
     */
    const DEFAULT_LOG_FORMAT = '[date][level] message';


    /**
     * Current configurations
     *
     * @var array
     */
    private $config = [];

    /**
     * Stripped console tags
     *
     * @var array $stripped
     */
    private $stripped = ['[0;32m','[0m', PHP_EOL];

    /**
     * Code names
     *
     * @var array $codeName
     */
    public static $codeName = [
        parent::EMERGENCY => 1,
        parent::ALERT     => 2,
        parent::CRITICAL  => 3,
        parent::ERROR     => 4,
        parent::WARNING   => 5,
        parent::NOTICE    => 6,
        parent::INFO      => 7,
        parent::DEBUG     => 8
    ];

    /**
     * Set configurations
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * Get default format of date
     *
     * @return string
     */
    public function getDefaultDateFormat() {
        return self::DEFAULT_DATE_FORMAT;
    }

    /**
     * Get default format of log record
     *
     * @return string
     */
    public function getDefaultLogFormat() {
        return self::DEFAULT_LOG_FORMAT;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function emergency($message, array $context = []) {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function alert($message, array $context = []) {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function critical($message, array $context = []) {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function error($message, array $context = []) {
        $this->log(self::ERROR, $message, $context);
    }
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function warning($message, array $context = []) {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function notice($message, array $context = []) {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function info($message, array $context = []) {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function debug($message, array $context = []) {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Setup logger
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = []) {
        $this->addToLog($level, $message, $context);
    }

    /**
     * Create log file
     *
     * @param string    $file
     * @param int $permissions
     */
    private function createLogFile($file, $permissions = 0666) {

        if (file_exists($file) === false) {
            if (is_dir(dirname($file)) === false) {
                mkdir(dirname($file), 0777, true);
            }
            file_put_contents($file, '');
            chmod($file, $permissions);
        }
    }

    /**
     * Add to log file
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function addToLog($level, $message, array $context = []) {

        // create log file (if not exist)
        $this->createLogFile($this->config['file']);

        // format content
        $content = array_merge([
            'date'      =>  (new \DateTime('now'))->format($this->config['date']),
            'message'   =>  $message,
            'level'     =>  strtoupper($level)
        ], $context);

        // stringify content
        $content = str_ireplace(array_keys($content), array_values($content), $this->config['format'])."\n\n";
        $content = str_replace($this->stripped, ' ', $content);

        if (file_put_contents($this->config['file'], $content.PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
            throw new \RuntimeException('Unable to write to the log file: '.$this->config['file']);
        }
    }
}