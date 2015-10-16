<?php


class RongdedeSitemap{

	function __construct() {
	
			//加后台菜单
			add_action('admin_menu', array($this,'rongdedetranslate_menu'));
	}

	//后台菜单
	function rongdedetranslate_menu(){
		
		add_menu_page( '多国翻译', 'RongdedeTrans', 'manage_options', 'rongdedemenu', '', '', 90 );
		add_submenu_page( 'rongdedemenu', '选项', 'options', 'manage_options', 'rongdedemenu', array($this,'rongdede_updatetrans') );
		add_submenu_page( 'rongdedemenu', '生成sitemap', 'sitemap', 'manage_options', 'sitemap', array($this,'rongdede_sitemap') );
		add_submenu_page( 'rongdedemenu', '更新翻译', 'createtrans', 'manage_options', 'rongdedemenu3', array($this,'rongdede_updatetrans') );

	
	}

	//菜单：生成sitemap
	function rongdede_sitemap(){
		if (isset($_REQUEST["action"]) && htmlspecialchars($_REQUEST["action"]) == "sitemap"){

			//判断是哪个搜索引擎用的
			$sitemaptype = "google";
			if(isset($_REQUEST["sitemaptype"]))
			{
				$sitemaptype = htmlspecialchars($_REQUEST["sitemaptype"]);
			}
			$sitemapfilename = "sitemap.xml";
			
			if(!isset($sitemapfilename) || $sitemapfilename == '')
			{
				$sitemapfilename = "sitemap.xml";
			}

			//判断sitemap.xml是否存在
			$sitepath = dirname(dirname(dirname(dirname(__FILE__))));
			$sitemapfile = $sitepath.DIRECTORY_SEPARATOR.$sitemapfilename;
			if(file_exists($sitemapfile))
			{
				//echo "111111";
				rename($sitemapfile,$sitepath.DIRECTORY_SEPARATOR."sitemap".time().".xml");

			}
			

			if($sitemaptype == "google"){
				
				$sitemapxml=$this->googlesitemap();
			}
			else
			{
				$sitemapxml=$this->googlesitemap();
			}

			//写入sitemap;
			$myfile = fopen($sitemapfile, "w");
			fwrite($myfile,$sitemapxml);
			fclose($myfile);
			
		}
		else
		{
			$html = "<form action='admin.php?page=sitemap' method='post'><input type=\"hidden\" name=\"action\" value=\"sitemap\">
<button type=\"submit\">Create Sitemap!</button></form>";
			echo $html;
		}
	}

	function rongdede_updatetrans(){
	
		$html = "test";
		echo $html;
	}

	//google的sitemap生成
	function googlesitemap(){
		
		global $wp_query;
		$sitemapxml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"
  xmlns:xhtml=\"http://www.w3.org/1999/xhtml\">\r\n";
		//主页
		$sitemapxml = $sitemapxml."\t<url>\r\n";
		$sitemapxml = $sitemapxml."\t\t<loc>".home_url()."</loc>\r\n";
		foreach(rongdede_const::$languages as $langkey => $langdes){
			$sitemapxml = $sitemapxml."\t\t<xhtml:link rel=\"alternate\" hreflang=\"".$langkey."\" href=\"".$this->translink(home_url(),$langkey)."\"/>\r\n";
		
		}
		$sitemapxml = $sitemapxml."\t\t<lastmod>".date("Y-m-d")."</lastmod>
\t\t<priority>1.00</priority>
\t\t<changefreq>daily</changefreq>\r\n";
		$sitemapxml = $sitemapxml."\t</url>\r\n";
		
		//文章
		query_posts("");
		if ( have_posts() ){

			 while ( have_posts() ){
			 
				the_post();
				$sitemapxml = $sitemapxml."\t<url>\r\n";
				$sitemapxml = $sitemapxml."\t\t<loc>".get_permalink()."</loc>\r\n";
				foreach(rongdede_const::$languages as $langkey => $langdes){
					$sitemapxml = $sitemapxml."\t\t<xhtml:link rel=\"alternate\" hreflang=\"".$langkey."\" href=\"".$this->translink(get_permalink(),$langkey)."\"/>\r\n";
				
				}
				$sitemapxml = $sitemapxml."\t\t<lastmod>".date("Y-m-d")."</lastmod>
\t\t<priority>1.00</priority>
\t\t<changefreq>weekly</changefreq>\r\n";
				$sitemapxml = $sitemapxml."\t</url>\r\n";
			 
			 }

		
		}

		//目录
		$categories = get_categories("");

			 foreach($categories as $category){
			 				$sitemapxml = $sitemapxml."\t<url>\r\n";
				$sitemapxml = $sitemapxml."\t\t<loc>".get_category_link( $category->term_id )."</loc>\r\n";
				foreach(rongdede_const::$languages as $langkey => $langdes){
					$sitemapxml = $sitemapxml."\t\t<xhtml:link rel=\"alternate\" hreflang=\"".$langkey."\" href=\"".$this->translink(get_category_link( $category->term_id ),$langkey)."\"/>\r\n";
				
				}
				$sitemapxml = $sitemapxml."\t\t<lastmod>".date("Y-m-d")."</lastmod>
\t\t<priority>0.97</priority>
\t\t<changefreq>weekly</changefreq>\r\n";
				$sitemapxml = $sitemapxml."\t</url>\r\n";
			 
			 }

		//tag
		$tags = get_tags();

			 foreach( $tags as $tag ){
			 				$sitemapxml = $sitemapxml."\t<url>\r\n";
				$sitemapxml = $sitemapxml."\t\t<loc>".get_tag_link( $tag->term_id )."</loc>\r\n";
				foreach(rongdede_const::$languages as $langkey => $langdes){
					$sitemapxml = $sitemapxml."\t\t<xhtml:link rel=\"alternate\" hreflang=\"".$langkey."\" href=\"".$this->translink(get_tag_link( $tag->term_id ),$langkey)."\"/>\r\n";
				
				}
				$sitemapxml = $sitemapxml."\t\t<lastmod>".date("Y-m-d")."</lastmod>
\t\t<priority>0.97</priority>
\t\t<changefreq>weekly</changefreq>\r\n";
				$sitemapxml = $sitemapxml."\t</url>\r\n";
			 
			 }		


		$sitemapxml = $sitemapxml."</urlset>";
		return $sitemapxml;

		


	}

	
	function translink($url,$lang){
		
			$urlarr = parse_url($url);
			$scheme =isset($urlarr["scheme"])?$urlarr["scheme"]:"";
			$host =isset($urlarr["host"])?$urlarr["host"]:"";
			$path =isset($urlarr["path"])?$urlarr["path"]:"/";

			if(get_option('permalink_structure'))
			{
				$urlreturn = $scheme."://".$host."/".$lang.$path;
			}
			else
			{	
				$urlreturn = $url."&".LANG_PARAM."=".$lang;
			
			}
			return $urlreturn;
	
	}


}

$myrongdedesitemap = new RongdedeSitemap;

?>