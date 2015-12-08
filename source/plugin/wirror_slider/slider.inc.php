<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: admincp.inc.php 18582 2010-11-29 07:12:59Z monkey $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_op = daddslashes($_GET['op']);

function xmlencode($tag){
    $tag = str_replace("&", "&amp;", $tag);
    $tag = str_replace("<", "&lt;", $tag);
    $tag = str_replace(">", "&gt;", $tag);
    $tag = str_replace("'", "&apos;", $tag);
    $tag = str_replace('"', '&quot;', $tag);
    return $tag;
}

//if($_op == "pics_xml"){
    global $_G;
        
    $usergroups = $_G['cache']['plugin']['wirror_slider']['usergroups'];
    $engine = strtolower($_G['cache']['plugin']['wirror_slider']['engine']);
    $player_width = intval($_G['cache']['plugin']['wirror_slider']['player_width']);
    $player_height = intval($_G['cache']['plugin']['wirror_slider']['player_height']);
    $play_cycle = intval($_G['cache']['plugin']['wirror_slider']['play_cycle']);
    
    $pic_width = ($player_width ? $player_width : 960)-40;
    $pic_height = ($player_height ? $player_height : 300)-100;
    $auto_play = $play_cycle ? $play_cycle : 10;
    
    $effects = array(
        '<Transition Pieces="9" Time="1.2" Transition="easeInOutBack" Delay="0.1" DepthOffset="300" CubeDistance="30"></Transition>',
        '<Transition Pieces="15" Time="3" Transition="easeInOutElastic" Delay="0.03" DepthOffset="200" CubeDistance="10"></Transition>',
        '<Transition Pieces="5" Time="1.3" Transition="easeInOutCubic" Delay="0.1" DepthOffset="500" CubeDistance="50"></Transition>',
        '<Transition Pieces="9" Time="1.25" Transition="easeInOutBack" Delay="0.1" DepthOffset="900" CubeDistance="5"></Transition>',
    );
    
    $xml = '';
    $xml .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
    $xml .= '<Piecemaker>'."\n";
    $xml .= '<Contents>'."\n";
    $pics = C::t('#wirror_slider#wirror_slider_pics')->fetch_all_data(true);
    foreach($pics as $pic) {
        $pic['title'] = iconv(CHARSET, 'UTF-8', $pic['title']); 
        $pic['description'] = iconv(CHARSET, 'UTF-8', $pic['description']);  
        $xml .= '<Image Source="'.xmlencode($pic['pic']).'" Title="'.xmlencode($pic['title']).'">'."\n";
        $xml .= "<Text>&lt;h1&gt;$pic[title]&lt;/h1&gt;&lt;p&gt;$pic[description]&lt;/p&gt;</Text>"."\n";
        $xml .= '<Hyperlink URL="'.xmlencode($pic['link']).'" Target="_blank" />'."\n";
        $xml .= '</Image>'."\n";
    }
    $xml .='</Contents>'."\n";
    $xml .='<Settings ImageWidth="'.$pic_width.'" ImageHeight="'.$pic_height.'" LoaderColor="0x333333" InnerSideColor="0x222222" SideShadowAlpha="0.8" DropShadowAlpha="0.7" DropShadowDistance="25" DropShadowScale="0.95" DropShadowBlurX="40" DropShadowBlurY="4" MenuDistanceX="20" MenuDistanceY="50" MenuColor1="0x999999" MenuColor2="0x333333" MenuColor3="0xFFFFFF" ControlSize="100" ControlDistance="20" ControlColor1="0x222222" ControlColor2="0xFFFFFF" ControlAlpha="0.8" ControlAlphaOver="0.95" ControlsX="'.($pic_width/2).'" ControlsY="'.($pic_height-50).'&#xD;&#xA;" ControlsAlign="center" TooltipHeight="30" TooltipColor="0x222222" TooltipTextY="5" TooltipTextStyle="P-Italic" TooltipTextColor="0xFFFFFF" TooltipMarginLeft="5" TooltipMarginRight="7" TooltipTextSharpness="50" TooltipTextThickness="-100" InfoWidth="400" InfoBackground="0xFFFFFF" InfoBackgroundAlpha="0.95" InfoMargin="15" InfoSharpness="0" InfoThickness="0" Autoplay="'.$auto_play.'" FieldOfView="45"></Settings>'."\n";
    $xml .='<Transitions>'."\n";
    foreach($pics as $idx=>$pic){
        if(isset($effects[$idx])){
            $xml .= $effects[$idx]."\n";
        }else{
            $xml .= $effects[0]."\n";
        }
    }
    $xml .='</Transitions>'."\n";
    $xml .='</Piecemaker>'."\n";

    header('Content-Type:text/xml;charset=utf-8;');
    echo $xml;
//}

?>