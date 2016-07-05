<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;
use Sync\Resolvers\Format;

/**
 * Class StatisticsMapper. Statistics Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/StatisticsMapper.php
 */
class StatisticsMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'Products';

    /**
     * Load frontend data by passing date
     *
     * @const LOAD_SHOP_IDS
     */
    const LOAD_SHOP_IDS = "SELECT GROUP_CONCAT(DISTINCT shops.id SEPARATOR ',') AS shop_id FROM shops";

	/**
     * Load frontend data by passing date
     *
     * @const LOAD_PUBLISHED_ITEMS
     */
    const LOAD_PUBLISHED_ITEMS = "SELECT count(*)  as `count` FROM Shop.products p WHERE p.published = 1 && p.images != ''";

		/**
     * Load frontend data by passing date
     *
     * @const LOAD_COUNT_ITEMS
     */
    const LOAD_COUNT_ITEMS = "SELECT SUM(p.count_sizes)  as `count` FROM Shop.products p WHERE p.published = 1 && p.images != ''";

    const LOAD_PUBLISHED_WITHOUT_SIZES_IN_STOCK_ITEMS = "SELECT id, articul, `published`, filter_size, date_update FROM Shop.products WHERE published = 1 && (count_sizes = 0 || count_sizes IS NULL) order by id desc";

    /**
     * Load using database
     */
    protected function useDb() {
        $this->db = $this->useFront();
    }

    /**
     * Load data from backend Shop
     */
	public function load() {
		return parent::load();
    }

    /**
     * Load data from frontend Categories
     *
     * @throws \Exception
     * @return array
     */
    public function loadShopIds() {
        $this->query = self::LOAD_SHOP_IDS;
        return $this->load();
    }

    /**
     * Load data from frontend Products
     *
     * @throws \Exception
     * @return array
     */
    public function loadPublishedItems() {
        $this->query = self::LOAD_PUBLISHED_ITEMS;
        return $this->load();
    }

    /**
     * Load data from frontend Products
     *
     * @throws \Exception
     * @return array
     */
    public function loadCountItems() {
        $this->query = self::LOAD_COUNT_ITEMS;
        return $this->load();
    }

    /**
     * Load data from frontend Products
     *
     * @throws \Exception
     * @return array
     */
    public function loadPublishedWithoutSizesInStockItems() {
        $this->query = self::LOAD_PUBLISHED_WITHOUT_SIZES_IN_STOCK_ITEMS;
        return $this->load();
    }

    /**
	 * Unload data to database
	 *
	 * @param array $data
	 *
	 * @return int
	 * @throws \Exception
	 */
    public function unload(array $data) {
	    try {
		    $rows = $this->insertBatch($this->table, $data) ;
		    return $rows;
	    }
	    catch(DbException $e) {
		    throw new \Exception($e->getMessage());
	    }
    }

}