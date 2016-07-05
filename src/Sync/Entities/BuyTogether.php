<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class BuyTogether. BuyTogether entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/BuyTogether.php
 */
class BuyTogether extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'BuyTogetherMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $backendMapper = $this->getBackendMapper();
        $buy = $backendMapper->setQueryParams(
                    $backendMapper->getSyncronizeDate()
                )->loadBuyTogether();

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

                    'addedBuyTogether' => $frontendMapper->unloadBuyTogether($data),
                    'removedBuyTogether' => 0//$frontendMapper->removeUnusedBuyTogether(),
                ];
            });

            $message = Message::success('buyTogether', [
                $data['addedBuyTogether'],
                $data['removedBuyTogether'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}