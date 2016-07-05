<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class Tag. Tag entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Tag.php
 */
class Tag extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'TagMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $backendMapper = $this->getBackendMapper();

        $this->prepare([
            'tags'  => $backendMapper->loadTags(),
            'sizes' => $backendMapper->loadSizes(),
            'types' => $backendMapper->loadTypes(),
        ]);
    }

    /**
     * Prepare entity data
     *
     * @return mixed
     */
    public function prepare(array $data)
    {
        $prepareData = [];

        $prepareData['add']['sizes'] =  (empty($data['sizes']) === false) ? $data['sizes'] : [];
        $prepareData['add']['types'] =  (empty($data['types']) === false) ? $data['types'] : [];
        $prepareData['add']['tags'] =  (empty($data['tags']) === false) ? $data['tags'] : [];

        $this->unload($prepareData);
    }

    /**
     * Make entity report
     *
     * @return mixed
     */
    public function unload(array $data)
    {
        if(empty($data) === false) {
            $frontendMapper = $this->getFrontendMapper();

            $data = $frontendMapper->callTransaction(function() use ($frontendMapper, $data) {

                return [
                    'addedTags'    => $frontendMapper->unloadTags($data['add']['tags']),
                    'addedSizes'   => $frontendMapper->unloadSizes($data['add']['sizes']),
                    'addedTypes'   => $frontendMapper->unloadTypes($data['add']['types']),
                ];
            });

            $message = Message::success('tags', [
                $data['addedTags'],
                $data['addedSizes'],
                $data['addedTypes'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}