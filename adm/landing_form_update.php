<?php
include_once('./_common.php');

/*
echo '<pre>';
print_r($_POST);
echo '<pre>';
exit;
*/


$upload_dir = G5_DATA_PATH.'/file/'.$bo_table;
@mkdir($upload_dir, G5_DIR_PERMISSION, true);

$file = $_FILES['bf_file'] ?? null;

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

    $sql = "INSERT INTO {$g5['landing']} SET\n".build_query($set, ['ld_content']);
    $insert = sql_query($sql);
    $ld_id = sql_insert_id();

    if($file &&  is_array($file['name'])){
        $cnt = count($file['name']);

        for ($i = 0; $i < $cnt; $i++){
            if($file['error'][$i] !== UPLOAD_ERR_OK) continue;

            //확장자
            $ext = pathinfo($file['name'][$i], PATHINFO_EXTENSION);

            //파일명
            $new_file = md5(uniqid('', true)).'.'.$ext;
            $dest = $upload_dir.'/'.$new_file;

            //업로드 이동
            if(move_uploaded_file($file['tmp_name'][$i], $dest)){
                @chmod($dest, G5_FILE_PERMISSION);

                //DB
                $source = sql_real_escape_string($file['name'][$i]);
                $size = $file['size'][$i];

                $set = array(
                    "bo_table"    => $bo_table,
                    "wr_id"       => $ld_id,
                    "bf_no"       => $i,
                    "bf_source"   => $source,
                    "bf_file"     => $new_file,
                    "bf_download" => 0,
                    "bf_content"  => '',
                    "bf_filesize" => $size,
                    "bf_type"     => preg_match('/\.(jpg|jpeg|png|gif)$/i',$ext)? 1:0,
                    "bf_datetime" => G5_TIME_YMDHIS
                );
                $sql = "INSERT INTO {$g5['board_file_table']} SET\n".build_query($set);
                sql_query($sql);

            }
        }
    }
    goto_url("./landing_list.php");

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

    $sql = "UPDATE {$g5['landing']} SET\n".build_query($set, ['ld_content']);
    $insert = sql_query($sql);

    //ld_id
    $row = sql_fetch("SELECT ld_id FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ");
    $ld_id = $row['ld_id'];

    //삭제요청 파일 삭제 처리
    $keep_file = $_POST['keep_file'] ?? array();
    $keep_file = array_map('intval', (array)$keep_file);

    $sql = "SELECT bf_no, bf_file FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$ld_id}' ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        if (!in_array((int)$row['bf_no'], $keep_file, true)) {
            // DB 삭제
            $sql = "DELETE FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$ld_id}' AND bf_no = '{$row['bf_no']}'";
            sql_query($sql);

            // 파일 삭제
            if ($row['bf_file'] && file_exists($upload_dir.'/'.$row['bf_file'])) {
                @unlink($upload_dir.'/'.$row['bf_file']);
            }
        }
    }

    //bf_no 최대값 구하기
    $sql = "SELECT COALESCE(MAX(bf_no), -1) AS max_bf_no FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$ld_id}' ";
    $row = sql_fetch($sql);
    $next_bf_no = ((int)$row['max_bf_no']) + 1;

    //세로운 파일 업로드
    if ($file && is_array($file['name'])) {
        $cnt = count($file['name']);
        for ($i = 0; $i < $cnt; $i++) {
            if ($file['error'][$i] !== UPLOAD_ERR_OK) continue;

            $ext = pathinfo($file['name'][$i], PATHINFO_EXTENSION);
            $new_file = md5(uniqid('', true)).'.'.$ext;
            $dest = $upload_dir.'/'.$new_file;

            if (move_uploaded_file($file['tmp_name'][$i], $dest)) {
                @chmod($dest, G5_FILE_PERMISSION);

                $source = sql_real_escape_string($file['name'][$i]);
                $size = $file['size'][$i];
                $is_img = preg_match('/\.(jpg|jpeg|png|gif)$/i', $ext) ? 1 : 0;

                //저장
                $set = array(
                    "bo_table"    => $bo_table,
                    "wr_id"       => $ld_id,
                    "bf_no"       => $next_bf_no++,
                    "bf_source"   => $source,
                    "bf_file"     => $new_file,
                    "bf_download" => 0,
                    "bf_content"  => '',
                    "bf_filesize" => $size,
                    "bf_type"     => $is_img,
                    "bf_datetime" => G5_TIME_YMDHIS
                );
                $sql = "INSERT INTO {$g5['board_file_table']} SET\n".build_query($set);
                sql_query($sql);
            }
        }
    }
    goto_url("./landing_form.php?w=u&ld_page={$ld_page}");
}
