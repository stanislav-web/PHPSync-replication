<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;

/**
 * Class PriceMapper. Price Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/PriceMapper.php
 */
class PriceMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'prices';

    /**
     * Load frontend data by passing date
     *
     * @const LOAD_PRICE_IDS
     */
    const LOAD_PRICE_IDS = "SELECT GROUP_CONCAT(DISTINCT shops.price_id SEPARATOR ',') AS price_id FROM shops";

    /**
     * Remove unused prices from table `prices`
     *
     * @const DELETE_UNUSED_PRICES
     */
    const DELETE_UNUSED_PRICES = "DELETE FROM prices WHERE `product_id` IN (SELECT prod.id
                                         FROM `products` prod
                                         WHERE `published` = 0 GROUP BY prod.id)";

    /**
     * Load using database
     */
    protected function useDb() {
        $this->db = $this->useFront();
    }

    /**
     * Load data from frontend
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
    public function loadPriceIds() {
        $this->query = self::LOAD_PRICE_IDS;
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

    /**
     * Unload prices params into `prices`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadPrices(array $data) {
        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;
    }

    /**
     * Remove prices from frontend
     *
     * @return int
     */
    public function removeUnusedPrices() {
        $this->query = self::DELETE_UNUSED_PRICES;
        return $this->execute( $this->query);
    }

}