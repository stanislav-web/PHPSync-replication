<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;

/**
 * Class BannerMapper. Banner Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/BannerMapper.php
 */
class BannerMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'banners';


    /**
     * Load frontend data by passing date
     *
     * @const LOAD_SHOP_IDS
     */
    const LOAD_SITE_IDS = "SELECT site_id  FROM shops";

    /**
     * Load using database
     */
    protected function useDb() {
        $this->db = $this->useFront();
    }

    /**
     * Load data from backend Shop
     */
	public function load(){
		return parent::load();
	}

    /**
     * Load data from frontend Categories
     *
     * @throws \Exception
     * @return array
     */
    public function loadSiteIds() {
        $this->query = self::LOAD_SITE_IDS;
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
		    $this->table = self::TABLE;
		    $rows = $this->insertBatch($this->table, $data);
		    return $rows;
	    }
	    catch(DbException $e) {
		    throw new \Exception($e->getMessage());
	    }
    }

    /**
     * Remove banners from frontend
     *
     * @param array $data
     * @return int
     */
    public function removeBanners() {

        //$rows = $this->remove(self::TABLE, ['*' => 'all']);
	    $rows = $this->remove(self::TABLE, ['status' => '0']);
        return $rows;
    }


}