<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_wirror_slider_pics.php 31511 2012-09-04 07:10:47Z monkey $
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_wirror_slider_pics extends discuz_table
{
	public function __construct() {

		$this->_table = 'wirror_slider_pics';
		$this->_pk    = 'id';

		parent::__construct();
	}

	public function fetch_all_data($enabled = true) {
		$rows = parent::range(0, 100, 'seq');
		if($rows){
			foreach ($rows as $k=>$row) {
				if($row['deleted']){
					unset($rows[$k]);
				}

				if($enabled && $row['enabled']==0){
					unset($rows[$k]);
				}
			}
		}
		return $rows;
	}
	
	public function update($id, $base) {
		parent::update($id, $base);
		manyoulog('wirror_slider_pics', $id, 'edit');
	}
	
	public function insert($base) {
		if(!isset($base['inserttime']))
			$base['inserttime'] = date('Y-m-d H:i:s');
		
		$picId = parent::insert($base, true);
		manyoulog('wirror_slider_pics', $picId, 'add');
	}
	
	public function delById($id) {
		parent::update($id, array('deleted'=>1));
		manyoulog('wirror_slider_pics', $id, 'delete');
	}
}

?>