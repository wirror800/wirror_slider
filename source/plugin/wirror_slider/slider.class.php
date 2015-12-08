<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: slider.class.php 21730 2011-04-11 06:23:46Z lifangming $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_wirror_slider {

	var $pics = array();
	var $width = 960;
	var $height = 300;
	var $cycle = 2;

	function plugin_wirror_slider() {
		global $_G;
		
		$this->usergroups = $_G['cache']['plugin']['wirror_slider']['usergroups'];
		$this->engine = strtolower($_G['cache']['plugin']['wirror_slider']['engine']);
		$player_width = intval($_G['cache']['plugin']['wirror_slider']['player_width']);
		$player_height = intval($_G['cache']['plugin']['wirror_slider']['player_height']);
		$play_cycle = intval($_G['cache']['plugin']['wirror_slider']['play_cycle']);
		$this->width = $player_width ? $player_width : 960;
		$this->height = $player_height ? $player_height : 300;
		$this->cycle = $play_cycle ? $play_cycle : 2;
		
		foreach(C::t('#wirror_slider#wirror_slider_pics')->fetch_all_data(true) as $pic) {
			$this->pics[] = array('src'=>$pic['pic'], 'link'=>$pic['link'], 'title'=>$pic['title'], 'desc'=>$pic['description']);
		}
	}
	
	/*
	function footer() {}
	function footer_output() {}

	function global_footer() {
		global $_G;
		if('plugin' == CURSCRIPT AND 'wirror_slider' == CURMODULE){
			include template('wirror_slider:js');
			return $return;
		}else{
			return "";
		}
	}
	*/

}

class plugin_wirror_slider_forum extends plugin_wirror_slider {
	//custom
	function index_top_diy_output(){
		global $_G;
		$template_dir = '/source/plugin/wirror_slider/template/';
		$width = $this->width;
		$height = $this->height;
		$cycle = $this->cycle;
		$host = 'http://' . $_SERVER['SERVER_NAME'];
		
		if(!empty($this->pics)){
			switch($this->engine){
				case 'js':
					include template('wirror_slider:js');
					break;
				case 'flash':
					include template('wirror_slider:flash');
					break;
				default:
					include template('wirror_slider:js');
					break;
				
			}
			
			return $return;
		}
	}
	
	//preprocess
	function index_top(){
		global $_G;
		//do sth.
		return '';
	}
	
	//output
	function index_top_output(){
		global $_G;
		//do sth.
		return '';
	}

}

?>