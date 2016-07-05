<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class Shop. Shop entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Shop.php
 */
class Shop extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'ShopMapper';

    /**
     * Load transfer properties
     */
    public function load()
    {
        $backendMapper = $this->getBackendMapper();
        $data = $backendMapper->loadShops();

	    if(ENV != 'production') {
		    echo Message::success('load', [count($data)]);
	    }

	    $this->prepare($data);
    }

    /**
     * Prepare entity data
     *
     * @param array $data
     * @return null|void
     */
    public function prepare(array $data)
    {
		$this->unload($data);
    }

    /**
     * Unload data to frontend
     *
     * @param array $data
     * @return null
     */
	public function unload(array $data = [])
	{
		if(empty($data) === false) {
			$frontendMapper = $this->getFrontendMapper();
			$rows = $frontendMapper->unload($data);

            $message = Message::success('unload', [$rows, $frontendMapper::TABLE]);
            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
		}

		$this->getBackendMapper()->setSyncronizeDate();
	}
}