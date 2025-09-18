<?php
include_once('./_common.php');

if($ld_page === 'basic'){
    $arr_result = array('state' => 'blocked', 'msg' => '해당 아이디는 사용할 수 없습니다.');
    die(json_encode($arr_result));
}

$sql = "SELECT COUNT(*) cnt FROM {$g5['landing']} WHERE (1) AND ld_page <> 'basic' AND ld_page = '{$ld_page}' ";
$row = sql_fetch($sql);

if($row['cnt'] > 0){
    $arr_result = array('state' => 'fail', 'msg' => '이미 사용 중인 아이디 입니다.');
}else{
    $arr_result = array('state' => 'success', 'msg' => '사용 가능한 아이디 입니다.');
}


die(json_encode($arr_result));

