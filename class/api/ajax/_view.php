<?php
namespace ublo\api\ajax;

class _view extends \ublo\api\_ajax {
	private $_header;
	private $_body;
	private $_footer;

	private $_reload;
	private $_fields;
	private $_selector;
	private $_row;
	private $_keep;

	public function get ($key = null, $opts = null) {
		if (is_null ($key)) {
			return json_encode ((object) [
				'header'	=> $this->_header,
				'body'		=> $this->_body,
				'footer'	=> $this->_footer,
				'reload'	=> $this->_reload,
				'fields'	=> $this->_fields,
				'selector'	=> $this->_selector,
				'row'		=> $this->_row,
				'keep'		=> $this->_keep
			]);
		}
		return parent::get ($key, $value);
	}

	public function set ($key = null, $value = null) {
		return parent::set ($key, $value);
	}
}
