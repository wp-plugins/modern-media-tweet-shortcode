<?php
/*
Plugin Name: Modern Media Tweet Shortcode
Plugin URI: http://modernmediapartners.com/
Description: Shortcode function for embedded tweets eg: [tweet https://twitter.com/twitterapi/status/133640144317198338]
Author: Chris Carson
Version: 1.0.1
Author URI: http://modernmediapartners.com/
*/
new ModernMediaTweetShortcode;
class ModernMediaTweetShortcode{
	
	const TWITTER_JS_URI = "https://platform.twitter.com/widgets.js";
	const TWITTER_ENDPOINT = "https://api.twitter.com/1/statuses/oembed.json";
	const PLUGIN_NAMESPACE = "ModernMediaTweetShortcode";
	
	function __construct(){
		
		add_action('plugins_loaded', array($this, '_action_plugins_loaded'));
	}
	
	function _action_plugins_loaded(){
		add_shortcode('tweet', array($this, '_shortcode_tweet'));
		add_action('admin_menu', array($this, '_action_admin_menu'));
		$options = $this->get_options();
		if ($options->add_widgets_script){
			add_action('wp_enqueue_scripts', array($this, '_action_wp_enqueue_scripts'));
		}
		if ($options->eliminate_element_clear){
			add_action('wp_print_styles', array($this, "_action_wp_print_styles"));
		}
	}
	
	function _action_wp_print_styles(){
		echo "\n<style type=\"text/css\">\n";
		echo ".ModernMediaTweetShortcode .twp-container{clear:none !important;}\n";
		echo "</style>\n";
	}
	
	function _action_wp_enqueue_scripts(){
		if (is_admin()) return;
		wp_enqueue_script(self::PLUGIN_NAMESPACE, self::TWITTER_JS_URI);
	}
	function _shortcode_tweet($params){
		
		if (! $this->is_valid_install($errors)) return "Invalid installation. Please check the 'Embedded Tweets' admin panel.";
		$uri = $params[0];
		$matches = array();
		if (! preg_match_all("/\d+$/", $uri, $matches )) return "Invalid shortcode - could not find a valid id.";
		$id = $matches[0][0];
		$path = $this->get_cache_directory($error);
		if (! $path){
			return $error;
		}
		$path = $path . DIRECTORY_SEPARATOR . $id;
		if (! file_exists($path)){
			$options = $this->get_options();
			$params = array(
				'id='. $id,
				'omit_script=true',
				'lang=' . urlencode($options->lang),
				'maxwidth=' . urlencode($options->maxwidth),
				'align=' . urlencode($params['align'])
				
			);
			
			$url = self::TWITTER_ENDPOINT . "?" . implode("&", $params);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			file_put_contents($path, $result);
			curl_close($ch);
		}
		$result = json_decode(file_get_contents($path));
		
		return "<div class=\"" . self::PLUGIN_NAMESPACE . "\">" . $result->html . "</div>";
	}
	
	function _action_admin_menu(){
		$t = "Embedded Tweets";
		add_options_page($t, $t, 'administrator', self::PLUGIN_NAMESPACE,
			array($this, 'admin'));
	}
	
	/**
	 * 
	 * @return ModernMediaTweetShortcodeOptions
	 */
	function get_options(){
		$options = get_option(self::PLUGIN_NAMESPACE);
		if (! is_a($options, 'ModernMediaTweetShortcodeOptions')){
			$options = new ModernMediaTweetShortcodeOptions();
		}
		return $options;
	}
	function get_cache_directory(&$error){
		$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . "cache";
		if (! is_dir($path)){
			if (! mkdir($path)){
				$error = "Could not create a cache directory.  Please check your file permissions in the ModernMediaTweetShortcode plugin directory.";
				return false;
			}
		}
		if (! is_writable($path)){
			$error = "The cache directory is not write-able.  Please check your file permissions in the ModernMediaTweetShortcode plugin directory.";
			return false;
		}
		return $path;
	}
	function admin(){
		$errors = array();
		$message = '';
		$options = $this->get_options();
		if (isset($_POST['submitting'])){
			check_admin_referer(self::PLUGIN_NAMESPACE);
			$options->init_from_post($_POST);
			$message = "Options updated!";
			update_option(self::PLUGIN_NAMESPACE, $options);
		} elseif(isset($_POST['clear_cache'])){
			$path = $this->get_cache_directory($error);
			if ($path){
				$dh = dir($path);
				while (false !== ($entry = $dh->read())){
					if (strpos($entry, ".") === 0 ) continue;
					unlink($path . DIRECTORY_SEPARATOR . $entry);
				}
			}
			$message = "Cache cleared.";
		}
		$this->is_valid_install($errors);	
		require dirname(__FILE__) . DIRECTORY_SEPARATOR . "admin.inc.php";
	}
	
	function is_valid_install(&$errors){
		$e = false;
		$cache_dir = $this->get_cache_directory($e);
		if ($error) $errors['cache'] = $error;
		if (! function_exists('json_decode')){
			$errors['json_decode'] = "The <code>json_decode</code> function does not exist.  This function is required.  please upgrade PHP.";
		}
		if (! function_exists('curl_init')){
			$errors['curl'] = "The <code>CURL</code> functions do not exist.  This function is required.  please upgrade PHP.";
		}
		return count($errors) ? false : true;
		
	}
	
}

class ModernMediaTweetShortcodeOptions{
	public $add_widgets_script = false;
	public $lang = 'en';
	public $maxwidth = 400;
	public $eliminate_element_clear = false;
	function init_from_post($p){
		$vars = get_class_vars(get_class($this));
		foreach ($vars as $n=>$i){
			$this->{$n} = trim(stripslashes($p[$n]));
		}
		$this->add_widgets_script = ($this->add_widgets_script == 1) ? true : false;
		$this->eliminate_element_clear = ($this->eliminate_element_clear == 1) ? true : false;
		$langs = require dirname(__FILE__) .  DIRECTORY_SEPARATOR . "langs.inc.php";
		if (! array_key_exists($this->lang, $langs)) $this->lang = 'en';
		if (! is_numeric($this->maxwidth)) $this->maxwidth = 400;
		$this->maxwidth = intval($this->maxwidth);
		if ($this->maxwidth < 250) $this->maxwidth = 250;
		if ($this->maxwidth > 550) $this->maxwidth = 550;
	}
}
