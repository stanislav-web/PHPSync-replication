<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class ShopMapper. Shop Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/ShopMapper.php
 */
class ShopMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'shop';

    /**
     * Load backend data by passing date
     *
     * @const LOAD
     */
    const LOAD = "SELECT shop.shop_id AS id, site_id, shop.shop_name AS name,
                    shop.shop_title AS title, shop.shop_code AS code, shop.token_key,
                    shop.price_id, shop.currency, shop.country_code, shop.delivery AS delivery_ids, shop.payment AS payment_ids, disc.discount AS discounts,
                    curr.currency_sign AS currency_symbol
                        FROM api_partners shop
                        LEFT JOIN shop_discounts disc ON (shop.discount_id = disc.id)
                        LEFT JOIN shop_currency curr ON (curr.code = shop.currency)
                          WHERE
                               shop.price_id NOT IN(103, 105, 0)
                               AND shop.site_id != 0
                               AND curr.`currency_sign` !=''
                               AND (disc.`last_update` >= '%s' || shop.`last_update` >= '%s')";

    /**
     * Load using database
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
		return $this->setParams($params, ...$param) ;
	}

    /**
     * Load data from backend Shop
     *
     * @throws \Exception
     * @return array
     */
    public function load() {
	    return parent::load();
    }

	/**
	 * Load data from backend Shops
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function loadShops() {
		$this->query = self::LOAD;
		return $this->setQueryParams($this->getSyncDate(), $this->getSyncDate())->load();
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