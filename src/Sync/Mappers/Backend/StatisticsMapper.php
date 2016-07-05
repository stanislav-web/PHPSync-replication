<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class StatisticsMapper. Banner Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/StatisticsMapper.php
 */
class StatisticsMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'statistics';

	/**
	 * Load backend data by passing date
	 *
	 * @const LOAD_PUBLISHED_ITEMS
	 */
	const LOAD_PUBLISHED_ITEMS = "SELECT count(DISTINCT(cat.cat_id)) as `count` FROM v6_z95.catalogue cat
									LEFT JOIN catalogue_categories_items cci ON (cci.item_id = cat.cat_id)
									LEFT JOIN catalogue_categories cc ON (cc.id = cci.cat_id)
									LEFT JOIN catalogue_categories_join_shop AS shop FORCE INDEX (`category`) ON (shop.category = cc.id)
									WHERE cat.cat_status = 1 && cat.photos != ''
									&& shop.`shop` IN (%s)";

	/**
	 * Load backend data by passing date
	 *
	 * @const LOAD_COUNT_ITEMS
	 */
	const LOAD_COUNT_ITEMS = "SELECT  SUM(s.count)  as `count`
								FROM catalogue_storage AS s
								LEFT JOIN (
									SELECT DISTINCT(cat.cat_id) FROM catalogue cat
									LEFT JOIN catalogue_categories_items cci ON (cci.item_id = cat.cat_id)
									LEFT JOIN catalogue_categories cc ON (cc.id = cci.cat_id)
									LEFT JOIN catalogue_categories_join_shop AS shop ON (shop.category = cc.id)
									WHERE cat.cat_status = 1 && cat.photos != ''
									&& shop.`shop` IN (%s)
								) AS cat ON (cat.cat_id = s.cat_id)
								WHERE cat.cat_id IS NOT NULL && s.count > 0 && s.store_id = 2";

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
	    return parent::load();
    }

	/**
	 * Load data from backend
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function loadPublishedItems() {
		$this->query = self::LOAD_PUBLISHED_ITEMS;
		return $this->load();
	}
	/**
	 * Load data from backend
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function loadCountItems() {
		$this->query = self::LOAD_COUNT_ITEMS;
		return $this->load();
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