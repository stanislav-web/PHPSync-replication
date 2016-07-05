<?php
namespace Sync\Mappers\Frontend;

use Sync\Aware\MapperAbstract;
use Sync\Exceptions\DbException;

/**
 * Class DocumentMapper. Buy Mapper
 *
 * @package Sync\Mappers\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Frontend/DocumentMapper.php
 */
class DocumentMapper extends MapperAbstract {

    /**
     * Db table
     *
     * @const TABLE
     */
    const TABLE = 'documents';

    /**
     * Load frontend data by passing date
     *
     * @const LOAD_SITE_IDS
     */
    const LOAD_SITE_IDS = "SELECT GROUP_CONCAT(DISTINCT shops.site_id SEPARATOR ',') AS site_id FROM shops";

    /**
     * Remove unused prices from table `documents`
     *
     * @const DELETE_UNUSED_DOCUMENTS
     */
    const DELETE_UNUSED_DOCUMENTS = "DELETE FROM documents WHERE status != 1";


    /**
     * Load using database
     */
    protected function useDb() {
        $this->db = $this->useFront();
    }

    /**
     * Load data from frontend
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
     * Unload prices params into `documents`
     *
     * @param array $data
     * @throws \Exception
     * @return int
     */
    public function unloadDocuments(array $data) {
        $this->table = self::TABLE;
        $rows = $this->unload($data);
        return $rows;
    }

    /**
     * Remove prices from frontend
     *
     * @return int
     */
    public function removeUnusedDocuments() {
        $this->query = self::DELETE_UNUSED_DOCUMENTS;
        return $this->execute($this->query);
    }
}