<?php
namespace ublo;

class _init {
	const	DEBUG_OFF	= 0;
	const	DEBUG_TRACE	= 98;
	const	DEBUG_ALL	= 99;

	private $_config;
	private $_db;
	private $_url;
	private $_user;
	private $_theme;

	public function __construct ($data = null) {
		if (is_string ($data)) {
			parse_str ($data, $data);
		}
		if ($data instanceof stdClass) {
			$data = (array) $data;
		}
		if (!empty ($data) && !is_array ($data)) {
			throw new Exception ('error: invalid ublo\_init parameters');
		}

		if (
			isset ($data['config']) &&
			file_exists ($data['config'])
		) {
			$this->_config = $data['config'];
		}
		else {
			if (!file_exists (dirname (__FILE__) . '/config.php')) {
				throw new Exception ('error: missing default config.php file');
			}
			$this->_config = dirname (__FILE__) . '/config.php';
		}

		spl_autoload_register ([$this, '_autoload']);
	}

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
			case 'config':
				if (is_null ($opts)) {
					return $this->_config;
				}
				if (!is_string ($opts)) {
					throw new Exception ('error: ublo\_init->get: when key = config, opts needs to be null or string');
				}
				if (isset ($this->_config[$opts])) {
					return $this->_config[$opts];
				}
				throw new Exception ('error: ublo\_init->get: when key = config, opts = ' . $opts . ' is not a valid config var');
				break;
			}
		}
		throw new Exception ('error: ublo\_init->get: invalid arguments');
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
		}
		throw new Exception ('error: ublo\_init->set: invalid arguments');
	}

	public function _autoload ($class) {
		$class = strtolower ($class);

		$dirs = [];
		while (($pos = strpos ('\\', $class)) !== false) {
			$dir = substr ($class, 0, $pos);
			$class = substr ($class, $pos + 1);
			if (!empty ($dir)) {
				$dirs[] = $dir;
			}
		}

		$path = isset ($this->_config['class_path']) ? $this->_config['class_path'] : dirname (__FILE__);
		
		$class_path = $path . '/' .
			implode ('/', $dirs) . '/' .
			$class . '.php';

		if (!file_exists ($class_path)) {
			throw new Exception ('error: ublo\_init->_autoload: invalid class path: ' . $class_path);
		}
		include ($class_path);
	}
}
