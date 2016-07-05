<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class BuyTogetherMapper. BuyTogether Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/BuyTogetherMapper.php
 */
class BuyTogetherMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'buyTogether';

    /**
     * Load backend data by passing date
     *
     * @const LOAD
     */
    const LOAD = "SELECT buy.item_id AS id ,
                    CONCAT('[', substring_index( GROUP_CONCAT(buy.buy_item_id ORDER BY buy.count_orders DESC) , ',', 10 ) ,']') as top_ten
                    FROM `buy_together` buy
                    WHERE buy.last_update	>= '%s'
                    GROUP BY buy.item_id";

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
    public function loadBuyTogether() {
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