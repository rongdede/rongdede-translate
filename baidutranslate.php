<?php 
//�ύ����ֵ
//from��ԭ����
//to��Ҫ����ɵ�����
//q����Ҫ���������
$post_value = array(

"from" => "zh",
"to" => "en",
"client_id" => "sdNwWQu0o9X3hIoyDXBmvjna",
"q" => "<article class='article-content'><p>��<a title='digitalocean' class='aalmanual' target='_blank'  href='http://www.91yun.org/goto/5/digitalocean'>digitalocean</a>�ĺ�̨���ֺ�̨��������Ĺ�����- &#8211;</p><p><img class='alignnone size-full wp-image-33' src='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927151525.png?resize=618%2C347' alt='20150927151525' data-recalc-dims='1' /></p><p>��chrome�Ŀ����߹��߿����£�����ԭ��������һ��css��CDN��ַ�������ˡ������㶮�ġ�</p><p><a href='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927151647.png'><img class='alignnone size-large wp-image-34' src='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927151647.png?resize=1024%2C290' alt='20150927151647' data-recalc-dims='1' /></a></p><p>ֻҪ����hosts�Ӹ�ip�Ϳ����ˡ�</p><p>windows��host��ַ�ڣ�</p><pre>C:WindowsSystem32driversetchosts</pre><p><a href='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927154222.png'><img class='alignnone size-full wp-image-35' src='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927154222.png?resize=752%2C281' alt='20150927154222' data-recalc-dims='1' /></a></p><p>�ü��±�������ļ�����һ��</p><pre>199.27.79.249 cloud-cdn-<a title='digitalocean' class='aalmanual' target='_blank'  href='http://www.91yun.org/goto/5/digitalocean'>digitalocean</a>-com.global.ssl.fastly.net</pre><p><img class='alignnone size-large wp-image-36' src='http://i2.wp.com/www.91yun.org/wp-content/uploads/2015/09/20150927154314.png?resize=591%2C457' alt='20150927154314' data-recalc-dims='1' /></p><p>&nbsp;</p><p>ת����ע����<a href='http://www.91yun.org'>��Ҫ��</a> &raquo; <a href='http://www.91yun.org/archives/14'>digitalocean��DO����̨CSS��ʧ���������취</a></p><div class='sharedaddy sd-sharing-enabled'><div class='robots-nocontent sd-block sd-social sd-social-icon sd-sharing'><h3 class='sd-title'>��������£�</h3><div class='sd-content'><ul><li class='share-twitter'><a rel='nofollow' data-shared='sharing-twitter-14' class='share-twitter sd-button share-icon no-text' href='http://www.91yun.org/archives/14?share=twitter' target='_blank' title='������� Twitter �Ϲ���'><span></span><span class='sharing-screen-reader-text'>������� Twitter �Ϲ������´����д򿪣�</span></a></li><li class='share-facebook'><a rel='nofollow' data-shared='sharing-facebook-14' class='share-facebook sd-button share-icon no-text' href='http://www.91yun.org/archives/14?share=facebook' target='_blank' title='�� Facebook �Ϲ���'><span></span><span class='sharing-screen-reader-text'>�� Facebook �Ϲ������´����д򿪣�</span></a></li><li class='share-google-plus-1'><a rel='nofollow' data-shared='sharing-google-14' class='share-google-plus-1 sd-button share-icon no-text' href='http://www.91yun.org/archives/14?share=google-plus-1' target='_blank' title='������� Google+ �Ϲ���'><span></span><span class='sharing-screen-reader-text'>������� Google+ �Ϲ������´����д򿪣�</span></a></li><li class='share-end'></li></ul></div></div></div><div id='jp-relatedposts' class='jp-relatedposts' >    <h3 class='jp-relatedposts-headline'><em>���</em></h3></div>        </article>"

);

//translate_url ��������
$translate_url = "http://openapi.baidu.com/public/2.0/bmt/translate";
$err = "";
$errmsg = "";


//post����
//$url �ַ�����post����ַ
//$post ���飬post�ı�����
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



//ȡ�÷�������
$translatec = curl_post($translate_url,$post_value);

//���ݽ��и�ʽ�����ٶȷ��ص���json��ʽ
$jd=json_decode($translatec);

//�ж��Ƿ��д�����Ϣ
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