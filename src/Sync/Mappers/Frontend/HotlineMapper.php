<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;
use Sync\Resolvers\Format;

/**
 * Class HotlineMapper. Hotline Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/HotlineMapper.php
 */
class HotlineMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'hotline';

    /**
     * Db table count
     *
     * @const TABLE_COUNT
     */
    const TABLE_COUNT = 'hotline_count';

    /**
     * Db table items
     *
     * @const TABLE_ITEMS
     */
    const TABLE_ITEMS = 'hotline_items';

    /**
     * Load frontend data by passing date
     *
     * @const LOAD_SHOP_IDS
     */
    const LOAD_SHOP_IDS = "SELECT GROUP_CONCAT(DISTINCT shops.id SEPARATOR ',') AS shop_id FROM shops";

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
     * Unload hotline last orders into `hotline`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadHotline(array $data) {
        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;
    }

    /**
     * Unload hotline total count orders by shops into `hotline_count`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadHotlineCount(array $data) {

	    $this->table = self::TABLE_COUNT;
        $sql = "INSERT INTO `".$this->table."` (shop_id, region, city, total)
	                VALUES (%d, '%s', '%s', %d)
		                ON DUPLICATE KEY UPDATE
		                `".$this->table."`.`total` =
		                IF (`".$this->table."`.`total` = NULL, %d, `".$this->table."`.`total` + %d)";

        $counter = 0;
        foreach($data as $row) {
            $row['total1'] = $row['total'];
            $row['total2'] = $row['total'];
            $this->db->execute(vsprintf($sql, $row));
            ++$counter;
        }
        return $counter;
    }

    /**
     * Unload hotline items from last 100 orders into `hotline_items`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadHotlineItems(array $data) {
	    $this->table = self::TABLE_ITEMS;
	    $rows = $this->unload($data);
        return $rows;
    }

}