<?php
namespace Sync\Resolvers;

use Sync\Development\Message;
use Sync\Exceptions\DbException;

/**
 * Class DbAdapter. Database connection client
 *
 * @package Sync\Resolvers
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Resolvers/DbAdapter.php
 */
class DbAdapter {

    /**
     * Db configuration
     *
     * @var array $config
     */
    private $config = [];

    /**
     * Db adapter
     *
     * @var \PDO $connection
     */
    private $connection = null;

	private $mysqlFunctoins = [
		'NOW()',
	];

    /**
     * Initialize Db config
     *
     * @param array $config
     */
    public function __construct(array $config) {

        if(empty($this->config) === true) {
            // always use lazy
            $this->config = $config;
        }
    }

    /**
     * Check connection status
     *
     * @throws \Sync\Exceptions\DbException
     */
    public function isConnected() {

        try {

            if($this->connection === null) {

                // always use lazy
                $this->connection = new \PDO($this->config['dsn'],$this->config['username'], $this->config['password'],
                    [
                        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '".$this->config['charset']."'",
                        \PDO::ATTR_CASE => \PDO::CASE_LOWER,
                        \PDO::ATTR_ERRMODE => $this->config['debug'],
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_PERSISTENT => $this->config['persistent'],
                    ]);
            }

            $status = $this->connection->getAttribute(\PDO::ATTR_CONNECTION_STATUS);

            if(ENV === 'development') {
                echo Message::success('connected', [$status]);
            }

            return ($status != false) ? true : false;
        }
        catch(\PDOException $e) {
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }
    }

    /**
     * Select database
     *
     * @param string $db
     * @throws \Sync\Exceptions\DbException
     * @return \PDO
     */
    public function selectDb($db) {

        try {
            // select database
            $this->connection->exec('USE '.$db);

            return $this->connection;
        }
        catch(\PDOException $e) {
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }
    }

    /**
     * Describe table columns for validate
     *
     * @param string $table
     * @return array
     */
	private function getTableColumns($table) {

        try {
            $stmt = $this->connection->prepare("DESCRIBE {$table}");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }
        catch (\PDOException $e) {
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }

	}

    /**
     * Transaction wrapper
     *
     * @param callable $function
     * @return array
     */
    public function callTransaction(callable $function) {

        try {
            // first of all, let's begin a transaction
            $this->connection->beginTransaction();

            // a set of queries; if one fails, an exception should be thrown
            $call = $function();

            // if we arrive here, it means that no exception was thrown
            // i.e. no query has failed, and we can commit the transaction
            $this->connection->commit();

            return $call;

        } catch (\PDOException $e) {
            $this->connection->rollback();
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }
    }

    /**
     * Run executable queries
     *
     * @param string $query
     * @return int
     */
    public function execute($query) {

        try {
            $count = $this->connection->exec($query);
            return $count;
        } catch (\PDOException $e) {
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }
    }

    /**
     * Fetch one row from database
     *
     * @param  string     $query
     * @param  array $bindParams
     * @throws \Sync\Exceptions\DbException
     * @return \stdClass object
     */
    public function fetchOne($query, array $bindParams) {

        try {

            $stmt = $this->connection->prepare($query);
            $stmt->execute($bindParams);
            return $stmt->fetch();
        }
        catch(\PDOException $e) {
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }
    }

	/**
	 * Fetch all rows from database
	 *
	 * @param  string     $query
	 * @param  array $bindParams
	 * @throws \Sync\Exceptions\DbException
	 * @return \stdClass object
	 */
	public function fetchAll($query, array $bindParams) {

		try {

            if(stripos($query, '%s') != false) {
                $stmt = $this->connection->query(vsprintf($query, $bindParams));
            }
            else {
	            $stmt = $this->connection->prepare($query);
            }

			$stmt->execute($bindParams);
			return $stmt->fetchAll();
		}
		catch(\PDOException $e) {
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
		}
	}

    /**
     * Check existing fields in transfer accepted base
     *
     * @param string $table
     * @param array $params
     * @return array
     */
    private function filterFields($table, $params) {

        $fields = $this->getTableColumns($table);

        foreach($fields as $field) {
            if(in_array($field, $fields) === false) {
                throw new DbException("The `{$field}` is not defined!");
            }
        }

        return $params;
    }

    /**
     * Insert, on duplicate key update
     *
     * @param string $table
     * @param array $params
     * @throws \Sync\Exceptions\DbException
     * @return bool
     */
    public function insertOnDuplicate($table, $params) {

        $params = $this->filterFields($table, $params);

	    $sql = "INSERT INTO {$table} (`".implode('`, `', array_keys($params))."`)
                        VALUES(:".implode(', :', array_keys($params)).")
                        ON DUPLICATE KEY UPDATE";

	    $sql = $this->prepareDataForUpdate($sql, $params);

        try {

            return $this->connection->exec($sql);

        } catch (\PDOException $e) {
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine() . $sql);
        }
    }

    /**
     * Prepare input data for insert-update queries
     *
     * @param string $sql
     * @param array $params
     * @param array $fields
     * @return string
     */
    private function prepareDataForUpdate($sql, $params) {

        foreach ($params as $field => $value) {
            $sql .= " `{$field}` = :".$field.",";
        }

        $sql = rtrim($sql, ',');

        foreach(array_reverse($params) as $field => $value) {

            if(is_numeric($value) === true) {
                // bind numbers
                $sql = str_replace(':'.$field, (int)$value, $sql);
            }
            else if(is_null($value)) {
                // bind null
                $sql = str_replace(':'.$field, 'null', $sql);
            }
            else {
	            if(in_array($value, $this->mysqlFunctoins)) {
		            //bind MYSQL function
		            $sql = str_replace(':'.$field, $value, $sql);
	            } else {
		            $value = (mb_strlen(trim($value)) == 0) ? '\'\'' : $this->connection->quote($value);
		            // bind string
		            $sql = str_replace(':'.$field, $value, $sql);

	            }
            }
        }

        return $sql;
    }

    /**
     * Remove from table
     *
     * @param       $table
     * @param array $params
     * @return int
     */
    public function remove($table, array $params = []) {

        try {

            if(key($params) === '*') {
                $sql = "DELETE FROM {$table}";
            }
            else if(empty($params) === false) {
                $sql = "DELETE FROM {$table} WHERE ".key($params)." IN (".$params[key($params)].")";
            }

            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount();

        } catch (\PDOException $e) {
            throw new DbException($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }

    }

}