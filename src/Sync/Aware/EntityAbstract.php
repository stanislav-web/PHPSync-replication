<?php
namespace Sync\Aware;

use Sync\Exceptions\EntityException;
use Sync\Development\Logger;
use Sync\Development\Notification;

/**
 * Class EntityAbstract. Entity interface
 *
 * @package Sync\Aware
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Aware/EntityAbstract.php
 */
abstract class EntityAbstract {

    /**
     * Configurations
     *
     * @var \Sync\Resolvers\Config $config
     */
    private $config = null;

    /**
     * Action logger
     *
     * @var \Sync\Development\Logger $logger
     */
    protected $logger = null;

    /**
     * Action notification
     *
     * @var \Sync\Development\Notification $notification
     */
    protected $notification = null;

    /**
     * Initialize configuration
     *
     * @param \Sync\Resolvers\Config $config
     */
    public function __construct(\Sync\Resolvers\Config $config) {

        if($this->config === null) {
            // always use lazy
            $this->config = $config;
        }
    }

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    abstract public function load();

    /**
     * Prepare entity data
     *
     * @param array $data
     * @return mixed
     */
    abstract public function prepare(array $data);

    /**
     * Unload data to frontend
     *
     * @param array $data
     * @return mixed
     */
    abstract public function unload(array $data);

    /**
     * Get Mapper
     *
     * @return \Sync\Aware\MapperAbstract
     */
    protected function getMapper($mapper) {

        $object = "Sync\\Mappers\\$mapper\\".static::MAPPER;

        if(class_exists($object) === true) {
            return new $object($this->config);
        }
        else {
            throw new EntityException('Undefined mapper called');
        }
    }

    /**
     * Get Backend Mapper
     *
     * @return \Sync\Aware\MapperAbstract
     */
    protected function getBackendMapper() {
        $mapper = $this->getMapper('Backend');
        return $mapper;
    }

    /**
     * Get Frontend Mapper
     *
     * @return \Sync\Aware\MapperAbstract
     */
    protected function getFrontendMapper() {
        $mapper = $this->getMapper('Frontend');
        return $mapper;
    }


    /**
     * Load logger interface
     *
     * @return Logger
     */
    protected function getLogger() {

        if($this->logger === null) {
            $entity = strtolower(
                (new \ReflectionClass(get_class($this)))->getShortName()
            );
            $this->logger = new Logger($this->config->get('entities')[$entity]['logger']);
        }
        return $this->logger;
    }

    /**
     * Load Notification interface
     *
     * @return Notification
     */
    protected function getNotification() {

        if($this->notification === null) {
            $this->notification = new Notification($this->config->get('notification'));
        }
        return $this->notification;
    }

}