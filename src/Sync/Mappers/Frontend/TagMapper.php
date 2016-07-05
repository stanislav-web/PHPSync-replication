<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;

/**
 * Class TagMapper. Tag Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/TagMapper.php
 */
class TagMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'tags';

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
     * Unload tags params into `tags`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadTags(array $data) {

        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;

    }

    /**
     * Unload sizes params into `tags`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadSizes(array $data) {

        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;

    }

    /**
     * Unload types params into `tags`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadTypes(array $data) {

        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;

    }
}