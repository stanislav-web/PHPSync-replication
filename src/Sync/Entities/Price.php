<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class Price. Price entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Price.php
 */
class Price extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'PriceMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $frontendMapper = $this->getFrontendMapper();
        $priceIdsFront = current($frontendMapper->loadPriceIds())['price_id'];

        $backendMapper = $this->getBackendMapper();
        $prices = $backendMapper->setQueryParams(
                    $backendMapper->getSyncronizeDate(),
                    $backendMapper->getSyncronizeDate(),
                    $backendMapper->getSyncronizeDate(),
                    $priceIdsFront
                )->loadPrices();

        if(ENV != 'production') {
            echo Message::success('load', [count($prices)]);
        }

        $this->prepare($prices);
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

                    'addedPrices' => $frontendMapper->unloadPrices($data),
                    //'removedPrices' => $frontendMapper->removeUnusedPrices(),
                ];
            });

            $message = Message::success('price', [
                $data['addedPrices'],
                0,//$data['removedPrices'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}