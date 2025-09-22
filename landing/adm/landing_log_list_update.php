<?php
$path = "";
for ($i =0; $i < 10; $i++) {
    if(file_exists($path.'common.php')) break;
    if(realpath($path) == '/') break;
    $path.= '../';
}
include_once $path.'common.php';

if($mode === 'update_category'){
    $set = array("ldg_cate" => $ldg_cate);
    $sql = "UPDATE {$g5['landing_log']} SET\n".build_query($set)."\nWHERE ldg_id = '{$ldg_id}' ";
    $update = sql_query($sql);

    if($update) {
        $arr_result = array("state" => "success_update_category");
    } else {
        $arr_result = array("state" => "fail_update_category");
    }
} elseif ($mode === 'update_memo'){
    $set = array("ldg_memo" => $ldg_memo);
    $sql = "UPDATE {$g5['landing_log']} SET\n".build_query($set)."\nWHERE ldg_id = '{$ldg_id}' ";
    $update = sql_query($sql);

    if($update) {
        $arr_result = array("state" => "success_update_memo");
    } else {
        $arr_result = array("state" => "fail_update_memo");
    }
} elseif ($mode === 'delete'){
    $sql = "DELETE FROM {$g5['landing_log']} WHERE ldg_id = '{$ldg_id}' ";
    $delete = sql_query($sql);

    if($delete){
        $arr_result = array("state" => "success_delete");
    } else {
        $arr_result = array("state" => "fail_delete");
    }
}

die(json_encode($arr_result));