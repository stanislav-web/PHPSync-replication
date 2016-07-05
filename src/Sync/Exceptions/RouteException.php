<?php
namespace Sync\Exceptions;

/**
 * Class RouteException. Route params exception handler
 *
 * @package Sync\Exceptions
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Exceptions/RouteException.php
 */
class RouteException extends BaseException {

    /**
     * @const TYPE exception type as object name raised an exception
     */
    const TYPE = 'RouteException';

    /**
     * @const CODE exception code
     */
    const CODE = 500;

    /**
     * Constructor
     *
     * @param string $message If no message is given default from child
     * @param string $code Status code, default from child
     */
    public function __construct($message) {
        parent::__construct($message);
    }
}