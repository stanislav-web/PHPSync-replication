<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class ProductMapper. Product Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/ProductMapper.php
 */
class ProductMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'product';

    /**
     * Load backend products
     *
     * We get product's sizes only from storage id=2 (info from Nikita)
     *
     * @const LOAD_PRODUCTS
     */
    const LOAD_PRODUCTS = "SELECT cat.cat_id AS id,  cat.cat_title AS name, cat.articul, cat.brand_id, cat.photos AS images, cat.preview AS preview,
							cat.cat_text AS description, cat.is_new,  cat.cat_status AS published, cat.income_date AS date_income, cat.rating, cat.tags, IFNULL(cc.sex, 3) AS sex,
							 CONCAT('{',GROUP_CONCAT(DISTINCT CONCAT('\"', TRIM(s.size),'\":', s.count)),'}') AS filter_size,
							 s2.count_sizes AS count_sizes
                            FROM catalogue cat
							LEFT JOIN catalogue_storage AS s1 ON (cat.cat_id = s1.cat_id)
                            LEFT JOIN (
								SELECT  *
								FROM catalogue_storage s
								WHERE s.store_id = 2
							) AS s ON (cat.cat_id = s.cat_id)
							LEFT JOIN (
								SELECT ss.cat_id, SUM(ss.count) as count_sizes
								FROM catalogue_storage ss
								WHERE ss.store_id = 2
                                GROUP BY ss.cat_id
							) AS s2 ON (cat.cat_id = s2.cat_id)
                            LEFT JOIN catalogue_categories_items cci ON (cci.item_id = cat.cat_id)
                            LEFT JOIN catalogue_categories cc ON (cc.id = cci.cat_id)
                            LEFT JOIN catalogue_categories_join_shop AS shop FORCE INDEX (`category`) ON (shop.category = cc.id)
							WHERE
							 (cat.last_update >= '%s' || s1.last_update >= '%s')
							 && shop.`shop` in (%s) && cat.brand_id > 0 && cat.photos != ''
							 GROUP BY cat.cat_id
							ORDER BY cat.cat_id";

    /**
     * Load backend product's categories
     *
     * @const LOAD_PRODUCT_CATEGORY
     */
    const LOAD_PRODUCT_CATEGORY = "SELECT category.item_id AS product_id, category.cat_id AS category_id, NULL AS tag_id , 4 as tag_meta_id
									FROM catalogue_categories_items category FORCE INDEX(`group`)
									LEFT JOIN catalogue cat ON (cat.cat_id = category.item_id)
									LEFT JOIN catalogue_storage `storage` ON (cat.cat_id = `storage`.cat_id)
									LEFT JOIN catalogue_categories_join_shop AS shop FORCE INDEX (`category`) ON (shop.category = category.cat_id)
									WHERE (cat.last_update	>= '%s' || category.last_update >= '%s' || `storage`.last_update >= '%s')
                                    && shop.`shop` in (%s)
                                    && cat.photos != '' && category.`cat_id` > 0 && cat.brand_id > 0
									GROUP BY product_id, category_id";

    /**
     * Load backend product's tags
     *
     * @const LOAD_PRODUCT_TAGS
     */
    const LOAD_PRODUCT_TAGS = "SELECT cat.cat_id AS product_id, NULL AS category_id , tag.tag_id AS tag_id, 1 as tag_meta_id
									FROM catalogue_tags_items tag
									INNER JOIN catalogue_tags tags ON (tag.tag_id = tags.id)
 									LEFT JOIN catalogue cat ON (cat.cat_id = tag.cat_id)

 									      LEFT JOIN catalogue_categories_items cci ON (cci.item_id = cat.cat_id)
 									      LEFT JOIN catalogue_categories_join_shop AS shop FORCE INDEX (`category`) ON (shop.category = cci.cat_id)

									LEFT JOIN catalogue_storage `storage` ON (cat.cat_id = `storage`.cat_id)
									WHERE (cat.last_update	>= '%s' || tag.last_update >= '%s' || `storage`.last_update >= '%s')
									&& cat.photos != '' && cat.brand_id > 0 && shop.`shop` in (%s)
									GROUP BY product_id, tag_id";

    /**
     * Load backend product's types
     *
     * @const LOAD_PRODUCT_TYPES
     */
    const LOAD_PRODUCT_TYPES = "SELECT cat.cat_id AS product_id, NULL AS category_id, `type`.id AS tag_id, 3 as tag_meta_id
									FROM catalogue cat
									LEFT JOIN catalogue_categories_items cci ON (cci.item_id = cat.cat_id)
 									LEFT JOIN catalogue_categories_join_shop AS shop FORCE INDEX (`category`) ON (shop.category = cci.cat_id)
									LEFT JOIN catalogue_storage `storage` ON (cat.cat_id = `storage`.cat_id)
									LEFT JOIN catalogue_tags_types `type` ON (`type`.name = LOWER(TRIM(`cat`.cat_title)))
									WHERE (cat.last_update	>= '%s' || `storage`.last_update >= '%s')
									&& cat.photos != '' && cat.brand_id > 0 && shop.`shop` in (%s)
									GROUP BY product_id, tag_id HAVING tag_id IS NOT NULL";

    /**
     * Load backend product's sizes
     *
     * @const LOAD_PRODUCT_SIZES
     */
    const LOAD_PRODUCT_SIZES = "SELECT cat.cat_id AS product_id, NULL AS category_id, size.id AS tag_id, 2 as tag_meta_id
									FROM catalogue_storage `storage`
									LEFT JOIN catalogue cat ON (cat.cat_id = `storage`.cat_id)

									LEFT JOIN catalogue_categories_items cci ON (cci.item_id = cat.cat_id)
 									LEFT JOIN catalogue_categories_join_shop AS shop FORCE INDEX (`category`) ON (shop.category = cci.cat_id)

									LEFT JOIN catalogue_tags_sizes size ON (size.name = TRIM(`storage`.size))
									WHERE (cat.last_update	>= '%s' || `storage`.last_update >= '%s')
									&& cat.photos != '' && cat.brand_id > 0
									&& `storage`.count > 0
									&& shop.`shop` in (%s)
									GROUP BY product_id, tag_id;";

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
     * Load data from backend Categories
     *
     * @throws \Exception
     * @return array
     */
    public function load() {
        return parent::load();
    }

    /**
     * Load products
     *
     * @throws \Exception
     * @return array
     */
    public function loadProducts() {
        $this->query = self::LOAD_PRODUCTS;
        return $this->load();
    }

    /**
     * Load product categories
     *
     * @throws \Exception
     * @return array
     */
    public function loadProductCategories() {
        $this->query = self::LOAD_PRODUCT_CATEGORY;
        return $this->load();
    }

    /**
     * Load product tags
     *
     * @throws \Exception
     * @return array
     */
    public function loadProductTags() {
        $this->query = self::LOAD_PRODUCT_TAGS;
        return $this->load();
    }

    /**
     * Load product sizes
     *
     * @throws \Exception
     * @return array
     */
    public function loadProductSizes() {
        $this->query = self::LOAD_PRODUCT_SIZES;
        return $this->load();
    }

    /**
     * Load product types
     *
     * @throws \Exception
     * @return array
     */
    public function loadProductTypes() {
        $this->query = self::LOAD_PRODUCT_TYPES;
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