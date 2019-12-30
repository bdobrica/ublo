<?php
namespace ublo\core;

class _url {
	private static $_schemes = [
		'http'		=> 80,
		'https'		=> 443,
		'ftp'		=> 21,
	];

	public static $K = [
		'scheme',
		'host',
		'port',
		'user',
		'pass',
		'path',
		'query',
		'fragment',
	];

	private $_data;
	private $_debug;

	public function __construct ($data = null) {
		if (is_null ($data)) {
			$data =
				(isset ($_SERVER['HTTPS']) ? 'https' : 'http') .
				'://' .
				$_SERVER['HTTP_HOST'] .
				$_SERVER['REQUEST_URI'];
		}
		if (is_null ($data) || !is_string ($data)) {
			throw new \Exception ('error: ublo\core\_url: the constructor parameter is not a string');
		}

		$this->_data = parse_url ($data);
		if (isset ($this->_data['query'])) {
			parse_str ($this->_data['query'], $this->_data['query']);
		}
		if (
			!isset ($this->_data['port']) &&
			isset ($this->_data['scheme']) &&
			isset (self::$_schemes[$this->_data['scheme']])
		) {
			$this->_data['port'] = self::$_schemes[$this->_data['scheme']];
		}
	}

	public function get ($key = null, $opts = null) {
		if (is_null ($key)) {
			$pieces = array_merge ($this->_data, (is_array ($opts) && !empty ($opts)) ? $opts : []);
			return
				(isset ($pieces['scheme']) ? ($pieces['scheme'] . '://') : '') .
				(isset ($pieces['host']) ? $pieces['host'] : '') .
				(isset ($pieces['path']) ? $pieces['path'] : '') .
				(
					empty ($pieces['query']) ?
					'' :
					('?' . $this->_http_build_query ($pieces['query']))
				) .
				(
					isset ($pieces['fragment']) ?
					'#' . $pieces['fragment'] :
					''
				);
		}
		if (is_string ($key)) {
			switch ($key) {
			case 'domain':
				$pieces = explode ('.', $this->_data['host']);
				if (sizeof ($pieces) < 3) {
					return $this->_data['host'];
				}
				while (sizeof ($pieces) > 2) {
					array_shift ($pieces);
				}
				return implode ('.', $pieces);
				break;
			case 'socket_host':
				return
					$this->_data['scheme'] == 'https' ?
					'ssl://' . $this->_data['host'] :
					$this->_data['host'];
				break;
			case 'query':
				return $this->_data['query'];
				break;
			case 'query_string':
				return $this->_http_build_query ();
				break;
			case 'path':
				if (
					!isset ($this->_data['query']) ||
					empty ($this->_data['query'])
				) {
					return
						isset ($this->_data['path']) ?
						$this->_data['path'] :
						'/';
				}
				return
					(
					isset ($this->_data['path']) ?
					$this->_data['path'] :
					'/'
					) .
					'?' .
					$this->_http_build_query ();
				break;
			default:
				if (!in_array ($key, self::$K)) {
					throw new \Exception ('error: ublo\core\_url->get: invalid key = ' . $key);
				}
				return isset ($this->_data[$key]) ? $this->_data[$key] : null;
			}
		}
		throw new Exception ('error: ublo\core\_url->get: invalid arguments');
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
		throw new Exception ('error: ublo\core\_url->set: invalid arguments');
	}

	public function __to_string () {
		return $this->get ();
	}
	
	private function _http_build_query ($data = null) {
		return preg_replace (
			'/%5B[0-9]+%5D/',
			'%5B%5D',
			http_build_query (is_null ($data) ? $this->_data['query'] : $data)
		);
	}

	public static function dot ($str, $num = 3, $dot = '.') {
		$c = 0;
		$offset = 0;
		$len = strlen ($str);
		if ($len < $num) {
			return $str;
		}
		while (
			($c < $num) &&
			(($pos = strpos ($str, $dot, -$offse)) !== false)
		) {
			$offset = $len - $pos + 1;
			$c++;
		}

		return $offset > 0 ? substr ($str, 2 - $offset) : $str;
	}
}
