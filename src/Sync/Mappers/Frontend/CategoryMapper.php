<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;
use Sync\Resolvers\Format;

/**
 * Class CategoryMapper. Category Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/CategoryMapper.php
 */
class CategoryMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'categories';

    /**
     * Db rel table
     *
     * @const TABLE_REL
     */
    const TABLE_REL = 'category_shop_relationship';

    /**
     * Load frontend data by passing date
     *
     * @const LOAD_SHOP_IDS
     */
    const LOAD_SHOP_IDS = "SELECT GROUP_CONCAT(DISTINCT shops.id SEPARATOR ',') AS shop_id FROM shops";

    /**
     * Load frontend data by passing date
     *
     * @const LOAD_CATEGORY_IDS
     */
    const LOAD_CATEGORY_IDS = "SELECT categories.id FROM categories";

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
    public function loadCategoryIds() {
        $this->query = self::LOAD_CATEGORY_IDS;
        return $this->load();
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
     * Remove categories from frontend
     *
     * @param array $data
     * @return int
     */
    public function removeCategories(array $data) {

        $rows = 0;
        if(empty($data[key($data)]) === false) {
            $rows = $this->remove(self::TABLE, [key($data) => Format::implodeType($data[key($data)])]);
        }
        return $rows;
    }

    /**
     * Unload categories params into `categories`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadCategories(array $data) {
        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;
    }

    /**
     * Unload shop categories params into `category_shop_relationship`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadShopCategories(array $data) {
	    $this->table = self::TABLE_REL;
	    $rows = $this->unload($data);
        return $rows;
    }

}