<?php
$sub_menu = "800100";
require_once './_common.php';

$bo_table = 'landing';

if (!$ld_page) alert('잘못된 접근입니다.');

if($w === 'd'){
    //파일, db 삭제
    delete_attach_file($bo_table, $ld_id);

    //랜딩페이지 삭제
    $sql = "DELETE FROM {$g5['landing']} WHERE ld_id = '{$ld_id}' AND ld_page = '{$ld_page}' ";
    sql_query($sql);

    //수집 데이터 삭제
    $sql = "DELETE FROM {$g5['landing_log']} WHERE ld_page = '{$ld_page}' ";
    sql_query($sql);

    //landing/{ld_page} 폴더 삭제
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
