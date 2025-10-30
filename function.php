<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/**
 * 배열 데이터를 SQL SET 구문 형태로 변환
 *
 * @param array $data            입력 데이터 (key => value 형태)
 * @param string $editor_fields   에디터 전용 필드 (addslashes 대신 stripslashes 저장)
 *
 * @return string                SQL SET 구문 문자열
 *
 * $sql = "INSERT INTO {$table} SET\n" . build_query($set, 'wr_content');
 */
function build_query(
    array $data,
    string $editor_fields = ''
):string {
    $set = array();

    foreach ($data as $key => $value) {
        if (is_null($value)) {
            $set[] = "{$key} = NULL";
        } elseif (is_numeric($value) && !preg_match('/^0[0-9]+$/', $value)) {
            $set[] = "{$key} = {$value}";
        } elseif ($key === $editor_fields) {
            // 에디터 전용 필드 → stripslashes 후 그대로 저장
            $clean_value = stripslashes($value);
            $set[] = "{$key} = '{$clean_value}'";
        } else {
            // 일반 텍스트 → escape 처리
            $escaped_value = addslashes($value);
            $set[] = "{$key} = '{$escaped_value}'";
        }
    }
    return implode(",\n", $set);
}

/**
 * 테이블 구조를 분석해, 기본값이 없는 NOT NULL 필드에 자동으로 빈값('')을 채우기 위한 배열 생성
 *
 * @param string $table_name  대상 테이블명 (예: g5_write_notice)
 * @param array  $ignore_cols 무시할 칼럼 (자동 생성되는 wr_id, wr_num 등)
 * @return array              기본값이 없는 NOT NULL 칼럼 목록
 */
function get_empty_fields(string $table_name, array $ignore_cols = []): array {
    global $g5;

    // 기본 무시 목록 (자동 증가나 시스템 필드)
    $ignore_defaults = array_merge([
        'wr_id', 'wr_num', 'wr_parent', 'wr_is_comment',
        'wr_datetime', 'wr_last', 'wr_ip', 'wr_hit'
    ], $ignore_cols);

    $fields = [];
    $sql = "SHOW FULL COLUMNS FROM {$table_name}";
    $res = sql_query($sql);

    while ($row = sql_fetch_array($res)) {
        $field = $row['Field'];
        $default = $row['Default'];
        $null = strtoupper($row['Null']);
        $extra = $row['Extra'];

        // 무시대상 제외
        if (in_array($field, $ignore_defaults)) continue;

        // AUTO_INCREMENT 제외
        if (stripos($extra, 'auto_increment') !== false) continue;

        // NOT NULL + 기본값 없음 => 빈값 대상
        if ($null === 'NO' && is_null($default)) {
            $fields[] = $field;
        }
    }

    return $fields;
}

/**
 * 파일 업로드 입력창 HTML 생성
 *
 * @param string $bo_table       게시판 테이블명 (기존 파일 미리보기 경로용)
 * @param array  $files          기존 업로드된 파일 배열 (get_file() 결과 등)
 * @param string $name           input name 속성명 (기본: bf_file[])
 * @param string $id             input id 속성명 (기본: file_input)
 * @param bool   $image_only     이미지 또는 파일 업로드 (기본 : true)
 * @param bool   $multiple       다중 업로드 허용 여부 (기본: true)
 * @param bool   $include_style  CSS 포함 여부 (기본: true) *
 * @return string                파일 업로드 HTML 마크업 *
 */
function file_upload_html(
    string $bo_table      = '',
    array  $files         = array(),
    string $name          = 'bf_file[]',
    string $id            = 'file_input',
    bool   $image_only    = false,
    bool   $multiple      = true,
    bool   $include_style = true,
    int    $width         = 100,
    int    $height        = 100,
):string {
    ob_start(); ?>

    <?php if($include_style): ?>
        <style>
            .file_upload_wrapper {display: flex; gap: .5rem; flex-wrap: nowrap; align-items: flex-start;}
            .file_upload_box {width: <?= $width ?>px; height: <?= $height ?>px; border: 2px dashed #ccc; border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                position: relative; overflow: hidden; background: #f9f9f9;}
            .file_upload_box img {width: 100%; height: 100%; object-fit: cover;}
            .file_upload_box.add_box {cursor: pointer;}
            .file_upload_box.add_box label {position: relative;}
            .file_upload_box.add_box label span {position: absolute; top: 50%; left: 50%;
                transform: translate(-50%, -60%); font-size: 48px;
                user-select: none; cursor: pointer;}
            .file_upload_box input[type=file] {display: none;}
            .remove_btn {position: absolute; top: 3px; right: 3px; padding-bottom: 5px;
                background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%;
                width: 20px; height: 20px; cursor: pointer; font-size: 14px; line-height: 18px;
                text-align: center;}
            #existing_files, #preview_container {display: flex; gap: 10px; flex-wrap: wrap;}
        </style>   
    <?php endif; ?>

    <div class="file_upload_wrapper">
        <!-- 새 파일 추가 버튼 -->
        <div class="file_upload_box add_box">
            <label for="<?= $id ?>"><span>+</span></label>
            <input type="file" name="<?//= $id ?>" id="<?= $id ?>" <?= $multiple ? 'multiple' : '' ?> <?= $image_only ? 'accept="image/*"' : '' ?>>
        </div>

        <!-- 기존 파일 영역 -->
        <div id="existing_files" style="display: flex; gap: .5rem;">
            <?php if (!empty($files)): ?>
                <?php foreach ($files as $key => $file): ?>
                    <div class="file_upload_box">
                        <img src="<?= G5_DATA_URL ?>/file/<?= $bo_table ?>/<?= $file['bf_file'] ?? $file['file'] ?>" alt="<?= $file['bf_source'] ?>">
                        <button type="button" class="remove_btn" data-bf-no="<?= $file['bf_no'] ?? $key ?>">X</button>
                        <input type="hidden" name="keep_file[]" value="<?= $file['bf_no'] ?? $key ?>">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 새 파일 preview -->
        <div id="preview_container"></div>
    </div>

    <?php return ob_get_clean();
}

/**
 * 파일 업로드 미리보기 및 삭제 기능용 JavaScript 반환
 *
 * @param string $id            파일 input 요소의 id (기본: file_input)
 * @param string $preview_id    미리보기 컨테이너 id (기본: preview_container)
 *
 * @return string               JavaScript 코드 (script 태그 제외)
 *
 */
function get_file_upload_js(
    string $id = 'file_input',
    string $preview_id = 'preview_container'
): string {
    ob_start(); ?>
    <script>
        $(document).on('change', '#<?= $id ?>', function(e){
            const files = e.target.files;
            if(!files.length) return false;

            $.each(files, function(i, file){
                const ext = file.name.split('.').pop().toLowerCase();

                // 새로운 input 생성, DataTransfer
                const dt = new DataTransfer();
                dt.items.add(file);
                const $input = $('<input>', {type: 'file', name: 'bf_file[]'}).prop('files', dt.files);

                // 박스 생성 및 구성
                const $box = $('<div>', {class: 'file_upload_box new-file'}).append($input);
                const $remove_btn = $('<button>', {class: 'remove_btn', text: 'X'});

                // 이미지 파일이면 미리 보기
                if(['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext)) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        $box.append($('<img>', {src: ev.target.result}));
                    }
                    reader.readAsDataURL(file)

                } else {
                    const $file_info  = $('<div>', {class: 'file-info'});
                    const $p = $('<p>').css('padding', '3px');
                    const $span = $('<span>').text(file.name);

                    $p.append($span);
                    $file_info.append($p);
                    $box.append($file_info);
                }

                $box.append($remove_btn);
                $('#<?= $preview_id ?>').append($box);
            });
            $(this).val('');
        });

        // 삭제 버튼 클릭 시 input + 박스 제거
        $(document).on('click', '.remove_btn', function(){
            $(this).closest('.file_upload_box').remove();

            const $old_input = $('#<?= $id ?>');
            const $new_input = $old_input.clone().val('');
            $old_input.replaceWith($new_input);
        });
    </script>
    <?php
    $html = ob_get_clean();
    return str_replace(['<script>', '</script>'], '', $html);
}

/**
 * 파일 첨부 처리
 *
 * @param array  $files (기본 : $_FILES['bf_file'])
 * @param string $bo_table 게시판 테이블명
 * @param int    $wr_id 글 고유 아이디
 * @param string $upload_dir 업로드 경로 (기본: /data/file/{bo_table})
 *
 * @return bool  파일 업로드 성공여부
 **/
function attach_file(
    array  $files,
    string $bo_table,
    int    $wr_id,
    string $upload_dir = ''
): bool {
    global $g5;

    if (empty($wr_id)) {
        alert('파일 업로드에 필요한 wr_id 값이 없습니다.');
        return false;
    }

    $upload_dir = $upload_dir ?: G5_DATA_PATH . "/file/{$bo_table}";

    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, G5_DIR_PERMISSION, true);
        @chmod($upload_dir, G5_DIR_PERMISSION);
    }

    // enctype 누락 감지
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_FILES)) {
        alert("The form is missing the enctype attribute.");
        return false;
    }

    // 파일이 없어도 return true
    if (empty($files) || empty($files['name'][0])) {
        return true;
    }
    
    // 현재 wr_id에서 가장 큰 bf_no 조회, 다음 번호부터 생성
    $sql = "SELECT MAX(bf_no) AS max_bf_no FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$wr_id}' ";
    $row = sql_fetch($sql);
    $bf_cursor = is_null($row['max_bf_no']) ? 0 : (int)$row['max_bf_no'] + 1;

    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK || !$files['name'][$i]) continue;

        //확장자 검사
        $original_name = $files['name'][$i];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        $deny_ext = array('php', 'phar', 'exe', 'sh', 'js');
        if (in_array($ext, $deny_ext)) {
            alert("허용되지 않는 파일 형식입니다. ({$ext})");
        }

        // 파일명 생성
        $new_name = date('YmdHis') . '_' . md5(uniqid('', true)) . '.' . $ext;
        $dest_path = "{$upload_dir}/{$new_name}";

        //파일 이동
        if (!move_uploaded_file($files['tmp_name'][$i], $dest_path)) {
            return false;
        }

        @chmod($dest_path, G5_FILE_PERMISSION);

        //이미지 정보
        $bf_width = 0;
        $bf_height = 0;
        $img_info = getimagesize($dest_path);
        if ($img_info) {
            $bf_width = $img_info[0];
            $bf_height = $img_info[1];
        }

        //DB 저장
        $bf_source = sql_real_escape_string($original_name);
        $bf_file = sql_real_escape_string($new_name);
        $bf_filesize = (int)$files['size'][$i];

        $sql = "
            INSERT INTO {$g5['board_file_table']}
            SET bo_table    = '{$bo_table}',
                wr_id       = '{$wr_id}',
                bf_no       = '{$bf_cursor}',
                bf_source   = '{$bf_source}',
                bf_file     = '{$bf_file}',
                bf_content  = '',
                bf_filesize = '{$bf_filesize}',
                bf_width    = '{$bf_width}',
                bf_height   = '{$bf_height}',
                bf_datetime = NOW()
        ";
        $insert = sql_query($sql);

        if (!$insert) {
            @unlink($dest_path);
            return false;
        }
        $bf_cursor++;
    }

    return true;
}

/**
 * 게시물에 연결된 첨부파일을 삭제
 *
 * @param string $bo_table   게시판 테이블명
 * @param int    $wr_id      글 고유 ID
 * @param int|null $bf_no    첨부파일 중 일부만 삭제할 때 사용
 * @param string $upload_dir 업로드 경로 (기본: /data/file/{bo_table})
 *
 * @return bool              성공 여부 (삭제할 파일이 없어도 true)
 */
function delete_attach_file(
    string $bo_table,
    int    $wr_id,
    ?int   $bf_no = null,
    string $upload_dir = ''
):bool {
    global $g5;

    if (!$bo_table || !$wr_id) return false;

    $upload_dir = $upload_dir ?: G5_DATA_PATH . "/file/{$bo_table}";
    $bf_sql = is_null($bf_no) ? "" : " AND bf_no = '{$bf_no}' ";

    $sql_select  = "SELECT * FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$wr_id}' {$bf_sql} ";
    $result = sql_query($sql_select);
    while ($file = sql_fetch_array($result)) {
        $file_path = "{$upload_dir}/{$file['bf_file']}";

        if (is_file($file_path)) {
            @unlink($file_path);
        }
    }
    // DB 삭제
    $sql_delete = "DELETE FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$wr_id}' {$bf_sql}  ";
    $delete = sql_query($sql_delete);

    if (!$delete) return false;

    return true;
}
