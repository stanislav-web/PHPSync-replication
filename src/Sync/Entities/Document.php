<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class Document. Document entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Document.php
 */
class Document extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'DocumentMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $frontendMapper = $this->getFrontendMapper();
        $siteIdsFront = current($frontendMapper->loadSiteIds())['site_id'];

        $backendMapper = $this->getBackendMapper();
        $documents = $backendMapper->setQueryParams(
            $siteIdsFront,
            $backendMapper->getSyncronizeDate(),
            $backendMapper->getSyncronizeDate(),
            $backendMapper->getSyncronizeDate()
        )->loadDocuments();

        if(ENV != 'production') {
            echo Message::success('load', [count($documents)]);
        }

        $this->prepare($documents);
    }

    /**
     * Prepare entity data
     *
     * @return mixed
     */
    public function prepare(array $data)
    {
        foreach($data as &$value) {

            $value['ru'] = trim(str_replace("'", '', $value['ru']));
            $value['ua'] = (empty($value['ua']) === false) ? trim(str_replace("'", '', $value['ua'])) : null;
            $value['en'] = (empty($value['en']) === false) ? trim(str_replace("'", '', $value['en'])) : null;

            $value['ru'] = trim(str_replace('\"', '"', $value['ru']));

        }

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

                    'addedDocuments' => $frontendMapper->unloadDocuments($data),
                    'removedDocuments' => $frontendMapper->removeUnusedDocuments(),
                ];
            });

            $message = Message::success('delivery', [
                $data['removedDocuments'],
                $data['addedDocuments'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}