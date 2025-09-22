<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

function build_query(array $data, array $editor_fields = array()):string {
    $set = array();

    foreach ($data as $key => $value) {
        if (is_null($value)) {
            $set[] = "{$key} = NULL";
        } elseif (is_numeric($value) && !preg_match('/^0[0-9]+$/', $value)) {
            $set[] = "{$key} = {$value}";
        } elseif (in_array($key, $editor_fields, true)) {
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

function file_upload_html(
    string $bo_table = '',
    string $name = 'bf_file[]',
    string $id = 'file_input',
    array $files = array(),
    bool $multiple = true,
    bool $include_style = true
):string{
    ob_start(); ?>

    <?php if($include_style): ?>
        <style>
            .file_upload_wrapper {display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-start;}
            .file_upload_box {width: 100px; height: 100px; border: 2px dashed #ccc; border-radius: 8px;
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
            <input type="file" name="<?=$name ?>" id="<?= $id ?>" <?= $multiple ? 'multiple' : '' ?> accept="image/*">
        </div>

        <!-- 기존 파일 영역 -->
        <div id="existing_files" style="display: flex; gap: .5rem;">
        <?php if (!empty($files)): ?>
            <?php foreach ($files as $file): ?>
                <div class="file_upload_box">
                    <img src="<?= G5_DATA_URL ?>/file/<?= $bo_table ?>/<?= $file['bf_file'] ?>" alt="<?= $file['bf_source'] ?>">
                    <button type="button" class="remove_btn" data-bf-no="<?= $file['bf_no'] ?>">X</button>
                    <input type="hidden" name="keep_file[]" value="<?= $file['bf_no'] ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>

        <!-- 새 파일 preview -->
        <div id="preview_container"></div>
    </div>

    <?php return ob_get_clean();
}

function get_file_upload_js(
    string $id = 'file_input',
    string $preview_id = 'preview_container'
): string {
    ob_start(); ?>
    <script>
        $(document).on('change', '#<?= $id ?>', function(){
            const files = this.files;
            if (!files.length) return;

            $('#<?= $preview_id ?>').empty();

            $.each(files, function(i, file){
                const reader = new FileReader();
                reader.onload = function(e){
                    const $box = $('<div>', {class : 'file_upload_box'});
                    const $img = $('<img>', {src : e.target.result});
                    const $remove_btn = $('<button>', {class : 'remove_btn', text : 'X'});
                    $box.append($img).append($remove_btn);
                    $('#<?= $preview_id ?>').append($box);
                };
                reader.readAsDataURL(file);
            });
        });

        // 삭제 버튼
        $(document).on('click', '.remove_btn', function(){
            const $box = $(this).closest('.file_upload_box');
            $box.find('input[name="keep_file[]"]').remove();
            $box.remove();
        });
    </script>
    <?php
    $html = ob_get_clean();
    return str_replace(['<script>', '</script>'], '', $html);
}
