<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class Delivery. Delivery entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Delivery.php
 */
class Delivery extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'DeliveryMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $backendMapper = $this->getBackendMapper();
        $deliveries = $backendMapper->setQueryParams(
            $backendMapper->getSyncronizeDate()
        )->loadDeliveries();

        if(ENV != 'production') {
            echo Message::success('load', [count($deliveries)]);
        }

        $this->prepare($deliveries);
    }

    /**
     * Prepare entity data
     *
     * @return mixed
     */
    public function prepare(array $data)
    {
        $this->unload($data);
    }

    /**
     * Unload data to frontend
     *
     * @return mixed
     */
    public function unload(array $data = [])
    {
        if(empty($data) === false) {

            $frontendMapper = $this->getFrontendMapper();

            $data = $frontendMapper->callTransaction(function() use ($frontendMapper, $data) {

                return [

                    'addedDeliveries' => $frontendMapper->unloadDeliveries($data),
                    'removedDeliveries' => $frontendMapper->removeUnusedDeliveries(),
                ];
            });

            $message = Message::success('delivery', [
                $data['removedDeliveries'],
                $data['addedDeliveries'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}