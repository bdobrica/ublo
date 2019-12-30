<?php
namespace ublo\core;
abstract class _api {
	public function __construct ($data = null) {
	}

	public function create () {
		return $this;
	}

	public function read () {
		return $this;
	}

	public function update () {
		return $this;
	}

	public function delete () {
		return $this;
	}

	public function __destruct () {
	}
}
