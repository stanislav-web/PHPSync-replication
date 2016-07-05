<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;
use Sync\Resolvers\APIClient;

/**
 * Class HotlineMapper. Banner Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/HotlineMapper.php
 */
class HotlineMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'hotline';

    /**
     * Load using database
     */
    protected function useDb() {
        $this->db = $this->useBack();
    }

	/**
	 * Setting up db parameters
	 *
	 * @param array $params
	 * @param array ...$param
	 * @return CategoryMapper
	 */
	public function setQueryParams($params, ...$param) {
        return parent::setParams($params, ...$param);
	}

    /**
     * Load data from backend
     *
     * @throws \Exception
     * @return array
     */
    public function load() {
        $this->getParams();
	    return parent::load();
    }

	/**
	 * Load data from backend (use APIClient)
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function loadHotlineData() {
        $config = $this->getEntityConfig()['sync'];

        $this->params = array_merge($config['entities'], current($this->params));
        $APIClient = new APIClient($config);
        $hotlineData = $APIClient->call($config['method'], ['params' => $this->getParams()]);

        return $hotlineData;
	}

    /**
     * Unload data to database
     */
    public function unload(array $data){ }

    /**
     * Get syncronize date
     */
    public function getSyncronizeDate() {
        return $this->getSyncDate();
    }

    /**
     * Set syncronize date
     */
	public function setSyncronizeDate() {
		$this->setSyncDate();
	}
}