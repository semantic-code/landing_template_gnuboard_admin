<?php
$sub_menu = "800100";
require_once './_common.php';

/*
echo "<pre>";
print_r($_GET);
echo "</pre>";
exit;
*/
if (!$ld_page) alert('잘못된 접근입니다.');

if($w === 'd'){
    //파일목록 불러오기
    $sql = "SELECT ld_files FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ";
    $row = sql_fetch($sql);
    if ($row && !empty($row['ld_files'])) {
        $files = explode('|', $row['ld_files']);

        foreach ($files as $fname) {
            // 보안: 경로 검증 (2509/파일명.jpg 형태만 허용)
            if (preg_match('/^[0-9]{4}\/[a-zA-Z0-9._-]+$/', $fname)) {
                $file_path = G5_PATH.'/data/editor/'.$fname;
                if (is_file($file_path)) @unlink($file_path);
            }
        }
    }

    // landing/{ld_page} 폴더 삭제 (재귀적으로)
    $landing_dir = G5_PATH.'/landing/'.$ld_page;
    rrmdir($landing_dir);

    $delete = sql_query("DELETE FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ");
    if($delete){
        goto_url("./landing_list.php");
    }else{
        alert("삭제에 실패했습니다.");
    }
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