<?php
namespace Sync\Aware;

use Sync\Development\Debugger;
use Sync\Resolvers\DbAdapter;
use Sync\Exceptions\DbException;

/**
 * Class MapperAbstract. Mapper interface
 *
 * @package Sync\Aware
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Aware/MapperAbstract.php
 */
abstract class MapperAbstract {

    /**
     * Backend database
     *
     * @const BACKDB
     */
    const BACKDB = 'v6_z95';

    /**
     * Frontend database
     *
     * @const FRONTDB
     */
    const FRONTDB = 'Shop';

    /**
     * Backend table sync
     *
     * @const TABLESYNC
     */
    const TABLESYNC = 'syncronize';

    /**
     * Configuration
     *
     * @var \Sync\Resolvers\Config $config
     */
    private $config = null;

    /**
     * Db connector instance
     *
     * @var \Sync\Resolvers\DbAdapter $dbAdapter
     */
    protected $db = null;

	/**
	 * Using table
	 *
	 * @var string $table
	 */
	protected $table;

	/**
	 * Using query
	 *
	 * @var string $query
	 */
	protected $query;

	/**
	 * Assigned to query params
	 *
	 * @var mixed $params
	 */
	protected $params = [];

    /**
     * Load data
     *
     * @return mixed
     */
    //abstract public function load();

    /**
     * Unload data to database
     *
     * @params array $data
     * @return int
     */
    abstract public function unload(array $data);

    /**
     * @return mixed
     */
    abstract protected function useDb();

    /**
     * Initialize Mapper connector
     *
     * @param \Sync\Resolvers\Config $config
     */
    public function __construct(\Sync\Resolvers\Config $config) {

        $this->config = $config;

        if($this->db === null) {
            // always use lazy
            $this->db = new DbAdapter($this->config->get('connect'));
        }
        $this->useDb();
    }

	/**
	 * Setting up db parameters
	 *
	 * @param array $params
	 * @param array ...$param
	 * @return CategoryMapper
	 */
	protected function setParams($params, ...$param) {

		if(empty($param) === true) {
			$this->params = [$params];
		}
		else {
			$this->params = array_merge([$params], $param);
		}

		return $this;
	}

	/**
	 * Get db parameters
	 *
	 * @return array
	 */
	protected function getParams() {
		return $this->params;
	}

	/**
	 * Get entity configurations
	 *
	 * @return array
	 */
	public function getEntityConfig() {
		return $this->config->get('entities')[static::COMMAND];
	}

	/**
	 * Get entity configurations
	 *
	 * @return array
	 */
	public function getNotificationConfig() {
		return $this->config->get('notification');
	}

    /**
     * Run executable queries
     *
     * @param string $query
     * @throws \Exception
     * @return int
     */
    public function execute($query) {

        try {
            return $this->db->execute($query);
        }
        catch(DbException $e) {
            throw new \Exception($e->getMessage());
        }
    }

	/**
	 * Load data from backend
	 *
	 * @throws \Exception
	 * @return array
	 */
	protected function load() {

		try {
			$result = $this->db->fetchAll($this->query, $this->getParams());

			return $result;
		}
		catch(DbException $e) {
			throw new \Exception($e->getMessage());
		}
	}

    /**
     * Load one row data from backend
     *
     * @throws \Exception
     * @return array
     */
    protected function loadRow() {

        try {

            $result = $this->db->fetchOne($this->query, $this->getParams());

            return $result;
        }
        catch(DbException $e) {
            throw new \Exception($e->getMessage());
        }
    }

	/**
     * Get date for synchronize
     *
     * @return string
     */
    protected function getSyncDate() {

	    $date = $this->getBinlogDate();
	    if(!empty($date)) {
	      return $date;
	    }

        $delay = $this->config->get('entities')[strtolower(static::COMMAND)]['delay'];
        $row = $this->db->fetchOne("SELECT (IFNULL(last_sync, NOW()) - INTERVAL ".$delay." MINUTE) as last_sync FROM `".self::TABLESYNC."` WHERE `command` = ?", [static::COMMAND]);

        if(empty($row['last_sync']))  {
            $date = new \DateTime();
            $date->add(\DateInterval::createFromDateString('-'.$delay.' seconds'));
            return $date->format('Y-m-d H:i:s');
        }

        return $row['last_sync'];
    }

	private function getBinlogDate() {
		$binlogDate = '';
		$filePath =  $this->config->get('binlogPath');
		$interval = $this->config->get('binlogInterval');
		$delay = $this->config->get('entities')[strtolower(static::COMMAND)]['delay'];

		if(file_exists($filePath)) {
			$binlogData = file_get_contents($filePath);
			if(!empty($binlogData)) {
				$binlogDataArray = explode('|', $binlogData);
				$binlogDate = $binlogDataArray[1]. ' ' .$binlogDataArray[2];

				$date = new \DateTime($binlogDate);
				$date->add(\DateInterval::createFromDateString('-'.$interval.' hours'));
				$binlogDate = $date->format('Y-m-d H:i:s');
			}
		}

		echo 'Sync date: '.$binlogDate.PHP_EOL;

		return $binlogDate;
	}

	/**
	 * Set syncronize date
	 */
    protected function setSyncDate() {

        $this->db->insertOnDuplicate(self::TABLESYNC, [
            'command'=> static::COMMAND,
            'last_sync'=>'NOW()',
            'data_receive'  => Debugger::getMemoryUsage(),
            'elapsed'       => Debugger::getElapsedTime(),
        ]);
    }

	/**
	 * Multiple insert rows
	 *
	 * @param string $table
	 * @param array $data
	 * @return int
	 */
	protected function insertBatch($table, array $data) {

		$count = 0;
		foreach($data as $row) {
            if($this->db->insertOnDuplicate($table, $row))
				++$count;
		}

		return $count;
	}

    /**
     * Remove from table
     *
     * @param string      $table
     * @param array $data
     * @return int
     */
    protected function remove($table, array $data = []) {

	    $rows = 0;
	    if(empty($data[key($data)]) === false) {
		    $rows = $this->db->remove($table, $data);
	    }

	    return $rows;
    }

    /**
     * Use backend database
     *
     * @return \Sync\Resolvers\DbAdapter
     */
    protected function useBack() {

        if($this->db->isConnected() === true) {
            $this->db->selectDb(self::BACKDB);
	        return  $this->db;
        }
    }

    /**
     * Use frontend database
     *
     * @return \Sync\Resolvers\DbAdapter
     */
    protected function useFront() {

        if($this->db->isConnected() === true) {
            $this->db->selectDb(self::FRONTDB);
	        return  $this->db;
        }
    }

    /**
     * Call transaction
     *
     * @param callable $functions
     */
    public function callTransaction(callable $functions) {
       return $this->db->callTransaction($functions);
    }
}