#!/usr/bin/php
<?php
include '../config.core.php';
include MODX_CORE_PATH.'config/config.inc.php';

$templates_path = MODX_ASSETS_PATH.'templates/';
$chanks_path = $templates_path.'chunks/';

if( !is_dir(MODX_ASSETS_PATH) ){
    echo "Создайте директорию ".MODX_ASSETS_PATH."\n";
    exit();
}

if( !is_dir($templates_path) ){
    mkdir($templates_path, 0640);
}

if( !is_dir($chanks_path) ){
    mkdir($chanks_path, 0640);
}

$link = mysqli_connect(
    $database_server,
    $database_user,
    $database_password,
    $dbase);

if (mysqli_connect_errno()) {
	printf("Попытка соединения не удалась: %s\n", mysqli_connect_error());
    exit();
}

if($result = mysqli_query($link, 'SELECT category,name,snippet FROM `mdx-site_htmlsnippets` ORDER BY category')){
    while($row = mysqli_fetch_assoc($result)){
	if($row['category'] > 0){
	    if($rowcat = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,parent,category FROM `mdx-categories` where id='".$row['category']."'"))){
		if($rowcat['parent'] > 0){
		    if( $parent = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,parent,category FROM `mdx-categories` where id='".$rowcat['parent']."'"))){
		    }
		    $parent_dir = $chanks_path.$parent['category'].'/';
		    echo $parent_dir.$rowcat['category']."/".$row['name']."\n";
		    if(!is_dir($parent_dir)) mkdir($parent_dir, 0640);
		    if(!is_dir($parent_dir.$rowcat['category'])) mkdir($parent_dir.$rowcat['category'], 0640);
		    $tpl = fopen($parent_dir.$rowcat['category']."/".$row['name'], 'w');
		    fwrite($tpl, $row['snippet']);
    		    fclose($tpl);
		}
	    }
	    echo $chanks_path.$rowcat['category']."/".$row['name']."\n";
	    if(!is_dir($chanks_path.$rowcat['category'])) mkdir($chanks_path.$rowcat['category'], 0640);
	    $tpl = fopen($chanks_path.$rowcat['category']."/".$row['name'], 'w');
	    fwrite($tpl, $row['snippet']);
	    fclose($tpl);
	}
	else {
	    echo $chanks_path.$row['name']."\n";
	    $tpl = fopen($chanks_path.$row['name'], 'w');
	    fwrite($tpl, $row['snippet']);
	    fclose($tpl);
	}
    }
}
else {
    echo "Query error\n";
}
mysqli_free_result($result);
mysqli_close($link);
?>
