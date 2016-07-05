<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class CategoryMapper. Category Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/CategoryMapper.php
 */
class CategoryMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'category';

    /**
     * Load backend data by passing date
     *
     * @const LOAD_CATEGORIES
     */
    const LOAD_CATEGORIES = "SELECT category.id, category.title AS name, shop.parent AS parent_id, category.url AS alias, category.sex, category.status
				    FROM catalogue_categories category
					INNER JOIN catalogue_categories_join_shop shop ON (category.id = shop.category)
					WHERE category.last_update >= ?
					  GROUP BY category.id";

    /**
     * Load backend data by passing date
     *
     * @const LOAD_SHOP_CATEGORIES
     */
    const LOAD_SHOP_CATEGORIES = "SELECT category AS category_id, parent AS category_parent_id, shop AS shop_id, sort
                                    FROM `catalogue_categories_join_shop`
                                    WHERE `category` IN(%s) AND shop IN(%s)";


    /**
     * Load backend data by passing date
     *
     * @const LOAD_CATEGORIES
     */
    const LOAD_CATEGORIES_IDS = "SELECT category.id FROM catalogue_categories category
            INNER JOIN catalogue_categories_join_shop shop ON (category.id = shop.category) GROUP BY category.id";

    /**
     * Using database
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
        return parent::setParams($params, ...$param) ;
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
     * Load data from backend Categories
     *
     * @throws \Exception
     * @return array
     */
    public function loadCategories() {
        $this->query = self::LOAD_CATEGORIES;
        return $this->setQueryParams($this->getSyncDate())->load();
    }

    /**
     * Load data from backend Categories
     *
     * @throws \Exception
     * @return array
     */
    public function loadCategoriesIds() {
        $this->query = self::LOAD_CATEGORIES_IDS;
        return $this->load();
    }

    /**
     * Load data from backend Shop_Categories
     *
     * @throws \Exception
     * @return array
     */
    public function loadShopCategories() {
        $this->query = self::LOAD_SHOP_CATEGORIES;
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