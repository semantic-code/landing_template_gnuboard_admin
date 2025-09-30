<?php
include_once('./_common.php');

$files = $_FILES['bf_file'] ?? null;

if($w === ''){
    //입력
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
        "ld_use"           => $ld_use,
        "ld_datetime"      => date('Y-m-d H:i'),
    );

    $sql = "INSERT INTO {$g5['landing']} SET\n".build_query($set);
    $insert = sql_query($sql);
    if (!$insert) alert('데이터 추가에 실패했습니다.');

    $ld_id = sql_insert_id();

    if (!attach_file($files, $bo_table, $ld_id)){
        alert("파일 업로드에 실패하였습니다.");
    }else{
        goto_url("./landing_list.php");
    }

}else{
    //수정
    if(!$ld_page) alert('잘못된 접근입니다.');

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
        "ld_use"           => $ld_use,
        "ld_datetime"      => date('Y-m-d H:i'),
    );

    $sql = "UPDATE {$g5['landing']} SET\n" . build_query($set) . "\nWHERE ld_id = '{$ld_id}' ";
    $update = sql_query($sql);
    if (!$update) alert('데이터 업데이트에 실패했습니다.');

    //삭제요청 파일 삭제 처리
    $keep_file = $_POST['keep_file'] ?? array();
    $keep_file = array_map('intval', (array)$keep_file);

    //첨부파일 전체
    $sql = "SELECT bf_no FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$ld_id}' ";
    $result = sql_query($sql);
    //삭제요청 파일 삭제
    while ($row = sql_fetch_array($result)) {
        if (!in_array((int)$row['bf_no'], $keep_file, true)) {
            // DB 삭제, 파일 삭제
            delete_attach_file($bo_table, $ld_id, $row['bf_no']);
        }
    }

    //새로운 파일 업로드
    if (!attach_file($files, $bo_table, $ld_id)){
        alert("파일 수정 업로드에 실패하였습니다.");
    }else{
        goto_url("./landing_form.php?w=u&ld_page={$ld_page}");
    }
}

