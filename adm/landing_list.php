<?php
$sub_menu = "800100";
include_once('./_common.php');

$g5['title'] = "랜딩페이지 관리";
include_once(G5_ADMIN_PATH.'/admin.head.php');

//페이지네이션
$page = $_GET['page'] ?? 1;
$sql = "SELECT COUNT(*) cnt FROM {$g5['landing']} WHERE (1)";
$row = sql_fetch($sql);

$total_count = $row['cnt'];
$page_rows = $config['cf_page_rows'];

$total_page = ceil($total_count / $page_rows);
$from_record = ($page - 1) * $page_rows;

$paging = get_paging($config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF']."?sca=");

$sql = "SELECT * FROM {$g5['landing']} ORDER BY ld_id DESC LIMIT {$from_record}, {$page_rows}";
$result = sql_query($sql);

while ($row = sql_fetch_array($result)){$list[] = $row;}

?>
<style>
    .label_cate, .label_field {
        display: inline-block;
        padding: 2px 6px;
        margin: 1px;
        font-size: 11px;
        border-radius: 3px;
        background: #f1f1f1;
        border: 1px solid #ddd;
        color: #333;
    }
    .label_cate {
        background: #e0f7fa;
        border-color: #00acc1;
        color: #006064;
    }
    .label_field {
        background: #fce4ec;
        border-color: #f06292;
        color: #880e4f;
    }
    .status-ok {
        display:inline-block;
        padding:2px 6px;
        font-size:12px;
        background:#e0f7e9;
        color:#0a7d34;
        border:1px solid #0a7d34;
        border-radius:4px;
    }
    .status-no {
        display:inline-block;
        padding:2px 6px;
        font-size:12px;
        background:#fdecea;
        color:#b71c1c;
        border:1px solid #b71c1c;
        border-radius:4px;
    }
</style>

<div class="local_ov01 local_ov">
    <span class="btn_ov01"><span class="ov_txt">랜딩페이지 목록</span></span>
</div>

<div class="btn_fixed_top">
    <a href="./landing_form.php" class="btn btn_02">랜딩페이지 추가</a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>랜딩 관리 목록</caption>
        <thead>
        <tr>
            <th scope="col">번호</th>
            <th scope="col">카테고리</th>
            <th scope="col">랜딩ID</th>
            <th scope="col">제목</th>
            <th scope="col">사용여부</th>
            <th scope="col">입력필드</th>
            <th scope="col">등록일</th>
            <th scope="col">스킨생성여부</th>
            <th scope="col">바로가기/스킨생성</th>
            <th scope="col">관리</th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)): ?>
        <?php foreach ($list as $i => $row): ?>
            <?php $skin_file = G5_PATH."/landing/{$row['ld_page']}/landing.skin.php"; ?>
            <?php $is_created = is_file($skin_file); ?>
            <tr>
                <?php $virtual_number = intval($total_count - ($i + $from_record)) ;?>
                <td><?php echo $virtual_number ?></td>
                <td>
                    <?php foreach (explode('|', $row['ld_category_list']) as $cate): ?>
                    <?php if (trim($cate) === '') continue; ?>
                    <span class="label_cate"><?= $cate ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <a href="/landing/?ld_page=<?= $row['ld_page'] ?>" target="_blank">
                        <?php echo $row['ld_page']; ?>
                    </a>
                </td>
                <td><?php echo get_text($row['ld_subject']); ?></td>
                <td><?php echo $row['ld_use'] === 'Y' ? '사용' : '미사용'; ?></td>
                <td>
                    <?php foreach (explode('|', $row['ld_fields']) as $field): ?>
                    <?php if (trim($field) === '') continue; ?>
                    <span class="label_field"><?= $field ?></span>
                    <?php endforeach; ?>
                </td>
                <td><?php echo substr($row['ld_datetime'],0,10); ?></td>
                <td class="td_skin_status">
                    <?php if ($is_created): ?>
                    <span class="status-ok">생성됨</span>
                    <?php else: ?>
                    <span class="status-no">없음</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($is_created): ?>
                    <a href="/landing/?ld_page=<?= $row['ld_page'] ?>" target="_blank" class="btn btn_03">랜딩페이지 바로가기</a>
                    <?php else: ?>
                    <a href="./landing_skin_create.php?ld_page=<?= $row['ld_page'] ?>" onclick="return confirm('스킨을 생성하시겠습니까?');" class="btn btn_02">생성하기</a>
                    <?php endif; ?>
                </td>
                <td style="display: flex; justify-content: center; gap: .7rem;">
                    <a href="./landing_preview.php?ld_page=<?=$row['ld_page']?>" class="btn btn_02" onclick="return open_preview(this.href);">미리보기</a>
                    <a href="./landing_form.php?w=u&ld_page=<?php echo $row['ld_page']; ?>" class="btn btn_03">수정</a>
                    <a href="./landing_list_update.php?w=d&ld_page=<?php echo $row['ld_page']; ?>&ld_id=<?= $row['ld_id'] ?>" class="btn btn_01" onclick="return confirm('정말 삭제하시겠습니까?\n관련 수집 데이터도 모두 삭제됩니다.');">삭제</a>
                </td>
            </tr>
        <?php  endforeach; ?>
        <?php else: ?>
            <tr><td colspan="10">등록된 랜딩페이지가 없습니다.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="paging">
        <?= $paging ?>
    </div>
</div>

<script>
    function open_preview(url) {
        window.open(
            url,
            "landingPreview",
            "width=500,height=800,scrollbars=yes,resizable=yes"
        );
        return false;
    }
</script>


<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');


