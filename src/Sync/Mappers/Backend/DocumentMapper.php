<?php
namespace Sync\Mappers\Backend;

use Sync\Aware\MapperAbstract;

/**
 * Class DocumentMapper. Buy Mapper
 *
 * @package Sync\Mappers\Backend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Mappers/Backend/DocumentMapper.php
 */
class DocumentMapper extends MapperAbstract {

    /**
     * Executed
     *
     * @const COMMAND
     */
    const COMMAND = 'document';

    /**
     * Load backend data by passing date
     *
     * @const LOAD
     */
    const LOAD = "SELECT docs.`id`, docs.`url` AS href, docs.`name` AS title, data.html AS ru,
                    (SELECT html FROM structure_docdata_lang WHERE doc_id = data.id AND lang = 'ua') AS ua,
                    (SELECT html FROM structure_docdata_lang WHERE doc_id = data.id AND lang = 'en') AS en,
                    docs.`site_id`, docs.`status`, docs.`sort`
                      FROM `structure_documents` AS docs
                      INNER JOIN `structure_docdata` AS data ON (data.`document_id` = docs.id)
                      INNER JOIN `structure_docdata_lang` AS lang
                      WHERE docs.site_id IN(%s)
                            AND data.`html` != '' AND docs.`is_menu` = 1 && (data.element = 'text' || data.element = 'text 1' || data.element = 'text 2')
                            AND (
                                  docs.`last_update` >= '%s' OR data.`last_update` >= '%s' OR lang.`last_update`  >= '%s'
                                )
                                GROUP BY docs.`id`
                                ORDER BY docs.`id` ASC";

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
     * Load data from backend
     *
     * @throws \Exception
     * @return array
     */
    public function loadDocuments() {
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