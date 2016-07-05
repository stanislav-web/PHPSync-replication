<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class Buy. Buy entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Buy.php
 */
class Buy extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'BuyMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $frontendMapper = $this->getFrontendMapper();
        $shopIdsFront = current($frontendMapper->loadShopIds())['shop_id'];

        $backendMapper = $this->getBackendMapper();
        $buy = $backendMapper->setQueryParams(
                    $backendMapper->getSyncronizeDate(),
                    $shopIdsFront
                )->loadBuy();

        if(ENV != 'production') {
            echo Message::success('load', [count($buy)]);
        }

        $this->prepare($buy);
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

                    'addedBuy' => $frontendMapper->unloadBuy($data),
                    'removedBuy' => $frontendMapper->removeUnusedBuy(),
                ];
            });

            $message = Message::success('buy', [
                $data['addedBuy'],
                $data['removedBuy'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}