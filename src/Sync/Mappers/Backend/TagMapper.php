<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class TagMapper. Tag mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/TagMapper.php
 */
class TagMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'tag';

    /**
     * Load backend tags
     *
     * @const LOAD_TAGS
     */
    const LOAD_TAGS = "SELECT `id`, `title` AS name, `parent_id`, 1 AS `tag_meta_id`
                          FROM `catalogue_tags`
                          WHERE title !='' && `last_update` >= '%s'";

    /**
     * Load backend sizes
     *
     * @const LOAD_SIZES
     */
    const LOAD_SIZES = "SELECT `id`, `name`, null AS parent_id, 2 as tag_meta_id
                            FROM catalogue_tags_sizes
                            WHERE `last_update` >= '%s'";

    /**
     * Load backend types
     *
     * @const LOAD_TYPES
     */
    const LOAD_TYPES = "SELECT `id`, `name`, null AS parent_id, 3 as tag_meta_id
                            FROM catalogue_tags_types
                            WHERE `last_update` >= '%s'";

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
     * Load data from backend Tags
     *
     * @throws \Exception
     * @return array
     */
    public function loadTags() {
        $this->query = self::LOAD_TAGS;
        return $this->setQueryParams($this->getSyncDate())->load();
    }

    /**
     * Load data from backend Sizes
     *
     * @throws \Exception
     * @return array
     */
    public function loadSizes() {
        $this->query = self::LOAD_SIZES;
        return $this->setQueryParams($this->getSyncDate())->load();
    }

    /**
     * Load data from backend Types
     *
     * @throws \Exception
     * @return array
     */
    public function loadTypes() {
        $this->query = self::LOAD_TYPES;
        return $this->setQueryParams($this->getSyncDate())->load();
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