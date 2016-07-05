<?php
namespace Sync\Resolvers;

/**
 * Class Shutdown. Shutdown fatal handler
 *
 * @package Sync\Resolvers
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Resolvers/Shutdown.php
 */
class Shutdown {

    /**
     * Error handler
     */
    public static function errorHandler() {

        if((is_null($error = error_get_last()) === false)) {

            if (ENV != 'production') {

                try {
                    throw new \RuntimeException($error['message'].' : '.$error['file'].' : '.$error['line']);
                }
                catch(\RuntimeException $e) {
                    die(\Sync\Development\Message::error($e->getMessage()));
                }
            }
        }
    }

}