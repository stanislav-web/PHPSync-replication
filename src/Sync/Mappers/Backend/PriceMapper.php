<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class PriceMapper. Price Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/PriceMapper.php
 */
class PriceMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'price';

    /**
     * Load backend data by passing date
     *
     * @const LOAD
     */
    const LOAD = "SELECT price.price_id AS id,  price.cat_id AS product_id,  price.price, CAST(price.discount AS SIGNED) AS percent, price.discount_price AS discount
  		            FROM catalogue_prices price
  		            INNER JOIN catalogue AS cat ON (cat.cat_id = price.cat_id)
  		            LEFT JOIN catalogue_storage AS s ON (cat.cat_id = s.cat_id)
  		            INNER JOIN `".self::FRONTDB."`.products prod ON (prod.id = price.cat_id)
		            WHERE (price.last_update >= '%s' || cat.last_update >= '%s' || s.last_update >= '%s')
		              && price.price_id in (%s) && cat.cat_status = 1 && price.price > 0
		              && cat.photos != '' && cat.brand_id > 0
		              GROUP BY price.cat_id, price.price_id";

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
    public function loadPrices() {
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