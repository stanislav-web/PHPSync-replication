<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;

/**
 * Class PaymentMapper. Buy Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/PaymentMapper.php
 */
class PaymentMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'shops_payments';

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
     * Unload payments params into `shops_payments`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadPayments(array $data) {
        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;
    }
}