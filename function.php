<?php
$sub_menu = "500100";
require_once './_common.php';
auth_check($auth[$sub_menu], "w");

$g5['title'] = $w === 'u' ? '배너 수정' : '배너 입력';

$target_table = $g5['write_prefix'] . $bo_table;

if ($w === 'u') {
    $sql = "SELECT * FROM {$target_table} WHERE wr_id = '{$wr_id}' ";
    $banner = sql_fetch($sql);
    $file_data = get_file($bo_table, $wr_id);
}

?>

<?php include_once(G5_ADMIN_PATH.'/admin.head.php'); ?>

<form name="fbanner" id="fbanner" action="./banner_form_update.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="bo_table" value="<?= $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?= $wr_id ?>">
    <input type="hidden" name="w" value="<?= $w ?>">

    <div class="tbl_frm01 tbl_wrap">
        <table>
            <colgroup>
                <col class="grid_3">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row"><label for="wr_subject">제목</label></th>
                <td>
                    <input type="text" name="wr_subject" id="wr_subject" value="<?= get_text($banner['wr_subject'] ?? '') ?>" required class="frm_input" size="60">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="bn_title">시작일</label></th>
                <td>
                    <input type="date" name="bn_title" id="bn_title" value="<?= $banner['wr_start'] ?? '' ?>" required class="frm_input">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="bn_link">종료일</label></th>
                <td>
                    <input type="date" name="bn_link" id="bn_link" value="<?= $banner['wr_end'] ?? '' ?>" class="frm_input">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bn_title">이미지 첨부</label></th>
                <td>
                    <?= file_upload_html($bo_table, $file_data ?? array()) ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bn_title">등록일</label></th>
                <td>
                    <input type="text" name="bn_title" id="bn_title" value="<?= get_text($banner['bn_title'] ?? '') ?>" required class="frm_input" size="60">
                </td>
            </tr>

            <tr>
                <th scope="row">사용 여부</th>
                <td>
                    <?php $bn_use = $banner['bn_use'] ?? '1'; ?>
                    <label><input type="radio" name="bn_use" value="1" <?= $bn_use == '1' ? 'checked' : '' ?>> 사용</label>
                    <label style="margin-left:15px;"><input type="radio" name="bn_use" value="0" <?= $bn_use == '0' ? 'checked' : '' ?>> 중지</label>
                </td>
            </tr>

            <?php if ($bn_id): ?>
                <tr>
                    <th scope="row">등록일</th>
                    <td><?= $banner['bn_datetime'] ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="btn_fixed_top">
        <input type="submit" value="저장" class="btn btn_submit">
        <a href="./banner_list.php" class="btn btn_02">목록</a>
    </div>
</form>

<?php include_once(G5_ADMIN_PATH.'/admin.tail.php'); ?>
