<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class BrandMapper. Brand Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/BrandMapper.php
 */
class BrandMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'brand';

    /**
     * Load backend data by passing date
     *
     * @const LOAD
     */
    const LOAD = "SELECT id, brand.brand AS name, brand.text AS description, brand.status
						FROM catalogue_brands brand
							WHERE brand.last_update >= ?";

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
		return $this->setParams($params, ...$param) ;
	}

    /**
     * Load data from backend Shop
     *
     * @throws \Exception
     * @return array
     */
    public function load() {
	    return parent::load();
    }

	/**
	 * Load data from backend Shops
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function loadBrands() {
		$this->query = self::LOAD;
		return $this->setQueryParams($this->getSyncDate())->load();
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