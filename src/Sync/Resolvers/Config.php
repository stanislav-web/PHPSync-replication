<?php
namespace Sync\Resolvers;

use Sync\Exceptions\ConfigException;

/**
 * Class Config. Configuration storage
 *
 * @package Sync\Resolvers
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Resolvers/Config.php
 */
class Config extends \SplObjectStorage {

    /**
     * Attach configuration
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->attach($this, $config);
    }

    /**
     * Get configuration
     *
     * @param string $param
     * @throws \Sync\Exceptions\ConfigException
     * @return mixed
     */
    public function get($param) {

        $config = $this->offsetGet($this);

        if(isset($config[$param]) === false) {
            throw new ConfigException('Could not find config param: '.$param);
        }

        return $config[$param];
    }
}