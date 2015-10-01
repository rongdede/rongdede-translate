<?php 
/*
Plugin Name: rongdede translate
Plugin URI: 
Description: auto translate the post to another language.
Version: 0.15.09.27
Author: Rongdede
Author URI: 
Text Domain: rongdede_translate
*/

//语言的变量
global $wpdb;
define('LANG_PARAM', 'lang');
define('RTTABLE', $wpdb->prefix.'rongdede_translate');

class rongdede_const{
	public static $languages = array(
			'en' => 'English',
			'jp' => '日本語',
			'spa' => 'Español',
			'th' => 'ภาษาไทย',
			'ru' => 'Русский',
			'de' => 'Deutsch',
			'nl' => 'Nederlands',
			'kor' => '한국어',
			'fra' => 'Français',
			'ara' => 'العربية',
			'pt' => 'Português',
			'it' => 'Italiano',
			'el' => 'Ελληνικά'
		);



}
class rongdede_translate{
	//翻译的内容
	var $translatecontent;
	//目标语言
	var $dst;
	//百度支持的语言
	var $baidudst = Array("jp","spa","th","ru","de","nl","en","kor","fra","ara","pt","it","el");
	//var $baidudst = Array("en","jp","spa");
	//源语言
	var $src;
	//错误信息
	var $err_msg;
	//使用的翻译引擎
	var $translateengine;


	function __construct() {
			//初始化赋值
			$this->dst = "en";
			$this->src = "zh";
			$this->err_msg = 0;
			$this->translateengine = "baidu";
			//析构函数挂钩action
			//rewrite
			add_filter('rewrite_rules_array', array(&$this, 'update_rewrite_rules'));
			add_filter('query_vars', array(&$this, 'parameter_queryvars'));

			 // comment_moderation_text - future filter TODO
			// full post wrapping (should happen late)
			add_filter('the_content', array($this, 'post_content_wrap'),1,1);
			add_filter('the_excerpt', array(&$this, 'post_content_wrap'),1,1);
			add_filter('the_title', array($this, 'post_wrap'), 1, 2);
			

			//按语言替换连接
			if(get_option('permalink_structure'))
			{
				add_filter('pre_option_home', array(&$this, 'home_url'));
			}
			else
			{
				add_filter('post_link', array($this, 'rongdede_link'));
				add_filter('author_feed_link', array($this, 'rongdede_link'));
				add_filter('author_link', array($this, 'rongdede_link'));
				add_filter('day_link', array($this, 'rongdede_link'));
				add_filter('feed_link', array($this, 'rongdede_link'));
				add_filter('month_link', array($this, 'rongdede_link'));
				add_filter('the_permalink', array($this, 'rongdede_link'));
				add_filter('year_link', array($this, 'rongdede_link'));
				add_filter('tag_link', array($this, 'rongdede_link'));
				add_filter('term_link', array($this, 'rongdede_link'));
				add_filter('comment_reply_link', array($this, 'rongdede_link'));
				add_filter('get_comment_author_link ', array($this, 'rongdede_link'));
				add_filter('get_comment_author_url_link', array($this, 'rongdede_link'));
				add_filter('page_link', array($this, 'rongdede_link'));
				add_filter('post_type_link', array($this, 'rongdede_link'));
			}
			//add_filter('pre_option_home', array(&$this, 'home_url'));


			//发布文章的时候
			add_action('publish_post', array($this,'rongdedetranslate'),10,2);
			//add_action('init', array(&$this, 'on_init'), 0);			
			//激活的时候
			register_activation_hook( __FILE__, Array('rongdede_translate','myplugin_activate'));


		}

	//写错误日志
	function rlog($logs){
		
		$myfile = fopen(plugin_dir_path(__FILE__)."log.txt", "a");
		fwrite(date("Y-m-d H:i:s")."\t".$myfile,$logs."\r\n");
		fclose($myfile);
	}
	

	
	function rongdede_link($link){
		global $wp_query;
//		$this->rlog($link);
		if(isset($wp_query->query_vars[LANG_PARAM]))
		{
//			$urlarr=parse_url($link);
//			if(get_option('permalink_structure'))
//			{
//				$ul=$urlarr["scheme"]."://".$urlarr["host"]."/".$wp_query->query_vars[LANG_PARAM]."".$urlarr["path"];
//				//$ul=home_url("/".$wp_query->query_vars[LANG_PARAM]);
//			}
//			else
//			{
				$ul=$link."&".LANG_PARAM."=".$wp_query->query_vars[LANG_PARAM];
				return $ul;
				
			}
			
			return $link;
//		}
//		$this->rlog($ul);
//		return $link;
	}
	
	//根据连接中是否带有语言来重写网站地址，使之带上语言的符号
	function home_url($homeurl){
		global $wp_query;
		if(isset($wp_query->query_vars[LANG_PARAM])){
			$hl = $homeurl."/".$wp_query->query_vars[LANG_PARAM];
			return $hl;
			//$langurl = home_url("/".$wp_query->query_vars[LANG_PARAM]);
		}
		//$hl = $homeurl."?lang=".$wp_query->query_vars[LANG_PARAM];
		return $homeurl;
	
	}

	//插件激活时执行的内容
	function myplugin_activate(){

		//创建数据库
		global $wpdb;
		$activesql = "select COUNT(*) from information_schema.tables WHERE table_name = '".$wpdb->prefix."rongdede_translate'";
		$createsql = "create table ".$wpdb->prefix."rongdede_translate ( 
		id int NOT NULL AUTO_INCREMENT primary key,
		postid int NOT NULL,
		posttitle varchar(255),
		lang varchar(20) NOT NULL,
		content text
	)";
		//如果数据表不存在就创建数据表
		//$this->rlog("222".$wpdb->query($activesql)."111");
		if(!$wpdb->get_var($activesql)){
		
			$wpdb->query($createsql);
		}

		//重写数据库的rewrite规则
		$GLOBALS['wp_rewrite']->flush_rules();


	}


	public function rongdedetranslate($postid,$post){
		global $wpdb;
		$dst = $this->dst;

		//对提交的内容进行处理
		$posttitle = $post->post_title;
		$postcontent = $post->post_content;
		
		//格式化内容
		
		//按html标签分行
		$postcontent = preg_replace("/(<[^>]*>)/","\r\n$1\r\n",$postcontent);
		
		//去掉空行
		$postcontent = preg_replace("/($\s*$)|(^\s*^)/m", "", $postcontent);
		
		//按行生成一个数组
		$postcontentarr = preg_split("/\n/",$postcontent);

		//for($n=0;$n<count($this->baidudst);$n++)
		foreach(rongdede_const::$languages as $langkey => $langdes)
		{

			$transcontent = "";
			$transtitle = "";
			$dstlang = $langkey;
			//判断使用的翻译引擎,返回标题和每一行翻译的数组
			if($this->translateengine == "baidu")
			{
				$transtitle = $this->baidu_translate($this->src,$dstlang,$post->post_title);
				$transcontentarr = preg_split("/\n/",$this->baidu_translate($this->src,$dstlang,$postcontent));
			}
			
			//$this->rlog($transtitle);

			//翻译后的内容进行重新处理，加回原来的带html代码的
			for($i=0;$i<count($postcontentarr);$i++)
			{
				//如果该数组没匹配到标签则进行替换
				if(!preg_match("/(<[^>]*>)/",$postcontentarr[$i]))
				{
					$transcontent = $transcontent . $transcontentarr[$i] . "\n";
				}
				else
				{
					$transcontent = $transcontent . $postcontentarr[$i] . "\n";
				}
				
			}

			//新增翻译内容的字段赋值
			$ivalue = array(
				"postid" => $post->ID,
				"posttitle" => $transtitle,
				"lang" => $dstlang,
				"content" => $transcontent
			);
			$iformat = array('%d','%s','%s','%s');
			
			//更新翻译内容的字段赋值
			$uvalue = array(
				"posttitle" => $transtitle,
				"content" => $transcontent	
				);
			$uformat = array('%s','%s');

			//判断该post的翻译是否已经存在
			$row_count = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."rongdede_translate where lang='".$dstlang."' and postid=".$post->ID);
			
			//如果数据已经存在则update，如果不存在则insert
			if(!$row_count)
			{
				$wpdb->insert($wpdb->prefix."rongdede_translate",$ivalue,$iformat);
			}
			else
			{
				$wpdb->update($wpdb->prefix."rongdede_translate",$uvalue,array('postid'=>$post->ID,'lang'=>$dstlang),$uformat,array('%d','%s'));
			}
		}
			
	}


	//post函数
	//$url 字符串，post的网址
	//$post 数组，post的表单内容
	private function curl_post($curl, array $cpost = NULL) 
	{ 
		$defaults = array( 
			CURLOPT_POST => 1, 
			CURLOPT_HEADER => 0, 
			CURLOPT_URL => $curl, 
			CURLOPT_FRESH_CONNECT => 1, 
			CURLOPT_RETURNTRANSFER => 1, 
			CURLOPT_FORBID_REUSE => 1, 
			CURLOPT_TIMEOUT => 4, 
			CURLOPT_POSTFIELDS => http_build_query($cpost) 
		); 

		$ch = curl_init(); 
		curl_setopt_array($ch, $defaults); 
		if( ! $result = curl_exec($ch)) 
		{ 
			trigger_error(curl_error($ch)); 
		} 
		curl_close($ch); 
		return $result; 
	} 

	//百度翻译函数
	private function baidu_translate($baidufrom,$baiduto,$baidupost){
		$baiduresult = "";
		//传递的表单数组
		$post_value = array(
			"from" => $baidufrom,
			"to" => $baiduto,
			"client_id" => "sdNwWQu0o9X3hIoyDXBmvjna",
			"q" => $baidupost
		);
		//提交表单
		$baidutranslate = $this->curl_post("http://openapi.baidu.com/public/2.0/bmt/translate",$post_value);
		//百度返回的是json格式的，需要解析下
		$baidujson=json_decode($baidutranslate);
		//判断有没错误信息，如果有错误信息则返回错误内容，如果没有，则返回翻译内容
		if(property_exists($baidujson, 'error_code'))
		{
			$baiduresult = $baidupost;
			$this->rlog($baidujson->error_code."--->".$baidujson->error_msg);
		}
		else
		{
			for ($i= 0;$i< count($baidujson->trans_result); $i++){
				$baiduresult = $baiduresult.$baidujson->trans_result[$i]->dst."\n";
			}
		}

		return $baiduresult;

	}



	//添加rewrite规则
	function update_rewrite_rules($rules) {

        $newRules = array();
        $lang_prefix = "([a-z]{2,3}(\-[a-z]{2,3})?)/";

        $lang_parameter = '&'.LANG_PARAM.'=$matches[1]';

        //catch the root url
        $newRules[$lang_prefix . "?$"] = "index.php?".LANG_PARAM."=\$matches[1]";


        foreach ($rules as $key => $value) {

            $original_key = $key;
            $original_value = $value;

            $key = $lang_prefix . $key;

            //Shift existing matches[i] two step forward as we pushed new elements
            //in the beginning of the expression
            for ($i = 6; $i > 0; $i--) {
                $value = str_replace('[' . $i . ']', '[' . ($i + 2) . ']', $value);
            }

            $value .= $lang_parameter;
			//$this->rlog($key."--->".$value);



            $newRules[$key] = $value;
            $newRules[$original_key] = $original_value;

        }

        return $newRules;
    }

	//添加网址变量
	function parameter_queryvars($vars) {
        $vars[] = LANG_PARAM;
        return $vars;
    }
	
	//显示文章的翻译
    function post_content_wrap($text) {
       	global $wp_query,$wpdb;
		if (!isset($GLOBALS['id']))
            return $text;
		if(isset($wp_query->query_vars[LANG_PARAM])){
			$lang = $wp_query->query_vars[LANG_PARAM];
		}
		else
		{
			$lang = "zh";
		}
		$row = $wpdb->get_row("select content from ".RTTABLE." where postid=".$GLOBALS['id']." and lang='".$lang."'");
		if(!$row){
			return $text;
			//return "2222".$lang.$GLOBALS['id'];
		}
		else
		{
			return $row->content;
			//return var_dump(wp_load_alloptions());
			//return "3333";
		}
		//return "!111111";
		//return get_option('permalink_structure');
    }

	//根据语言显示相应语言的标题
    function post_wrap($text, $id = 0) {
		global $wp_query,$wpdb;
		if(isset($wp_query->query_vars[LANG_PARAM])){
			$lang = $wp_query->query_vars[LANG_PARAM];
		}
		else
		{
			$lang = "zh";
		}
		$id = (is_object($id)) ? $id->ID : $id;
        if (!$id){
			return $text;
		}
		$row = $wpdb->get_row("select posttitle from ".RTTABLE." where postid=".$id." and lang='".$lang."'");
		if(!$row){
			return $text;
		}
		else
		{
			return $row->posttitle;
		}

    }
	




}

$my_rongdede_translate = new rongdede_translate;

require_once("widget.php");

