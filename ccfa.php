<?php
/*
Plugin Name: CodyCast Featured Audio
Plugin URI: http://www.cmtradiolive.com
Description: Custom Plugin to play MP3 files from a specific category.
Version: 1.0
Author: Zack Shutt
Author URI: http://www.cmtradiolive.com
*/
?>
<?php
function ccfa_add_pages() {
    add_options_page('CCFA', 'CCFA', 8, 'ccfa-settings', 'ccfa_settings_page');
}

function ccfa_settings_page() {
    require("ccfa-settings.php");
}

function ccfa_show_player() {
	$url = get_bloginfo('wpurl').'/';
	if (substr($url, -1, 1)=='/') $url = substr($url, 0, -1);
	static $unique_id = 0;
	$result = '';
	$audiocat = get_option(ccfa_opt_audiocat);
	$logourl = get_option(ccfa_opt_logourl);
	$playerwidth = get_option(ccfa_opt_playerwidth);
	query_posts('cat='. $audiocat . '&showposts=1');
	while (have_posts()) : the_post();
		ob_start();
		the_content();
		$mp3url = ob_get_clean();
		$mp3url = str_replace("<p>", "", $mp3url);
		$mp3url = str_replace("</p>", "", $mp3url);
		$mp3url = substr($mp3url, 0, -1);
		if($logourl) {
			echo '<img src="' . $logourl . '" style="margin: 0 0 -7px -5px" />';
		}
		$unique_id++;
		if ($unique_id==1) {
		$result .= <<<EOD
<script type="text/javascript" src="$url/wp-content/plugins/ccfa/swfobject.js"></script>
EOD;
		}
		$result .= <<<EOD
<span id="mp3_{$unique_id}"><a href="http://www.adobe.com/products/flashplayer/" target="_blank">Go get Adobe Flash Player!</a></span>
<script type="text/javascript">
	var so = new SWFObject(
		"$url/wp-content/plugins/ccfa/mp3player.swf",
		"mp3_player", "$playerwidth", "20", "8", "#FFFFFF");
	so.addVariable("file", "$mp3url");
	so.addVariable("width", "$playerwidth");
	so.write("mp3_{$unique_id}");
</script>
EOD;
		echo $result;
	endwhile;
	wp_reset_query();
}

function replace_links($content) {
	$audiocat = get_option("ccfa_opt_audiocat");
	$playerwidth = get_option("ccfa_opt_playerwidth");
	foreach((get_the_category()) as $category) {
		if(is_single() && !is_home() && ($category->cat_ID == $audiocat)) {
			$content = str_replace("<p>", "", $content);
			$content = str_replace("</p>", "", $content);
$content = substr($content, 0, -1);
			$content = '[mp3]' . $content . '[/mp3]';
		}
	}
	return $content;
}

class singleMP3 {

	function parser($content) {
		return preg_replace_callback("/\[mp3(.*)\](.+)\[\/mp3\]/iU", array('singleMP3', 'parser_callback'), $content);
	}


	function parser_callback($param) {
		$path = $param[2];
		$attr = $param[1];

		$attr = strtr($attr,
			array(
				'&#8220;',
				'&#8221;',
			),
			'"'
		);

		$attributes = array();

		preg_match_all("/([a-z]+)\=\"([^\"]+)\"/i", $attr, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			$attributes[$match[1]] = $match[2];
		}

		preg_match_all("/([a-z]+)\=([^ ]+)/i", $attr, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			if (!isset($attributes[$match[1]]))
				$attributes[$match[1]] = $match[2];
		}

		$url = get_bloginfo('wpurl').'/';
		if (substr($url, -1, 1)=='/') $url = substr($url, 0, -1);

		foreach ($attributes as $k=>$v) {
			$v = "so.addVariable(\"{$k}\", \"{$v}\");";
			$attributes[$k] = $v;
		}
		
		if (count($attributes)>0) {
			$attributes = implode(' ', $attributes);
		} else {
			$attributes = '';
		}
		
		$result = '';
		
		static $unique_id = 0;
		$unique_id++;
		
		if ($unique_id==1) {
			$result .= <<<EOD
<script type="text/javascript" src="$url/wp-content/plugins/ccfa/swfobject.js"></script>

EOD;
		}

		$result .= <<<EOD
<span id="singlemp3_{$unique_id}"><a href="http://www.adobe.com/products/flashplayer/" target="_blank">Go get Adobe Flash Player!</a></span>
<script type="text/javascript">
	var so = new SWFObject(
		"$url/wp-content/plugins/ccfa/mp3player.swf",
		"singlemp3_player", "300", "20", "8", "#FFFFFF");
	so.addVariable("file", "$path");
	{$attributes}
	so.write("singlemp3_{$unique_id}");
</script>
EOD;

		return $result;
	}

}

function ccfa_link($links) {  
	$settings_link = '<a href="options-general.php?page=ccfa-settings">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}  

$plugin = plugin_basename(__FILE__);

# Hooks
add_filter("plugin_action_links_$plugin", 'ccfa_link');
add_filter('the_content', 'replace_links');
add_filter('the_content', array('singleMP3', 'parser'));
add_action('admin_menu', 'ccfa_add_pages'); # Add options page
?>