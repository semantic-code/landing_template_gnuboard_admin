<?php
$sub_menu = "800100";
require_once './_common.php';

$bo_table = 'landing';

if (!$ld_page) alert('잘못된 접근입니다.');

if($w === 'd'){
    //파일 삭제
    $sql = "SELECT bf_file FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$ld_id}' ";
    $result = sql_query($sql);
    while ($row = mysqli_fetch_array($result)) {
        $file_path = G5_DATA_PATH.'/file/'.$bo_table.'/'.$row['bf_file'];
        if (is_file($file_path)) @unlink($file_path);
    }
    //db 삭제
    $sql = "DELETE FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$ld_id}' ";
    sql_query($sql);

    //랜딩페이지 삭제
    $sql = "DELETE FROM {$g5['landing']} WHERE ld_id = '{$ld_id}' AND ld_page = '{$ld_page}' ";
    sql_query($sql);

    // landing/{ld_page} 폴더 삭제
    rrmdir(G5_PATH.'/landing/'.$ld_page);

    goto_url("./landing_list.php");
}

function rrmdir($dir) {
    if (!is_dir($dir)) return;

    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object === '.' || $object === '..') continue;

        $path = $dir . '/' . $object;

        if (is_dir($path)) {
            rrmdir($path); // 하위폴더도 다시 rrmdir() 호출
        } else {
            @unlink($path); // 파일 삭제
        }
    }
    @rmdir($dir); // 폴더 삭제
}