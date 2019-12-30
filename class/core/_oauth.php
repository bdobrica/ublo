<?php
namespace ublo\core;

class _oauth {
	const	TOKEN_LIFETIME		= 86400;

	private static $_grant_types = [
		'password',
		'client_credentials',
	];

	private $_token;

	public function __construct ($data = null) {
		$token = $this->get ('header', 'accesstoken');
		if ($token === false) {
			$post = $this->get ('post');
			if (empty ($post)) {
				throw new \Exception ('error: ublo\core\_oauth: missing oauth parameters');
			}
			$grant_type = $post['grant_type'];
			$grant_type = strtolower (trim ($grant_type));
			if (!in_array ($grant_type, self::$_grant_types)) {
				throw new \Exception ('error: ublo\core\_oauth: invalid grant type request');
			}

			$username = isset ($post['username']) ? $post['username'] : null;
			$password = isset ($post['password']) ? $post['password'] : null;
			$scope = isset ($post['scope']) ? $post['scope'] : null;

			$user = new _user (['username' => $username, 'password' => $password]);
			if (!$user->has ('access', 'RPC_OAUTH')) {
				throw new \Exception ('error: ublo\core\_oauth: user access restricted to RPC/OAuth');
			}

			$this->_token = new \ublo\db\OAuthToken ([
				'token_created'		=> (string) (new _date()),
				'token_user_id'		=> $user->get (),
				'token_user_type'	=> $user->get ('user_type'),
				'token_data'		=> sha1 (uniqid (mt_rand (), true)),
				'token_request_ip'	=> (new _client())->get ('ip'),
			]);

			try {
				$this->_token->create ();
			}
			catch (\Exception $e) {
				throw new \Exception ('error: ublo\core\_oauth: could not store OAuth token');
			}
			return;
		}

		$this->_token = new \ublo\db\oauthtoken (['token_data' => $token], true);
		if ($this->_token->is ('empty')) {
			throw new \Exception ('error: ublo\core\_oauth: invalid access token');
		}

		if ($this->_token->get ('token_created')->get ('time') + self::TOKEN_LIFETIME > (new _date())->get ('time')) {
			throw new \Exception ('error: ublo\core\_oauth: access token expired');
		}
	}

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
			case 'post':
				$json = file_get_contents ('php://input');
				$data = json_decode ($json);
				if (empty ($data) && !empty ($_POST)) {
					$data = $_POST;
				}
				return $data;
				break;
			case 'header':
				$headers = [];
				foreach ($_SERVER as $key => $value) {
					$key = str_replace ('_', '-', strtolower (substr ($key, 5)));
					$headers[$key] = $value;
				}
				if (is_string ($opts) && isset ($headers[$opts])) {
					return $headers[$opts];
				}
				return false;
				break;
			case 'access_token':
				return
					json_encode ((object) [
					'access_token' => $this->_token->get ('token_data'),
					'expires_in' => self::TOKEN_LIFETIME,
					]);
				break;
			}
		}
		throw new Exception ('error: ublo\core\_oauth->get: invalid arguments');
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
		throw new Exception ('error: ublo\core\_oauth->set: invalid arguments');
	}

	public function out ($key = null, $opts = null) {
		$out = $this->get ($key, $opts);
		if (is_scalar ($out)) {
			echo $out;
		}
		return $this;
	}
}
