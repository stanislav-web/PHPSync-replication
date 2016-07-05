<?php
namespace Sync\Development;

/**
 * Class Debugger. Debug application
 *
 * @package Sync\Development
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Development/Debugger.php
 */
class Debugger {

    /**
     * Timer
     *
     * @var int|float
     */
    private static $time = 0;

    /**
     * Memory
     *
     * @var int|float
     */
    private static $memory = 0;

    /**
     * Initialize application start mode
     */
    public function __construct() {

        if (PHP_SAPI !== 'cli') {
            echo 'Warning: Script should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
        }

        switch(ENV) {
            case 'production':
                ini_set('display_errors', 'Off');
                ini_set('error_reporting', '0');
            break;

            case 'testing':
                ini_set('display_errors', 'On');
                ini_set('error_reporting', E_ALL & ~E_NOTICE);
            break;

            default :
                ini_set('display_errors', 'On');
                ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_WARNING);
        }

        self::$memory = memory_get_usage();
    }

    /**
     * Start timer
     */
    public function startTime() {
        self::$time = microtime(true);
    }

    /**
     * Get elapsed time
     */
    public static function getElapsedTime() {
        return round(microtime(true) - self::$time, 3);
    }

    /**
     * Get memory usage script
     *
     * @return float
     */
    public static function getMemoryUsage() {
        return self::formatBytes(round(memory_get_usage() - self::$memory, 3));
    }


    /**
     * Format byte code to human understand
     *
     * @param int $bytes number of bytes
     * @param int $precision after comma numbers
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2) {
        $size = array('bytes', 'kb', 'mb', 'gb', 'tb', 'pb', 'eb', 'zb', 'yb');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.".$precision."f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }
}