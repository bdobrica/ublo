<?php
namespace ublo\core;

abstract class _model {
	const HMAC_KEY = '';
	const CACHE_PERSISTENCE = 86400;
	const SORT_ASC = 1;
	const SORT_DESC = -1;

	public static $K = [];
	public static $T = null;
	public static $P = null;
	public static $J = null;

	protected $_db;
	protected $_live;
	protected $_data;
	protected $_list;
	protected $_identifier;
	protected $_table_info;
	
	protected $_debug;

	protected $_last_ids;
	protected $_sort_by;

	protected $_page_sort;
	protected $_page_offset;
	protected $_page_limit;
	protected $_page_total;
	protected $_sort_direction;

	public function __construct ($data = null, $live = false, $debug = 0) {
		$this->_debug = intval ($debug);
		$this->_last_ids = null;
		
		$this->_data = [];
		$this->_list = null;
		$this->_identifier = '';
		
		$this->_sort_by = null;

		$this->_page_sort = null;
		$this->_page_offset = null;
		$this->_page_limit = null;
		$this->_page_total = null;
		$this->_sort_direction = null;

		$this->_skip_columns = null;

		if (isset ($data['_persistence'])) {
			$cache_persistence = intval ($data['_persistence']);
			unset ($data['_persistence']);
		}
		else {
			$cache_persistence = self::CACHE_PERSISTENCE;
		}

		if (isset ($data['_no_cache'])) {
			$cache_persistence = $data['_no_cache'] ? 0 : $cache_persistence;
			unset ($data['_no_cache']);
		}

		if (isset ($data['_sort_by'])) {
		}

		$this->_live = $live === true;
		if ($this->_live) {
			/** begin: _live = true */
			$this->get ('identifier', $data);
			
			$sql = null;
			$parameters = [];

			if (!is_null (static::$_T)) {
				if (is_string (static::$_T)) {
				}
			}
			
			/** end: _live = true */
		}
		else {
			/** begin: _live = false */
			/** end: _live = false */
		}
	}
}
