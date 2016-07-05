<?php
namespace Sync\Resolvers;

/**
 * Class FormatHelper. Format data helper
 *
 * @package Sync\Resolvers
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Resolvers/Format.php
 */
class Format {

    /**
     * Implode array with handler
     *
     * @param array  $params input params
     * @param string $function handle function
     * @param string $delimiter cut delimiter
     * @return string
     */
    public static function implodeType(array $params, $function = 'intval', $delimiter = ',') {

        if(function_exists($function)) {
            $data = array_map(function($value) use ($function) {
                return $function(strip_tags(trim($value)));
            }, $params);
        }

        return implode($delimiter, $data);
    }

    /**
     * Implode array with handler
     *
     * @param array  $params input params
     * @param string $function handle function
     * @param string $delimiter cut delimiter
     * @return string
     */
    public static function getColumn(array $params, $column) {
        return array_column($params, $column);
    }

}