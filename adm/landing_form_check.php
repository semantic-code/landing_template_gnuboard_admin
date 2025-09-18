<?php
include_once('./_common.php');

$sql = "SELECT COUNT(*) cnt FROM {$g5['landing']} WHERE (1) AND ld_page = '{$ld_page}' ";
$row = sql_fetch($sql);

if($row['cnt'] > 0){
    $arr_result = array('state' => 'fail', 'msg' => '이미 사용 중인 아이디 입니다.');
}else{
    $arr_result = array('state' => 'success', 'msg' => '사용 가능한 아이디 입니다.');
}

die(json_encode($arr_result));