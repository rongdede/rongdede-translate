<?php 
//提交表单的值
//from：原语言
//to：要翻译成的语言
//q：需要翻译的内容
$post_value = array(

"from" => "zh",
"to" => "en",
"client_id" => "sdNwWQu0o9X3hIoyDXBmvjna",
"q" => "<article class='article-content'><p>进<a title='digitalocean' class='aalmanual' target='_blank'  href='http://www.91yun.org/goto/5/digitalocean'>digitalocean</a>的后台发现后台变成这样的鬼样了- &#8211;</p><p><img class='alignnone size-full wp-image-33' src='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927151525.png?resize=618%2C347' alt='20150927151525' data-recalc-dims='1' /></p><p>用chrome的开发者工具看了下，发现原来是他的一个css的CDN地址被屏蔽了。。。你懂的。</p><p><a href='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927151647.png'><img class='alignnone size-large wp-image-34' src='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927151647.png?resize=1024%2C290' alt='20150927151647' data-recalc-dims='1' /></a></p><p>只要在在hosts加个ip就可以了。</p><p>windows的host地址在：</p><pre>C:WindowsSystem32driversetchosts</pre><p><a href='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927154222.png'><img class='alignnone size-full wp-image-35' src='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927154222.png?resize=752%2C281' alt='20150927154222' data-recalc-dims='1' /></a></p><p>用记事本打开这个文件，加一行</p><pre>199.27.79.249 cloud-cdn-<a title='digitalocean' class='aalmanual' target='_blank'  href='http://www.91yun.org/goto/5/digitalocean'>digitalocean</a>-com.global.ssl.fastly.net</pre><p><img class='alignnone size-large wp-image-36' src='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927154314.png?resize=591%2C457' alt='20150927154314' data-recalc-dims='1' /></p><p>&nbsp;</p><p>转载请注明：<a href='http://www.91yun.org'>就要云</a> &raquo; <a href='http://www.91yun.org/archives/14'>digitalocean（DO）后台CSS丢失的问题解决办法</a></p><div class='sharedaddy sd-sharing-enabled'><div class='robots-nocontent sd-block sd-social sd-social-icon sd-sharing'><h3 class='sd-title'>共享此文章：</h3><div class='sd-content'><ul><li class='share-twitter'><a rel='nofollow' data-shared='sharing-twitter-14' class='share-twitter sd-button share-icon no-text' href='http://www.91yun.org/archives/14?share=twitter' target='_blank' title='点击以在 Twitter 上共享'><span></span><span class='sharing-screen-reader-text'>点击以在 Twitter 上共享（在新窗口中打开）</span></a></li><li class='share-facebook'><a rel='nofollow' data-shared='sharing-facebook-14' class='share-facebook sd-button share-icon no-text' href='http://www.91yun.org/archives/14?share=facebook' target='_blank' title='在 Facebook 上共享'><span></span><span class='sharing-screen-reader-text'>在 Facebook 上共享（在新窗口中打开）</span></a></li><li class='share-google-plus-1'><a rel='nofollow' data-shared='sharing-google-14' class='share-google-plus-1 sd-button share-icon no-text' href='http://www.91yun.org/archives/14?share=google-plus-1' target='_blank' title='点击以在 Google+ 上共享'><span></span><span class='sharing-screen-reader-text'>点击以在 Google+ 上共享（在新窗口中打开）</span></a></li><li class='share-end'></li></ul></div></div></div><div id='jp-relatedposts' class='jp-relatedposts' >    <h3 class='jp-relatedposts-headline'><em>相关</em></h3></div>        </article>"

);

//translate_url 翻译引擎
$translate_url = "http://openapi.baidu.com/public/2.0/bmt/translate";
$err = "";
$errmsg = "";


//post函数
//$url 字符串，post的网址
//$post 数组，post的表单内容
function curl_post($url, array $post = NULL) 
{ 
    $defaults = array( 
        CURLOPT_POST => 1, 
        CURLOPT_HEADER => 0, 
        CURLOPT_URL => $url, 
        CURLOPT_FRESH_CONNECT => 1, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_FORBID_REUSE => 1, 
        CURLOPT_TIMEOUT => 4, 
        CURLOPT_POSTFIELDS => http_build_query($post) 
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



//取得翻译内容
$translatec = curl_post($translate_url,$post_value);

//内容进行格式化，百度返回的是json格式
$jd=json_decode($translatec);

//判断是否有错误信息
//if(property_exists($jd, 'error_code')){
//	
//	$err = $jd->error_code;
//}
//
//if(property_exists($jd, 'error_msg')){
//	
//	$errmsg = $jd->error_msg;
//}


$dst_result = $jd->trans_result[0]->dst;
echo $dst_result;


?>bbbbbb