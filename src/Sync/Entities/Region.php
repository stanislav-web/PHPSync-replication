<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class Region. Region entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Region.php
 */
class Region extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'RegionMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $backendMapper = $this->getBackendMapper();
        $regions = $backendMapper->setQueryParams(
            $backendMapper->getSyncronizeDate()
        )->loadRegions();

        if(ENV != 'production') {
            echo Message::success('load', [count($regions)]);
        }

        $this->prepare($regions);
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
                    'addedRegions'      => $frontendMapper->unloadRegions($data),
                ];
            });

            $message = Message::success('unload', [
                $data['addedRegions'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}