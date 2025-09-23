<?php
include_once('./_common.php');

$ld_content = stripslashes($ld_content);

//에디터 이미지 파일이름 저장
preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $ld_content, $matches);

/*
echo '<pre>';
print_r($matches[1]);
echo '<pre>';
exit;
*/

$editor_files = array();
foreach ($matches[1] as $src) {
    // /data/editor/ 뒤에 오는 경로만 추출
    if (preg_match('#/data/editor/(.+)$#', $src, $m)) {
        $real_path = $m[1]; // 예: "2509/abc.jpg"
        $editor_files[] = $real_path;
    }
}
$editor_files = array_unique($editor_files); // 중복 제거
$ld_files = implode('|', $editor_files);

if($w === 'u'){
    
    if(!$ld_page) alert('잘못된 접근입니다.');

    //기존 파일 목록 불러오기
    $sql = "SELECT ld_files FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ";
    $row = sql_fetch($sql);
    $old_files = !empty($row['ld_files']) ? explode('|', $row['ld_files']) : array();

    //기존에는 있었는데, 새 본문에는 없는 파일 삭제
    $delete_files = array_diff($old_files, $editor_files);
    foreach ($delete_files as $fname){
        $file_path = G5_PATH.'/data/editor/'.$fname;
        if(is_file($file_path)) @unlink($file_path);
    }

    $set = array(
        "ld_page"          => $ld_page,
        "ld_subject"       => $ld_subject,
        "ld_content"       => $ld_content,
        "ld_fields"        => $ld_fields,
        "ld_category_list" => $ld_category_list,
        "ld_use_category"  => $ld_use_category ?? 0,
        "ld_use_search"    => $ld_use_search ?? 0,
        "ld_files"         => $ld_files,
        "ld_access_id"    => $ld_access_id,
        "ld_sort_field"   => $ld_sort_field,
        "ld_use"          => $ld_use,
        "ld_datetime"     => date('Y-m-d H:i'),
    );

    $sql = "UPDATE {$g5['landing']} SET\n".build_query($set, ['ld_content'])." WHERE ld_page = '{$ld_page}' ";
    //die($sql);
    $update = sql_query($sql);

    if ($update) {
        goto_url("./landing_form.php?w=u&ld_page={$ld_page}");
    } else {
        alert('정보 수정에 실패했습니다.', './landing_list.php');
    }

}else{
    $set = array(
        "ld_page"          => $ld_page,
        "ld_subject"       => $ld_subject,
        "ld_content"       => $ld_content,
        "ld_fields"        => $ld_fields,
        "ld_category_list" => $ld_category_list,
        "ld_use_category"  => $ld_use_category ?? 0,
        "ld_use_search"    => $ld_use_search ?? 0,
        "ld_files"         => $ld_files,
        "ld_access_id"     => $ld_access_id,
        "ld_sort_field"    => $ld_sort_field,
        "ld_title"         => $ld_title,
        "ld_use"           => $ld_use,
        "ld_datetime"      => date('Y-m-d H:i'),
    );

    $sql = "INSERT INTO {$g5['landing']} SET\n".build_query($set, ['ld_content']);
    //die($sql);
    $insert = sql_query($sql);

    if($insert){
        goto_url('./landing_list.php');
    }else{
        alert('정보 저장에 실패했습니다.', './landing_list.php');
    }
}