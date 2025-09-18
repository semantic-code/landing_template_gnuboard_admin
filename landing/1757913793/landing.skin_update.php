<?php
$path = "";
for($i = 0; $i < 10; $i++){
    if(file_exists($path .'common.php')) break;
    if(realpath($path) ==  '/') break;
    $path.= '../';
}
include_once $path.'common.php';

$set = array(
    "ld_page"      => $ld_page,
    "ldg_field_1"  => $ldg_field_1,
    "ldg_field_2"  => $ldg_field_2,
    "ldg_field_3"  => $ldg_field_3,
    "ldg_field_4"  => $ldg_field_4,
    "ldg_field_5"  => $ldg_field_5,
    "ldg_cate"     => $ldg_cate,
    "ldg_datetime" => date("Y-m-d H:i:s"),
);

$sql = "INSERT INTO {$g5['landing_log']} SET\n".build_query($set);
$insert = sql_query($sql);

if($insert) alert("내용이 저장되었습니다.", G5_URL."/landing/?ld_page={$ld_page}");
