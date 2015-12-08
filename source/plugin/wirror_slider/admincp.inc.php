<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: admincp.inc.php 18582 2010-11-29 07:12:59Z monkey $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$operation = daddslashes($_GET['op']);
$lang = is_array($lang) ? array_merge($lang, $scriptlang['wirror_slider']) : $scriptlang['wirror_slider'];
$extra = '';
cpheader();

function xmlencode($tag){
	$tag = str_replace("&", "&amp;", $tag);
	$tag = str_replace("<", "&lt;", $tag);
	$tag = str_replace(">", "&gt;", $tag);
	$tag = str_replace("'", "&apos;", $tag);
	$tag = str_replace('"', '&quot;', $tag);
	return $tag;
}

function genXml(){
	//forbit
}

if(empty($operation)) {

	if(!submitcheck('picsubmit')) {
		/*
		shownav('plugin', 'slider', 'image_manage');
		showsubmenu('slider_manage', array(
			array('image_manage', "plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp$extra", 1),
			array('image_add', "plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp&op=add$extra", 0),
			array('engine_manage', "plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp&op=engine$extra", 0)
		));*/
		showtips('slider_tips');
		showformheader("plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp$extra");
		showtableheader();
		showsubtitle(array('del', 'title', 'picture', 'enabled', 'operator', 'update_time', 'operation'));

		foreach(C::t('#wirror_slider#wirror_slider_pics')->fetch_all_data(false) as $pic) {
			showtablerow('', array('class="td25"', 'class="td31"', 'class="td31"', 'class="td32"', 'class="td32"', 'class="td31"'), array(
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$pic[id]\">",
				$pic['title'],
				"<a href='$pic[link]'><img width='60' height='40' src='$pic[pic]'/></a>",
				"<input class=\"checkbox\" type=\"checkbox\" name=\"enabled[$pic[id]]\" value=\"1\" ".($pic['enabled'] ? 'checked' : '').">",
				$pic['operator'],
				$pic['updatetime'] ? $pic['updatetime'] : $lang['unlimited'],
				"<a href=\"".ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp&op=edit&pic_id={$pic[id]}$extra\" class=\"act\">$lang[edit]</a>"
			));
		}
		
		showsubmit('picsubmit', 'submit', 'select_all','<input type="button" class="btn" value="'.$lang['add'].'" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=wirror_slider&pmod=admincp&op=add'.$extra.'\'"/> &nbsp;<input type="button" class="btn" style="display:none;" value="'.$lang['gen_xml'].'" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=wirror_slider&pmod=admincp&op=gen_xml'.$extra.'\'"/>');
		showtablefooter();
		showformfooter();

	} else {
		if(is_array($_G['gp_delete'])) {
			foreach($_G['gp_delete'] as $pic) {
				C::t('#wirror_slider#wirror_slider_pics')->delById($pic);
			}
		}
		genXml();
		cpmsg('pic_remove_succeed', "action=plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp$extra", 'succeed');

	}

} elseif($operation == 'add') {

	if(!submitcheck('addsubmit')) {
		shownav('plugin', 'slider', 'image_add');
		showformheader("plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp&op=add$extra", 'enctype');
		showtableheader('image_add');
		
		showsetting('title', 'title', '', 'text');
		showsetting('description', 'description', '', 'textarea', '', 0, $lang['allow_empty']);
		
		showsetting('picture', array('isupload', array(
			array(1, $lang['pic_upload'], array('uploadtxt' => '','linktxt' => 'none')),
			array(0, $lang['pic_src'], array('uploadtxt' => 'none','linktxt' => ''))
		)), 1, 'mradio');
		showtagheader('tbody', 'uploadtxt', 1, 'sub');
		showsetting('picture', 'pic_file', '', 'file');
		showtagfooter('tbody');
		showtagheader('tbody', 'linktxt',  0, 'sub');
		showsetting('link', 'pic', '', 'text');
		showtagfooter('tbody');
		
		showsetting('link', 'link', '#', 'text');
		showsetting('seq', 'seq', '0', 'text');
		showsetting('enabled', 'enabled', '1', 'radio');
		
		showsubmit('addsubmit','submit','','<input type="button" class="btn" value="'.$lang['return'].'" onclick="history.go(-1)"/>');
		showtablefooter();
		showformfooter();
	} else {
		if($_FILES['pic_file'] AND is_uploaded_file($_FILES['pic_file']['tmp_name'])){
			$upload = new discuz_upload();
			$pic = '';
			if($upload->init($_FILES['pic_file'], 'common') && $upload->save(1)) {
				$pic = (!strstr($_G['setting']['attachurl'], '://') ? $_G['siteurl'] : '').$_G['setting']['attachurl'].'common/'.$upload->attach['attachment'];
			}
			/*//upload by yourself
			$pic = '';
			$dest_dir = DISCUZ_ROOT.'./uploads';
			if( !is_dir($dest_dir) || !is_writeable($dest_dir) ){
				cpmsg('wirror_slider:dir_err', '', 'error', array('dir'=>$dest_dir));
			}
			
			$type=array("png","gif","jpg");
			$upfile = &$_FILES['pic_file'];
			
			$filetype = strtolower( pathinfo($upfile['name'], PATHINFO_EXTENSION) );
			if( !in_array( $filetype,$type) ){
				$text=implode(",",$type);
				cpmsg('wirror_slider:type_err', '', 'error', array('types'=>$text));
			}else{
				$source = $upfile['tmp_name'];
				$newFileName = date("ymdHis")."_".$upfile['name'];
				$target = $dest_dir.'/'.$newFileName;
				if(!(is_uploaded_file($source) || is_uploaded_file(str_replace('\\\\', '\\', $source)))) {
					$succeed = false;	
				}elseif(@copy($source, $target)) {
					$succeed = true;
				}elseif(function_exists('move_uploaded_file') && @move_uploaded_file($source, $target)) {
					$succeed = true;
				}elseif (@is_readable($source) && (@$fp_s = fopen($source, 'rb')) && (@$fp_t = fopen($target, 'wb'))) {
					while (!feof($fp_s)) {
						$s = @fread($fp_s, 1024 * 512);
						@fwrite($fp_t, $s);
					}
					fclose($fp_s); 
					fclose($fp_t);
					$succeed = true;
				}
				if ($succeed){
					@chmod($target, 0644); 
					@unlink($source);
					
					$pic = '/uploads/'.$newFileName;
				}else{
					switch($upfile['error']){
						case 1 : $err = $lang['error']['upload_max_err'];
						case 2 : $err = $lang['error']['max_size_err'];
						case 3 : $err = $lang['error']['part_err'];
						case 4 : $err = $lang['error']['no_file_err'];
						case 5 : $err = $lang['error']['no_tmp_err'];
						case 6 : $err = $lang['error']['no_right_err'];
					}
					
					cpmsg($err, '', 'error');
				}
			}
			*/
		}else{
			$pic = $_POST['pic'];
		}
		
		//do insert
		$data['title'] = $_POST['title'];
		$data['description'] = $_POST['description'];
		$data['link'] = $_POST['link'] ? $_POST['link'] : '#';
		$data['seq'] = $_POST['seq'] ? $_POST['seq'] : 0;
		$data['enabled'] = $_POST['enabled'] ? 1 : 0;
		$data['isupload'] = $_POST['isupload'] ? 1 : 0;
		$data['pic'] = $pic;
		$data['operator'] = $_G['username'];
		
		C::t('#wirror_slider#wirror_slider_pics')->insert($data);
		
		updatecache('setting');
		genXml();
		cpmsg('pic_add_succeed', "action=plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp$extra", 'succeed');
	}

} elseif($operation == 'edit') {

	if(!submitcheck('editsubmit')) {
		shownav('plugin', 'slider', 'image_add');
		showformheader("plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp&op=edit$extra", 'enctype');
		showtableheader('image_edit');
		
		$entity = C::t('#wirror_slider#wirror_slider_pics')->fetch($_GET['pic_id'], true);
		
		echo '<input type="hidden" name="id" value="'.$entity['id'].'"/>';
		showsetting('title', 'title', $entity['title'], 'text');
		showsetting('description', 'description', $entity['description'], 'textarea', '', 0, $lang['allow_empty']);
		
		showsetting('picture', array('isupload', array(
			array(1, $lang['pic_upload'], array('uploadtxt' => '','linktxt' => 'none')),
			array(0, $lang['pic_src'], array('uploadtxt' => 'none','linktxt' => ''))
		)), $entity['isupload'], 'mradio');
		showtagheader('tbody', 'uploadtxt', $entity['isupload']==1, 'sub');
		showsetting('picture', 'pic_file', '', 'file');
		showtagfooter('tbody');
		showtagheader('tbody', 'linktxt',  $entity['isupload']==0, 'sub');
		showsetting('link', 'pic', $entity['pic'], 'text');
		showtagfooter('tbody');
		
		showsetting('link', 'link', $entity['link'], 'text');
		showsetting('seq', 'seq', $entity['seq'], 'text');
		showsetting('enabled', 'enabled', $entity['enabled'], 'radio');
		
		showsubmit('editsubmit','submit','','<input type="button" class="btn" value="'.$lang['return'].'" onclick="history.go(-1)"/>');
		showtablefooter();
		showformfooter();
	} else {
		if($_FILES['pic_file'] AND is_uploaded_file($_FILES['pic_file']['tmp_name'])){
			$upload = new discuz_upload();
			$pic = '';
			if($upload->init($_FILES['pic_file'], 'common') && $upload->save(1)) {
				$pic = (!strstr($_G['setting']['attachurl'], '://') ? $_G['siteurl'] : '').$_G['setting']['attachurl'].'common/'.$upload->attach['attachment'];
			}
			/*//upload by yourself
			$pic = '';
			$dest_dir = DISCUZ_ROOT.'./uploads';
			if( !is_dir($dest_dir) || !is_writeable($dest_dir) ){
				cpmsg('wirror_slider:dir_err', '', 'error', array('dir'=>$dest_dir));
			}
			
			$type=array("png","gif","jpg");
			$upfile = &$_FILES['pic_file'];
			
			$filetype = strtolower( pathinfo($upfile['name'], PATHINFO_EXTENSION) );
			if( !in_array( $filetype,$type) ){
				$text=implode(",",$type);
				cpmsg('wirror_slider:type_err', '', 'error', array('types'=>$text));
			}else{
				$source = $upfile['tmp_name'];
				$newFileName = date("ymdHis")."_".$upfile['name'];
				$target = $dest_dir.'/'.$newFileName;
				if(!(is_uploaded_file($source) || is_uploaded_file(str_replace('\\\\', '\\', $source)))) {
					$succeed = false;	
				}elseif(@copy($source, $target)) {
					$succeed = true;
				}elseif(function_exists('move_uploaded_file') && @move_uploaded_file($source, $target)) {
					$succeed = true;
				}elseif (@is_readable($source) && (@$fp_s = fopen($source, 'rb')) && (@$fp_t = fopen($target, 'wb'))) {
					while (!feof($fp_s)) {
						$s = @fread($fp_s, 1024 * 512);
						@fwrite($fp_t, $s);
					}
					fclose($fp_s); 
					fclose($fp_t);
					$succeed = true;
				}
				if ($succeed){
					@chmod($target, 0644); 
					@unlink($source);
					
					$pic = '/uploads/'.$newFileName;
				}else{
					switch($upfile['error']){
						case 1 : $err = $lang['error']['upload_max_err'];
						case 2 : $err = $lang['error']['max_size_err'];
						case 3 : $err = $lang['error']['part_err'];
						case 4 : $err = $lang['error']['no_file_err'];
						case 5 : $err = $lang['error']['no_tmp_err'];
						case 6 : $err = $lang['error']['no_right_err'];
					}
					
					cpmsg($err, '', 'error');
				}
			}
			*/
		}else{
			$pic = $_POST['pic'];
		}
		
		//do update
		$id = $_POST['id'];
		$data['title'] = $_POST['title'];
		$data['description'] = $_POST['description'];
		$data['link'] = $_POST['link'] ? $_POST['link'] : '#';
		$data['seq'] = $_POST['seq'] ? $_POST['seq'] : 0;
		$data['enabled'] = $_POST['enabled'] ? 1 : 0;
		$data['isupload'] = $_POST['isupload'] ? 1 : 0;
		$data['pic'] = $pic;
		$data['operator'] = $_G['username'];
		
		C::t('#wirror_slider#wirror_slider_pics')->update($id, $data);
		
		updatecache('setting');
		genXml();
		cpmsg('pic_edit_succeed', "action=plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp$extra", 'succeed');
	}

}elseif($operation == 'gen_xml') {
	genXml();
	cpmsg('xml_gen_succeed', "action=plugins&operation=config&do=$pluginid&identifier=wirror_slider&pmod=admincp$extra", 'succeed');
}
echo <<<EOT
<script type="text/javascript" src="static/js/calendar.js"></script>
<script type="text/JavaScript">
function change_title(type) {
	if(type == 'bold') {
		old = $('newsubject').value.replace(/<b>(.*?)<\/b>/i, '$1');
		if(old == $('newsubject').value) {
			$('newsubject').value = '<b>'+old+'</b>';
		} else {
			$('newsubject').value = old;
		}
	} else if(type == 'italic') {
		old = $('newsubject').value.replace(/<i>(.*?)<\/i>/i, '$1');
		if(old == $('newsubject').value) {
			$('newsubject').value = '<i>'+old+'</i>';
		} else {
			$('newsubject').value = old;
		}
	} else if(type == 'underline') {
		old = $('newsubject').value.replace(/<u>(.*?)<\/u>/i, '$1');
		if(old == $('newsubject').value) {
			$('newsubject').value = '<u>'+old+'</u>';
		} else {
			$('newsubject').value = old;
		}
	}
}

function change_choose(id) {
	className = $(id).className;
	if(className == '') {
		$(id).className = 'a';
	} else {
		$(id).className = '';
	}
}

function title_replace(a) {
	old = $('newsubject').value;
	old = old.replace(/<font(.*?)>(.*?)<\/font>/i, '$2');
	if(a) {
		$('newsubject').value = '<font color='+a+'>'+old+'</font>';
	} else {
		$('newsubject').value = old;
	}
}

function change_title_color(hlid) {
	var showid = hlid;
	if(!$(showid + '_menu')) {
		var str = '';
		var coloroptions = {'0' : '#000', '1' : '#EE1B2E', '2' : '#EE5023', '3' : '#996600', '4' : '#3C9D40', '5' : '#2897C5', '6' : '#2B65B7', '7' : '#8F2A90', '8' : '#EC1282'};
		var menu = document.createElement('div');
		menu.id = showid + '_menu';
		menu.className = 'cmen';
		menu.style.display = 'none';
		for(var i in coloroptions) {
			str += '<a href="javascript:;" onclick="title_replace(\'' + coloroptions[i] + '\');$(\'' + showid + '\').style.backgroundColor=\'' + coloroptions[i] + '\';hideMenu(\'' + menu.id + '\')" style="background:' + coloroptions[i] + ';color:' + coloroptions[i] + ';">' + coloroptions[i] + '</a>';
		}
		menu.innerHTML = str;
		$('append_parent').appendChild(menu);
	}
	showMenu({'ctrlid':hlid + '_ctrl','evt':'click','showid':showid});

}

</script>
EOT;

?>