<?php
function Curl_avito($url,$time_sleep,$mistakes)
{
$useragent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/'.rand(60,72).'.0.'.rand(1000,9999).'.121 Safari/537.36';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
$page = curl_exec($ch);
$html=str_get_html($page);
curl_close($ch);
sleep($time_sleep);
$html=check_html($html,$url,$mistakes);
return $html;
}
function check_html($html,$url,$mistakes)
{
    $string_proxy=$GLOBALS['number_proxy'];
    if(!isset($string_proxy) or $string_proxy==''){$GLOBALS['number_proxy']=1; $GLOBALS['proxy_type']='CURLPROXY_SOCKS4';}
	$check_html=$html;
	//echo "<br>".$url."<br>";
	$check_1=strpos($check_html,'Объявления');
	$check_2=strpos($check_html,'user_unauth');
	$check_3=strpos($check_html,'image64');
	if($check_html!='')
	{
	if($check_1!==false or $check_2!==false or $check_3!==false) {fwrite($mistakes, "<br>Vse norm<br>"); $check_proxy_check=1;}
	else {fwrite($mistakes, "<br>Vse ploho, zabanen<br>");}
	}
	else {fwrite($mistakes, "<br>Vse ploho, dead proxy<br>");$check_proxy_check=0;}
	while($check_html=='' or $check_proxy_check==0)
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
        
	$check_1=strpos($check_html,'Объявления');
	$check_2=strpos($check_html,'user_unauth');
	$check_3=strpos($check_html,'image64');
	if($check_html!='')
	{
    	if($check_1!==false or $check_2!==false or $check_3!==false) {fwrite($mistakes,"<br>Vse norm<br>"); $check_proxy_check=1; break;}
    	else {fwrite($mistakes,"<br>Vse ploho, zabanen<br>");}
	}
	else {fwrite($mistakes,"<br>Vse ploho, dead proxy<br>");}
	$useragent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/'.rand(60,72).'.0.'.rand(1000,9999).'.121 Safari/537.36';
	$show_info = file('socks5_proxies.txt');
	$proxy=$show_info[$string_proxy];
	if($proxy='' or $proxy=NULL)
	{
	$url_proxy='https://api.proxyscrape.com/?request=getproxies&proxytype=socks5&timeout=10000&country=all';
    download_proxy($url_proxy);
    $GLOBALS['proxy_type']='CURLPROXY_SOCKS5';
    $GLOBALS['number_proxy']=1;
    $show_info = file('socks5_proxies.txt');
	$proxy=$show_info[1];
	}
	$proxy_mistakes = fopen('peoxy.txt', 'a+');
	fwrite($proxy_mistakes, $string_proxy." next");
	fclose($proxy_mistakes);
	$statusfp = fopen('status.txt', 'w+');
    fwrite($statusfp, "IP забанен, подключаемся к прокси");
    fclose($statusfp);
	$ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    curl_setopt($ch, CURLOPT_PROXY, '34.96.248.174:443');
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'vps:12345');
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
	$page = curl_exec($ch);
	curl_close($ch);
	$check_html=str_get_html($page);
	fwrite($mistakes, "<br><br>Плохой хтмл<br>");
	$string_proxy++;
    }
	//echo "<br>End of while<br>";
	$GLOBALS['number_proxy']=$string_proxy;
	sleep(7);
	return $check_html;
}
function find_phone_url($id, $phone_item_only0, $phone_item_only1, $phone_item_only2)
{
	$id_only=$id;
	$array_keys0 = str_split($phone_item_only0); 
	$array_keys1 = str_split($phone_item_only1);
	$array_keys2 = str_split($phone_item_only2);
	//function check_code($array0,$array1,$array2)
	$k=0; //Номер кода
	$code_key='';//Буква разделитель
	$letters_in_key=count($array_keys0);
	for($a=0;$a<$letters_in_key;$a=$a+3)
	{
		//Если все 3 совпадают
		if($array_keys0[$a]==$array_keys1[$a] and $array_keys0[$a]==$array_keys2[$a])
		{ continue; }
		//Проверка когда 2 отличаются
		elseif($array_keys0[$a]!=$array_keys1[$a] and $array_keys0[$a]!=$array_keys2[$a] and $array_keys1[$a]!=$array_keys2[$a])
		{
			if($array_keys0[$a+1]==$array_keys1[$a+1]) { $k=0; $code_key=$array_keys0[$a]; break;}
			elseif($array_keys0[$a+1]==$array_keys2[$a+1]) { $k=0; $code_key=$array_keys0[$a]; break;} 
			elseif($array_keys1[$a+1]==$array_keys2[$a+1]) { $k=1; $code_key=$array_keys1[$a]; break;}	
		}
		//Проверка когда 1 отличается
		elseif($array_keys0[$a]==$array_keys1[$a] and $array_keys0[$a]!=$array_keys2[$a])
		{
			$code_key=$array2[$a];
			if($array_keys0[$a]==$array_keys2[$a+1]) { $k=2; break;}	
		}
		elseif($array_keys0[$a]==$array_keys2[$a] and $array_keys0[$a]!=$array_keys1[$a])
		{
			$code_key=$array_keys1[$a];
			if($array_keys0[$a]==$array_keys1[$a+1]) { $k=1; break;}	
		}
		elseif($array_keys1[$a]==$array_keys2[$a] and $array_keys1[$a]!=$array_keys0[$a])
		{
			$code_key=$array_keys0[$a];
			if($array_keys1[$a]==$array_keys0[$a+1]) { $k=0; break;}	
		}
	}
	//Находим количество букв и вгоняем линию
	if($k==0) { $crypted_line_array=$array_keys0; }
	elseif($k==1) { $crypted_line_array=$array_keys1; }
	elseif($k==2) { $crypted_line_array=$array_keys2; }
	$numer=count($crypted_line_array);
	$findphone = fopen('phoneurls.txt', 'wb');
    fwrite($findphone, $k."  ".$numer."  ".$crypted_line_array." codekey: ".$code_key);
    fclose($findphone);
	$pkey=''; //Код
	//$i=0;
	for($i=0;$i<$numer;$i=$i+3)
	{
		if($crypted_line_array[$i]==$code_key) {$i++;}
		$pkey.=$crypted_line_array[$i];
	}
	$phoneUrl="https://www.avito.ru/items/phone/".$id_only."?pkey=".$pkey."&vsrc=r";
	//echo "<br>Вывод из поиска".$phoneUrl."<br>";
	return $phoneUrl;
}
function download_proxy($url)
{
	$fp = fopen('socks5_proxies.txt', 'wb'); // создаём и открываем файл для записи
	$ch = curl_init($url); // $url содержит прямую ссылку на видео
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FILE, $fp); // записать вывод в файл
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}
?>
