<?php
namespace ublo\core;

class _db {
	private $_config;
	private $_conn;
	private $_query;
	private $_sql;
	private $_parameters;

	private $_affected_rows;

	private $_fetch_mode;

	private $_debug;
	

	public function __construct ($data = null) {
		if (is_string ($data)) {
			parse_str ($data, $data);
		}
		if ($data instanceof stdClass) {
			$data = (array) $data;
		}
		if (empty ($data)) {
			throw new \Exception ('error: ublo\core\_db invalid constructor config');
		}
		$this->_config = [
			'driver'	=> isset ($data['driver']) ? $data['driver'] : 'mysql',
			'host'		=> isset ($data['host']) ? $data['host'] : 'localhost',
			'port'		=> isset ($data['port']) ? $data['port'] : null,
			'db_name'	=> isset ($data['db_name']) ? $data['db_name'] : 'ublo',
			'db_user'	=> isset ($data['db_user']) ? $data['db_user'] : 'root',
			'db_pass'	=> isset ($data['db_pass']) ? $data['db_pass'] : ''
		];
		$this->_conn = null;
		$this->_fetch_mode = \PDO::FETCH_ASSOC;
	}

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
			case 'config':
				if (is_null ($opts)) {
					return $this->_config;
				}
				if (!is_string ($opts)) {
					throw new \Exception ('error: ublo\core\_db->get: when key = config, opts needs to be null or string');
				}
				if (isset ($this->_config[$opts])) {
					return $this->_config[$opts];
				}
				throw new \Exception ('error: ublo\core\_db->get: when key = config, opts = ' . $opts . ' is not a valid config var');
				break;
			case 'all':
			case 'rows':
				if (is_null ($this->_conn)) {
					throw new \Exception ('error: ublo\core\_db->get: when key = ' . $key . ', the connection was not opened');
				}
				if (!($this->_query instanceof \PDOStatement)) {
					throw new \Exception ('error: ublo\core\_db->get: when key = ' . $key . ', the query was not prepared');
				}
				return $this->_query->fetchAll ($this->_fetch_mode);
				break;
			case 'row':
				if (is_null ($this->_conn)) {
					throw new \Exception ('error: ublo\core\_db->get: when key = ' . $key . ', the connection was not opened');
				}
				if (!($this->_query instanceof \PDOStatement)) {
					throw new \Exception ('error: ublo\core\_db->get: when key = ' . $key . ', the query was not prepared');
				}
				return $this->_query->fetch ($this->_fetch_mode);
				break;
			case 'rows_affected':
			case 'affected_rows':
				return $this->_affected_rows;
				break;
			case 'last_insert_id':
			case 'last_inserted_id':
				return $this->_conn->lastInsertId();
				break;
			}
		}
		throw new \Exception ('error: ublo\core\_db->get: invalid arguments');
	}

	public function set ($key = null, $value = null) {
		if (is_string ($key) && is_null ($value)) {
			parse_str ($key, $key);
		}
		if (is_array ($key)) {
			foreach ($key as $_key => $_value) {
				$this->set ($key, $value);
			}
			return $this;
		}
		if (is_string ($key)) {
			switch ($key) {
			case 'debug':
				$this->_debug = intval ($value);
				break;
			case 'query':
				$this->_sql = '';
				$this->_parameters = [];
				$this->_affected_rows = -1;

				if ($value instanceof stdClass) {
					$value = (array) $value;
				}
				if (is_array ($value)) {
					$this->_sql = isset ($value['sql']) ? $value['sql'] : '';
					$this->_parameters = isset ($value['parameters']) ? $value['parameters'] : '';
				}
				else {
					if (is_string ($value)) {
						$this->_sql = $value;
					}
				}
				if (empty ($this->_sql)) {
					throw new \Exception ('error: ublo\core\_db->set: when key = query, opts should contain a valid sql query');
				}
				if ($this->_query instanceof \PDOStatement) {
					$this->_query = null;
					unset ($this->_query);
				}

				$this->_connect ();
				$this->_query = $this->_conn->prepare ($this->_sql);
				$this->_query->execute ($this->_parameters);
				$this->_affected_rows = $this->_query->rowCount();
				
				return $this;
				break;
			case 'params':
			case 'parameters':
				$this->_parameters = [];
				$this->_affected_rows = -1;
				
				if (empty ($this->_sql)) {
					throw new \Exception ('error: ublo\core\_db->set: when key = ' . $key . ', expects sql query to be previously provided');
				}
				if (!($this->_query instanceof \PDOStatement)) {
					$this->_connect ();
					$this->_query = $this->_conn->prepare ($this->_sql);
				}

				$this->_parameters = $value;
				$this->_query->execute ($this->_parameters);
				$this->_affected_rows = $this->_query->rowCount();

				return $this;
				break;
			case 'fetch_mode':
				if (is_scalar ($value)) {
					switch ($value) {
					case \PDO::FETCH_ASSOC:
					case \PDO::FETCH_BOTH:
					case \PDO::FETCH_NUM:
					case \PDO::FETCH_OBJ:
						$this->_fetch_mode = $value;
						break;
					}
				}
				break;
			}
		}
		throw new \Exception ('error: ublo\core\_db->set: invalid arguments');
	}

	public function is ($what = null, $opts = null) {
		return $this->_conn instanceof \PDO;
	}

	private function _connect () {
		if (!is_null ($this->_conn)) {
			return;
		}

		try {
			$this->_conn = new \PDO (
				$this->_config['driver'] . ':' .
				'host=' . $this->_config['host'] . ';' .
				(
					!is_null ($this->_config['port']) ?
					'port=' . $this->_config['port'] . ';' :
					''
				) .
				'dbname=' . $this->_config['db_name'],
				$this->_config['db_user'],
				$this->_config['db_pass']
			);
		}
		catch (\PDOException $e) {
			throw new \Exception ('error: ublo\core\_db invalid connection config' . PHP_EOL . $e->getMessage ());
		}
	}

	private function _disconnect () {
		if (!is_null ($this->_query)) {
			$this->_query = null;
			unset ($this->_query);
		}

		if (!is_null ($this->_conn)) {
			$this->_conn = null;
			unset ($this->_conn);
		}
	}

	public function __destruct () {
		$this->_disconnect ();
	}
}
