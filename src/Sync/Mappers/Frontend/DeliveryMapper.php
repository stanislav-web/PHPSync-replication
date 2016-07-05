<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;

/**
 * Class DeliveryMapper. Buy Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/DeliveryMapper.php
 */
class DeliveryMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'shops_deliveries';

    /**
     * Remove unused prices from table `shop_delivery`
     *
     * @const DELETE_UNUSED_DELIVERIES
     */
    const DELETE_UNUSED_DELIVERIES = "DELETE FROM shops_deliveries WHERE `status` = 2";

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
     * Unload payments params into `shops_deliveries`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadDeliveries(array $data) {
        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;
    }

    /**
     * Remove prices from frontend
     *
     * @return int
     */
    public function removeUnusedDeliveries() {
        $this->query = self::DELETE_UNUSED_DELIVERIES;
        return $this->execute( $this->query);
    }
}