<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;

/**
 * Class BuyMapper. Buy Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/BuyMapper.php
 */
class BuyMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'buy';

    /**
     * Load frontend data by passing date
     *
     * @const LOAD_SHOP_IDS
     */
    const LOAD_SHOP_IDS = "SELECT GROUP_CONCAT(DISTINCT shops.id SEPARATOR ',') AS shop_id FROM shops";

    /**
     * Remove unused prices from table `prices`
     *
     * @const DELETE_UNUSED_BUY
     */
    const DELETE_UNUSED_BUY = "DELETE FROM buy WHERE `id` IN (SELECT prod.id
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
    public function loadShopIds() {
        $this->query = self::LOAD_SHOP_IDS;
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
    public function unloadBuy(array $data) {
        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;
    }

    /**
     * Remove prices from frontend
     *
     * @return int
     */
    public function removeUnusedBuy() {
        $this->query = self::DELETE_UNUSED_BUY;
        return $this->execute( $this->query);
    }

}