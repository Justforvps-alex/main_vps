<?php
ignore_user_abort(true);
ini_set('max_execution_time', 86400);
set_time_limit(86400);
error_reporting(0);
$main_url=$_GET['url'];
$number_of_phones=$_GET['n'];
$page_number=1;
$phone_number=0;
print('<form action="check_status.php" class="justify-content-center "><input style="margin-top:20px; width:100%; font-size:20px;" type="submit" name="status" value="Отслеживать состояние загрузки">
</form>');
ob_flush();
flush();
$connect = new mysqli("localhost", "root", "12345", "phones");
$delete_all = $connect->query("DELETE FROM phones_url");
//Создание статусазапущено
$php_status = fopen('phpstatus.txt', 'w+');
fwrite($php_status, "run");
fclose($php_status);
$main_url=str_replace ('nedvizhimost','kvartiry/prodam-ASgBAgICAUSSA8YQ',$main_url);
$main_url=str_replace ('transport','avtomobili',$main_url);
$main_url=str_replace ('uslugi','predlozheniya_uslug/bytovye_uslugi-ASgBAgICAUSYC7CfAQ',$main_url);
require_once 'simple_html_dom.php';
require_once 'classes.php';
require_once 'functions.php';
//$url='https://api.proxyscrape.com/?request=getproxies&proxytype=socks4&timeout=10000&country=all';
//download_proxy($url);
$max_pages=100;
//$number_of_phones=1500; //vvodim post
$url='';

$fp = fopen('/var/www/domains/ovz4.alexdumachev.m2oon.vps.myjino.ru/data.txt', 'a+');
$mistakes = fopen('mistakes.txt', 'a+');
fwrite($mistakes, date('l jS \of F Y h:i:s A'));
//$array0[0]=''; $array1[0]=''; $array2[0]='';
$time = fopen('time.txt', 'a+');
fwrite($time, date('l jS \of F Y h:i:s A'));
while($phone_number<=$number_of_phones)
{
if($page_number>100)
{
    $page_number=1;
}
$done_pages_write = fopen('/var/www/domains/ovz4.alexdumachev.m2oon.vps.myjino.ru/done_pages.txt', 'w');
fwrite($done_pages_write, $page_number);
fclose($done_pages_write);
    //Проверка сигналов
    $signal=htmlentities(file_get_contents("signal.txt"));
    if($signal=="stop")
    {
        $php_status = fopen('phpstatus.txt', 'w+');
        fwrite($php_status, "done");
        fclose($php_status);
        exit;
    }
	$url=$main_url.'?p='.$page_number;
	fwrite($mistakes, $url);
	$time_sleep=rand(7,8);
	$html=Curl_avito($url,$time_sleep,$mistakes);
	//fwrite($mistakes, $html);
	foreach($html->find('div.index-root-2c0gs') as $html_div)
	{
	    $html=$html_div;
	}
	//echo "<br>nulevoy cicl<br>";
	fwrite($mistakes, 'nulevoy cicl');
	//echo $html;
	$write_id_file = fopen('id_array.txt', 'w+');
	foreach($html->find('div.snippet-horizontal') as $href_div)
	{
		$id=$href_div->attr['data-item-id'];
		if($id%2!=0)
		{
			$array0[$id].=$href_div->attr['data-pkey'];
			if($array0[$id]!='')
			{
			    $contact_number=0;
		    	foreach($href_div->find('a.snippet-link') as $href_to_check)
	    	    {$contact_number=$contact_number+1;  }	
		         if($contact_number==1)
		        {
                   fwrite($write_id_file, $id.",");
		    	    fwrite($mistakes, $id);
		        }
			}
		}
	}
//unset($html);
	fclose($write_id_file);
	//Собираем айди из файла, разбиваем на части
    $id_arrayall=htmlentities(file_get_contents("id_array.txt"));
    $id_array=preg_split("/[\s,]+/",$id_arrayall);
	$max_id=count($id_array);
    fwrite($mistakes, 'perviy cicl');
	$time_sleep=rand(7,8);
	$html=Curl_avito($url,$time_sleep,$mistakes);
	foreach($html->find('div.index-root-2c0gs') as $html_div)
	{
	    $html=$html_div;
	}
	//echo $html;
	
    //Проверка сигналов
    $signal=htmlentities(file_get_contents("signal.txt"));
    if($signal=="stop")
    {
        $php_status = fopen('phpstatus.txt', 'w+');
        fwrite($php_status, "done");
        fclose($php_status);
        exit;
    }
  
	for($id_numer=0;$id_numer<$max_id; $id_numer++)
	{
		$id=$id_array[$id_numer];
		foreach($html->find("div[data-item-id=$id]") as $href_div)
		{
		        if(isset($href_div)){$array1[$id].=$href_div->attr['data-pkey']; fwrite($mistakes, $id.") ".$array1[$id]."<br>");}
		}
	}
//unset($html);
	//echo "<br>vtoroy cicl<br>";
	fwrite($mistakes, 'vtoroy cicl');
	$checked_id=1;
	$time_sleep=rand(7,8);
	$html=Curl_avito($url,$time_sleep,$mistakes);
	foreach($html->find('div.index-root-2c0gs') as $html_div)
	{
	    $html=$html_div;
	}
	//echo $html;
	for($id_numer=0;$id_numer<$max_id; $id_numer++)
	{
	 //Проверка сигналов
    $signal=htmlentities(file_get_contents("signal.txt"));
    if($signal=="stop")
    {
    $php_status = fopen('phpstatus.txt', 'w+');
    fwrite($php_status, "done");
    fclose($php_status);
    exit;
    }
		$id=$id_array[$id_numer];
		foreach($html->find("div[data-item-id=$id]") as $href_div)
		{
		if(isset($href_div))
		{$array2[$id].=$href_div->attr['data-pkey']; fwrite($mistakes, $id.") ".$array2[$id]."<br>");}
		if($array0[$id]!='' && $array1[$id]!='' && $array2[$id]!='')
		 {           
            		$phone_item_only0=$array0[$id];
            		$phone_item_only1=$array1[$id];
            		$phone_item_only2=$array2[$id];
              		$url=find_phone_url($id, $phone_item_only0, $phone_item_only1, $phone_item_only2);
                    $sel = $connect->query("SELECT id FROM phones_url WHERE item_url='$url'");
	            	$numer = $sel->num_rows; //считаем, сколько с таким же логином
	            	if($numer == 0)
	            	{
              		    $add = $connect->query("INSERT INTO phones_url (id, item_url) VALUES (NULL,'$url')");
              		}
              		$sell = $connect->query("SELECT COUNT(id) FROM phones");
              		$phone_number++;
              		if($phone_number>=$number_of_phones) { $phone_number_done = $sell->num_rows; }
              		if($phone_number_done>=$number_of_phones) { $php_status = fopen('phpstatus.txt', 'w+'); fwrite($php_status, "done"); fclose($php_status); exit; }
              		else { $phone_number=$phone_number_done; }
		 }
		}
	}
unset($html);
$page_number=$page_number+1;
}
?>
