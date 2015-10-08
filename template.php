<?php


Class RongdedeTemplate{


	function __construct() {

		add_action('wp_head', Array($this,'headhreflang'));

	}

	function headhreflang(){
		global $wp_query;
		$current_url = rongdede_const::curPageURL();
		$urlarr=parse_url($current_url);
		if(isset($urlarr["scheme"]))
			$scheme = $urlarr["scheme"];
		if(isset($urlarr["host"]))
			$host = $urlarr["host"];
		if(isset($urlarr["path"]))
			$path = $urlarr["path"];
		$urlquery="";
		if(isset($urlarr["query"]))
			$urlquery = $urlarr["query"];
		//echo $current_url;
		//var_dump($urlarr);
		
		//默认都添加中文的link
		if(get_option('permalink_structure')){
			if(isset($wp_query->query_vars[LANG_PARAM])){
				$outcontent = "<link rel=\"alternate\" hreflang=\"zh\" href=\"".$urlarr["scheme"]."://".$urlarr["host"].str_replace("/".$wp_query->query_vars[LANG_PARAM]."/","",$urlarr["path"])."\" />\r\n";
			}
			else
			{
				$outcontent = "<link rel=\"alternate\" hreflang=\"zh\" href=\"".$current_url."\" />\r\n";
			}
		}
		else
		{
			
			if(isset($wp_query->query_vars[LANG_PARAM])){
				$outcontent = "<link rel=\"alternate\" hreflang=\"zh\" href=\"".$urlarr["scheme"]."://".$urlarr["host"].str_replace(LANG_PARAM."=".$wp_query->query_vars[LANG_PARAM],LANG_PARAM."=",$urlarr["path"])."\" />\r\n";
			}
			else
			{
				$outcontent = "<link rel=\"alternate\" hreflang=\"zh\" href=\"".$current_url."\" />\r\n";
			}		
		}


		//添加各种语言link
		foreach(rongdede_const::$languages as $langkey => $langdes){
		
			if(get_option('permalink_structure')){
				if(isset($wp_query->query_vars[LANG_PARAM])){
					$outcontent = $outcontent . "<link rel=\"alternate\" hreflang=\"".$langkey."\" href=\"".$urlarr["scheme"]."://".$urlarr["host"].str_replace("/".$wp_query->query_vars[LANG_PARAM]."/","/".$langkey."/",$urlarr["path"])."\" />\r\n";
				}
				else
				{
					$outcontent = $outcontent . "<link rel=\"alternate\" hreflang=\"".$langkey."\" href=\"".$urlarr["scheme"]."://".$urlarr["host"]."/".$langkey."".$urlarr["path"]."\" />\r\n";
				}
			}
			else
			{
				if(isset($wp_query->query_vars[LANG_PARAM])){
					$outcontent = $outcontent . "<link rel=\"alternate\" hreflang=\"".$langkey."\" href=\"".str_replace(LANG_PARAM."=".$wp_query->query_vars[LANG_PARAM],LANG_PARAM."=".$langkey,$current_url)."\" />\r\n";
					echo $current_url;
				}
				else
				{
					$outcontent = $outcontent . "<link rel=\"alternate\" hreflang=\"".$langkey."\" href=\"".$urlarr["scheme"]."://".$urlarr["host"]."".$urlarr["path"]."?".$urlquery."&lang=".$langkey."\" />\r\n";
				}

			}
		}
		//$outcontent = $current_url;
		echo $outcontent;

	
	}





}

$my_rongdede_template = new RongdedeTemplate;
?>