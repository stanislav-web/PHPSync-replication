<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class BuyMapper. Buy Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/BuyMapper.php
 */
class BuyMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'buy';

    /**
     * Load backend data by passing date
     *
     * @const LOAD
     */
    const LOAD = "SELECT buy.item_id AS id , buy.top_ten
                    FROM `catalogue_view_then_buy` buy
	                LEFT JOIN catalogue cat ON (cat.cat_id = buy.item_id)
	                LEFT JOIN catalogue_categories_items cci ON (cci.item_id = cat.cat_id)
	                LEFT JOIN catalogue_categories_join_shop AS shop FORCE INDEX (`category`) ON (shop.category = cci.cat_id)
                        WHERE buy.last_update	>= '%s'
 	                      && cat.photos != '' && cat.brand_id > 0
	                      && shop.`shop` in (%s)
	                      GROUP BY cat.cat_id";

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
     * Load data from backend `catalogue_view_then_buy`
     *
     * @throws \Exception
     * @return array
     */
    public function loadBuy() {
        $this->query = self::LOAD;
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