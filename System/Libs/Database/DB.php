<?php
/*************************************************
 * Titan-2 Mini Framework
 * DB Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT
 *
 *************************************************/
namespace System\Libs\Database;

use PDO;
use PDOException;
use System\Libs\Exception\ExceptionHandler;

class DB
{
    // Default connection
    protected $con      = 'primary';

	// PDO instance
	public $pdo 		= null;

	// Select statement
	protected $select 	= '*';

	// Table name
	protected $table	= null;

	// Where statement
	protected $where	= null;

	// Limit statement
	protected $limit	= null;

	// Join statement
	protected $join		= null;

	// Order By statement
	protected $orderBy	= null;

	// Group By statement
	protected $groupBy	= null;

	// Having statement
	protected $having	= null;

	// Last instert id
	protected $insertId	= null;

	// Custom query
	protected $custom 	= null;

	// SQL Statement
	protected $sql		= null;

	// Table prefix
	protected $prefix	= null;

	// Error
	protected $error	= null;

	// Number of total rows
	protected $numRows	= 0;

	// Group flag for where and having statements
	protected $grouped 	= 0;

	// DB config items
	protected $config;

	/**
	 * Initializing
	 *
     * @throws \Exception
     */
	public function __construct()
	{
        // Connect
		$this->connect();
	}

    /**
     * Select a connection
     *
     * @param string $connection
     * @return $this
     * @throws \Exception
     */
    public function connection($connection)
    {
        $this->con = $connection;
        $this->connect();

        return $this;
	}

    /**
     * Connect
     *
     * @return PDO|null
     * @throws \Exception
     */
    public function connect()
    {
        // Getting db config items
        $this->config                   = config('database.' . $this->con);

        $this->config['db_driver']		= ($this->config['db_driver']) ? $this->config['db_driver'] : 'mysql';
        $this->config['db_host']		= ($this->config['db_host']) ? $this->config['db_host'] : 'localhost';
        $this->config['db_charset']		= ($this->config['db_charset']) ? $this->config['db_charset'] : 'utf8';
        $this->config['db_collation']	= ($this->config['db_collation']) ? $this->config['db_collation'] : 'utf8_general_ci';
        $this->config['db_prefix']		= ($this->config['db_prefix']) ? $this->config['db_prefix'] : '';

        // Setting prefix
        $this->prefix = $this->config['db_prefix'];

        $dsn = '';
        // Setting connection string
        if ($this->config['db_driver'] == 'mysql' || $this->config['db_driver'] == 'pgsql' || $this->config['db_driver'] == '') {
            $dsn = $this->config['db_driver'] . ':host=' . $this->config['db_host'] . ';dbname=' . $this->config['db_name'];
        } elseif ($this->config['db_driver'] == 'sqlite') {
            $dsn = 'sqlite:' . $this->config['db_name'];
        } elseif ($this->config['db_driver'] == 'oracle') {
            $dsn = 'oci:dbname=' . $this->config['db_host'] . '/' . $this->config['db_name'];
        }

        // Connecting to server
        try
        {
            $this->pdo = new PDO($dsn, $this->config['db_user'], $this->config['db_pass']);
            $this->pdo->exec("SET NAMES '" . $this->config['db_charset'] . "' COLLATE '" . $this->config['db_collation'] . "'");
            $this->pdo->exec("SET CHARACTER SET '" . $this->config['db_charset'] . "'");
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        }
        catch(PDOException $e)
        {
            throw new ExceptionHandler("DB error", "Can not connect to Database with PDO.<br><br>" . $e->getMessage());
        }

        return $this->pdo;
	}

	/**
	 * Defines columns to select
	 *
	 * @param string $select
	 * @return $this
	 */
	public function select($select = null)
	{
		if (!is_null($select))
			$this->select = $select;

		return $this;
	}

	/**
	 * Defines table
	 *
	 * @param string $table
	 * @return $this
	 */
	public function table($table)
	{
		$this->table = $this->prefix . $table;

		return $this;
	}

	/**
	 * Defines 'Left Join' operation
	 *
	 * @param string $table
	 * @param string $op
	 * @return $this
	 */
	public function leftJoin($table, $op)
	{
		$this->_join($table, $op, 'LEFT');

		return $this;
	}

	/**
	 * Defines 'Right Join' operation
	 *
	 * @param string $table
	 * @param string $op
	 * @return $this
	 */
	public function rightJoin($table, $op)
	{
		$this->_join($table, $op, 'RIGHT');

		return $this;
	}

	/**
	 * Defines 'Inner Join' operation
	 *
	 * @param string $table
	 * @param string $op
	 * @return $this
	 */
	public function innerJoin($table, $op)
	{
		$this->_join($table, $op, 'INNER');

		return $this;
	}

	/**
	 * Defines 'Full Outer Join' operation
	 *
	 * @param string $table
	 * @param string $op
	 * @return $this
	 */
	public function fullOuterJoin($table, $op)
	{
		$this->_join($table, $op, 'FULL OUTER');

		return $this;
	}

	/**
	 * Defines 'Left Outer Join' operation
	 *
	 * @param string $table
	 * @param string $op
	 * @return $this
	 */
	public function leftOuterJoin($table, $op)
	{
		$this->_join($table, $op, 'LEFT OUTER');

		return $this;
	}

	/**
	 * Defines 'Right Outer Join' operation
	 *
	 * @param string $table
	 * @param string $op
	 * @return $this
	 */
	public function rightOuterJoin($table, $op)
	{
		$this->_join($table, $op, 'RIGHT OUTER');

		return $this;
	}

	/**
	 * Defines 'Join' operations
	 *
	 * @param string $table
	 * @param string $op
	 * @return $this
	 */
	private function _join($table, $op, $join)
	{
		$this->join = $this->join . ' ' . $join . ' JOIN ' . $this->prefix . $table . ' ON ' . $op;
	}

	/**
	 * Escape data
	 *
	 * @param string $data
	 * @return string
	 */
	private function _escape($data)
	{
		return $this->pdo->quote(trim($data));
	}

	/**
	 * Defines 'where' operation
	 *
	 * @param string $column
	 * @param string $op
	 * @param string $value
	 * @param string $logic
	 * @return $this
	 */
	public function where($column, $op = '=', $value = '', $logic = 'AND', $noEscape = false)
	{
		if ($noEscape === false)
			$value = $this->_escape($value);

		if (is_null($this->where))
			$this->where = 'WHERE ' . $column . $op . $value;
		else {
			if ($this->grouped > 0) {
				$this->where .= ' ' . $column . $op . $value;
				$this->grouped = 0;
			} else {
				$this->where .= ' ' . $logic . ' ' . $column . $op . $value;
			}
		}

		return $this;
	}

	/**
	 * Defines 'or where' operation
	 *
	 * @param string $column
	 * @param string $op
	 * @param string $value
	 * @return $this
	 */
	public function orWhere($column, $op = '=', $value = '')
	{
		$this->where($column, $op, $value, 'OR');

		return $this;
	}

	/**
	 * Start a group of 'where' operation
	 *
	 * @param string $logic
	 * @return $this
	 */
	public function whereGroupStart($logic = 'AND')
	{
		$this->where .= ' ' . $logic . ' (';
		$this->grouped++;

		return $this;
	}

	/**
	 * End a group of 'where' operation
	 *
	 * @return $this
	 */
	public function whereGroupEnd()
	{
		$this->where .= ' )';
		$this->grouped = 0;

		return $this;
	}

	/**
	 * Start a group of 'having' operation
	 *
	 * @param string $logic
	 * @return $this
	 */
	public function havingGroupStart($logic = 'AND')
	{
		$this->having .= ' ' . $logic . ' (';
		$this->grouped++;

		return $this;
	}

	/**
	 * End a group of 'having' operation
	 *
	 * @return $this
	 */
	public function havingGroupEnd()
	{
		$this->having .= ' )';
		$this->grouped = 0;

		return $this;
	}

	/**
	 * Defines 'Order By' operation
	 *
	 * @param string $column
	 * @param string $sort
	 * @return $this
	 */
	public function orderBy($column, $sort = 'asc')
	{
		if (is_null($this->orderBy))
			$this->orderBy = ' ORDER BY ' . $column . ' ' . $sort;
		else
			$this->orderBy .= ', ' . $column . ' ' . $sort;

		return $this;
	}

	/**
	 * Defines 'Limit' operation
	 *
	 * @param integer $start
	 * @param integer $row
	 * @return $this
	 */
	public function limit($start, $rows = 0)
	{
		if ($rows === 0)
			$this->limit = ' LIMIT ' . $start;
		else
			$this->limit = ' LIMIT ' . $start . ', ' . $rows;

		return $this;
	}

	/**
	 * Defines 'Group By' operation
	 *
	 * @param string $column
	 * @return $this
	 */
	public function groupBy($column)
	{
		if (is_null($this->groupBy))
			$this->groupBy = ' GROUP BY ' . $column;
		else
			$this->groupBy .= ', ' . $column;

		return $this;
	}

	/**
	 * Defines 'Having' operation
	 *
	 * @param string $column
	 * @param string $op
	 * @param string $value
	 * @param string $logic
	 * @return $this
	 */
	public function having($column, $op = '=', $value = '', $logic = 'AND')
	{
		if (is_null($this->having))
			$this->having = 'HAVING ' . $column . $op . $this->_escape($value);
		else {
			if ($this->grouped > 0) {
				$this->having .= ' ' . $column . $op . $value;
				$this->grouped = 0;
			} else {
				$this->having .= ' ' . $logic . ' ' . $column . $op . $this->_escape($value);
			}
		}

		return $this;
	}

	/**
	 * Defines 'Or Having' operation
	 *
	 * @param string $column
	 * @param string $op
	 * @param string $value
	 * @return $this
	 */
	public function orHaving($column, $op = '=', $value = '')
	{
		$this->having($column, $op, $value, 'OR');

		return $this;
	}

	/**
	 * Defines 'Like' operation
	 *
	 * @param string $column
	 * @param string $value
	 * @param string $logic
	 * @return $this
	 */
	public function like($column, $value, $logic = 'AND')
	{
		$this->where($column, ' LIKE ', $value, $logic);

		return $this;
	}

	/**
	 * Defines 'Or Like' operation
	 *
	 * @param string $column
	 * @param string $value
	 * @return $this
	 */
	public function orLike($column, $value)
	{
		$this->like($column, $value, 'OR');

		return $this;
	}

	/**
	 * Defines 'Not Like' operation
	 *
	 * @param string $column
	 * @param string $value
	 * @param string $logic
	 * @return $this
	 */
	public function notLike($column, $value, $logic = 'AND')
	{
		$this->where($column, ' NOT LIKE ', $value, $logic);

		return $this;
	}

	/**
	 * Defines 'Or Not Like' operation
	 *
	 * @param string $column
	 * @param string $value
	 * @return $this
	 */
	public function orNotLike($column, $value)
	{
		$this->notLike($column, $value, 'OR');

		return $this;
	}

	/**
	 * Defines 'In' operation
	 *
	 * @param string $column
	 * @param array $list
	 * @param string $logic
	 * @return $this
	 */
	public function in($column, $list = [], $logic = 'AND')
	{
		$in_list = '';

		foreach ($list as $element) {
			$in_list .= $this->_escape($element) . ',';
		}
		$in_list = '(' . rtrim($in_list, ',') . ')';

		$this->where($column, ' IN ', $in_list, $logic, true);

		return $this;
	}

	/**
	 * Defines 'Or In' operation
	 *
	 * @param string $column
	 * @param array $list
	 * @return $this
	 */
	public function orIn($column, $list = [])
	{
		$this->in($column, $list, 'OR');

		return $this;
	}

	/**
	 * Defines 'Not In' operation
	 *
	 * @param string $column
	 * @param array $list
	 * @param string $logic
	 * @return $this
	 */
	public function notIn($column, $list = [], $logic = 'AND')
	{
		$in_list = '';

		foreach ($list as $element) {
			$in_list .= $this->_escape($element) . ',';
		}
		$in_list = '(' . rtrim($in_list, ',') . ')';

		$this->where($column, ' NOT IN ', $in_list, $logic, true);

		return $this;
	}

	/**
	 * Defines 'Or Not In' operation
	 *
	 * @param string $column
	 * @param array $list
	 * @return $this
	 */
	public function orNotIn($column, $list = [])
	{
		$this->notIn($column, $list, 'OR');

		return $this;
	}

	/**
	 * Defines 'Between' operation
	 *
	 * @param string $column
	 * @param array $first
	 * @param string $second
	 * @param string $logic
	 * @return $this
	 */
	public function between($column, $first, $second, $logic = 'AND')
	{
		$this->where($column, ' BETWEEN ', $first . ' AND ' . $second, $logic);

		return $this;
	}

	/**
	 * Defines 'Or Between' operation
	 *
	 * @param string $column
	 * @param array $first
	 * @param string $second
	 * @return $this
	 */
	public function orBetween($column, $first, $second)
	{
		$this->between($column, $first, $second, 'OR');

		return $this;
	}

	/**
	 * Defines 'Not Between' operation
	 *
	 * @param string $column
	 * @param array $first
	 * @param string $second
	 * @param string $logic
	 * @return $this
	 */
	public function notBetween($column, $first, $second, $logic = 'AND')
	{
		$this->where($column, ' NOT BETWEEN ', $first . ' AND ' . $second, $logic);

		return $this;
	}

	/**
	 * Defines 'Or Not Between' operation
	 *
	 * @param string $column
	 * @param array $first
	 * @param string $second
	 * @return $this
	 */
	public function orNotBetween($column, $first, $second)
	{
		$this->notBetween($column, $first, $second, 'OR');

		return $this;
	}

	/**
	 * Fetch a row
	 *
	 * @param string $fetch
	 * @return object|array
	 */
	public function getRow($fetch = 'object')
	{
		$this->_prepare();
		$query = $this->_query($this->sql);

		try {
			if ($fetch == 'array')
				$row = $query->fetch(PDO::FETCH_ASSOC);
			else
				$row = $query->fetch(PDO::FETCH_OBJ);

			return $row;
		} catch (PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Fetch a recordset
	 *
	 * @param string $fetch
	 * @return object|array
	 */
	public function getAll($fetch = 'object')
	{
		$this->_prepare();
		$query = $this->_query($this->sql);

		try {
			if ($fetch == 'array')
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
			else
				$result = $query->fetchAll(PDO::FETCH_OBJ);

			$this->numRows = $query->rowCount();

			return $result;
		} catch (PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Execute a query
	 *
	 * @param string $query
	 * @return object
	 */
	private function _query($query)
	{
		$this->_reset();

		return $this->pdo->query($query);
	}

	/**
	 * Prepare a query
	 *
	 * @return void
	 */
	private function _prepare()
	{
		if (is_null($this->custom))
			$this->sql = rtrim('SELECT ' . $this->select . ' FROM ' . $this->table . ' ' . $this->join . ' ' . $this->where . ' ' . $this->groupBy . ' ' . $this->having . ' ' . $this->orderBy . ' ' . $this->limit);
	}

	/**
	 * Execute a custom query
	 *
	 * @param string $query
	 * @return mixed
	 */
	 public function customQuery($query)
	 {
		 $this->custom 	= true;
		 $this->sql 	= $query;

		 if (stristr($query, 'SELECT')) {
			return $this;
		} else {
			$run = $this->pdo->query($query);
			if (!$run) {
				$this->error = $this->pdo->errorInfo()[2];
				$this->getError();
			} else {
				return $run;
			}
		}
	 }

	/**
	 * Insert a row to table
	 *
	 * @param array $data
	 * @return integer
	 */
	public function insert($data = [])
	{

		if (is_null($this->table))
			throw new ExceptionHandler('DB Hatası', 'INSERT işlemi yapılacak tablo seçilmedi.');

		$insert_sql = 'INSERT INTO ' . $this->table . ' SET ';

		$col 		= [];
		$val 		= [];
		$stmt		= [];

		foreach ($data as $column => $value) {
			$val[] 	= $value;
			$col[] 	= $column . '= ? ';
			$stmt[]	= $column . '=' . $this->_escape($value);
		}

		$this->sql 	= $insert_sql . implode(', ', $stmt);
		$insert_sql .= implode(',', $col);

		try {
			$query 	= $this->pdo->prepare($insert_sql);
			$insert = $query->execute($val);
			$this->insertId = $this->pdo->lastInsertId();

			// reset
			$this->_reset();

			return $insert;
		} catch(PDOException $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Update operations
	 *
	 * @param array $data
	 * @return integer
	 */
	public function update($data = [])
	{
		if (is_null($this->table))
			throw new ExceptionHandler('DB Hatası', 'UPDATE işlemi yapılacak tablo seçilmedi.');

		$update_sql = 'UPDATE ' . $this->table . ' SET ';

		$col 	= [];
		$val 	= [];
		$stmt	= [];

		foreach ($data as $column => $value) {
			$val[] 	= $value;
			$col[] 	= $column . '= ? ';
			$stmt[]	= $column . '=' . $this->_escape($value);
		}

		$this->sql 	= $update_sql . implode(', ', $stmt);
		$update_sql .= implode(',', $col);

		$this->sql 	.= ' ' . $this->where;
		$update_sql	.= ' ' . $this->where;

		try {
			$query 		= $this->pdo->prepare($update_sql);
			$update 	= $query->execute($val);

			// reset
			$this->_reset();

			return $query->rowCount();
		} catch(PDOException $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Delete operations
	 *
	 * @return integer
	 */
	public function delete()
	{
		if (is_null($this->table))
			throw new ExceptionHandler('DB Hatası', 'DELETE işlemi yapılacak tablo seçilmedi.');

		$delete_sql	= 'DELETE FROM ' . $this->table . ' ' . $this->where;
		$this->sql 	= $delete_sql;

		try {
			$query 	= $this->pdo->query($this->sql);

			// reset
			$this->_reset();

			return $query->rowCount();
		} catch(PDOException $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Analyze a table
	 *
	 * @param string $table
	 * @param string $fetch
	 * @return object|array
	 */
	public function analyze($table, $fetch = 'object')
	{
		$this->sql = 'ANALYZE TABLE ' . $this->prefix . $table;
		try {
			$query = $this->pdo->query($this->sql);

			if ($fetch == 'array')
				return $query->fetch(PDO::FETCH_ASSOC);
			else
				return $query->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Check a table
	 *
	 * @param string $table
	 * @param string $fetch
	 * @return object|array
	 */
	public function check($table, $fetch = 'object')
	{
		$this->sql = 'CHECK TABLE ' . $this->prefix . $table;
		try {
			$query = $this->pdo->query($this->sql);

			if ($fetch == 'array')
				return $query->fetch(PDO::FETCH_ASSOC);
			else
				return $query->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/**
	 * CheckSum a table
	 *
	 * @param string $table
	 * @param string $fetch
	 * @return object|array
	 */
	public function checksum($table, $fetch = 'object')
	{
		$this->sql = 'CHECKSUM TABLE ' . $this->prefix . $table;
		try {
			$query = $this->pdo->query($this->sql);

			if ($fetch == 'array')
				return $query->fetch(PDO::FETCH_ASSOC);
			else
				return $query->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Optimize a table
	 *
	 * @param string $table
	 * @param string $fetch
	 * @return object|array
	 */
	public function optimize($table, $fetch = 'object')
	{
		$this->sql = 'OPTIMIZE TABLE ' . $this->prefix . $table;
		try {
			$query = $this->pdo->query($this->sql);

			if ($fetch == 'array')
				return $query->fetch(PDO::FETCH_ASSOC);
			else
				return $query->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Repair a table
	 *
	 * @param string $table
	 * @param string $fetch
	 * @return object|array
	 */
	public function repair($table, $fetch = 'object')
	{
		$this->sql = 'REPAIR TABLE ' . $this->prefix . $table;
		try {
			$query = $this->pdo->query($this->sql);

			if ($fetch == 'array')
				return $query->fetch(PDO::FETCH_ASSOC);
			else
				return $query->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/**
	 * Returns last executed query statement
	 *
	 * @return string
	 */
	public function lastQuery()
	{
		return $this->sql;
	}

	/**
	 * Returns last insert id
	 *
	 * @return integer
	 */
	public function lastInsertId()
	{
		return $this->insertId;
	}

	/**
	 * Returns record count
	 *
	 * @return integer
	 */
	public function numRows()
	{
		return $this->numRows;
	}

	/**
	 * Throw error messages
	 *
	 * @return void
	 */
	public function getError()
	{
		if (null !== $this->error)
			throw new ExceptionHandler('DB Hatası', $this->error);
	}

	/**
	 * Reset all statements
	 *
	 * @return void
	 */
	private function _reset()
	{
		$this->select 		= '*';
		$this->table		= null;
		$this->where		= null;
		$this->limit		= null;
		$this->join			= null;
		$this->orderBy		= null;
		$this->groupBy		= null;
		$this->having		= null;
		$this->custom 		= null;
		$this->numRows		= 0;
		$this->grouped 		= 0;
	}

	function __destruct()
	{
		$this->pdo = null;
	}

}
