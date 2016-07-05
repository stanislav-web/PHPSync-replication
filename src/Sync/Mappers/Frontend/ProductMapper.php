<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;
use Sync\Resolvers\Format;

/**
 * Class ProductMapper. Product Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/ProductMapper.php
 */
class ProductMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'products';

    /**
     * Db table
     *
     * @const TABLE_REL
     */
    const TABLE_REL = 'products_relationship';

    /**
     * Db table
     *
     * @const TABLE_PRICE
     */
    const TABLE_PRICE = 'prices';

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
     * Setting up db parameters
     *
     * @param array $params
     * @param array ...$param
     * @return CategoryMapper
     */
    public function setQueryParams($params, ...$param) {
        return parent::setParams($params, ...$param) ;
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
     * Unload tags params into `products`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadProducts(array $data) {

        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;

    }

    /**
     * Unload product categories into `products_relationship`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadProductCategories(array $data) {

        $this->table = self::TABLE_REL;
        $rows = $this->unload($data);
        return $rows;

    }

    /**
     * Unload product tags, sizes, types into `products_relationship`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadProductTags(array $data) {

        $this->table = self::TABLE_REL;
        $rows = $this->unload($data);
        return $rows;

    }

    /**
     * Remove product tags from `products_relationship`
     *
     * @param array $data
     * @return int
     */
    public function removeProductTags(array $data) {

        $rows = 0;
        if(empty($data[key($data)]) === false) {
            $rows = $this->remove(self::TABLE_REL, [key($data) => Format::implodeType($data[key($data)])]);
        }
        return $rows;
    }
}