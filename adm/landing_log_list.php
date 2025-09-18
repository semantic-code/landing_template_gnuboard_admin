<?php
$sub_menu = "800200";
require_once './_common.php';

//auth_check_menu($auth, $sub_menu, 'r');

$sql = "SELECT * FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ";
$cf = sql_fetch($sql);

//접근 아이디 확인
$arr_access_id = explode('|', $cf['ld_access_id']);
if($member['mb_id'] !== 'admin' && !in_array($member['mb_id'], $arr_access_id)){
    alert('해당 페이지는 이용할 수 없습니다.\n관리자에게 문의하세요.');
}


$landing_title = $cf['ld_subject'];

//
$g5['title'] = "{$landing_title}";
require_once './admin.head.php';

//필드 값 여부
list($fn_1, $fn_2, $fn_3, $fn_4, $fn_5) = explode('|', $cf['ld_fields']);
$is_use_field_1 = isset($fn_1) ? 1 : 0;
$is_use_field_2 = isset($fn_2) ? 1 : 0;
$is_use_field_3 = isset($fn_3) ? 1 : 0;
$is_use_field_4 = isset($fn_4) ? 1 : 0;
$is_use_field_5 = isset($fn_5) ? 1 : 0;

//검색 사용 여부
$ld_use_search = $cf['ld_use_search'] ?? 0;
//카테고리 사용 여부
$ld_use_category = $cf['ld_use_category'] ?? 0;
$arr_cate = explode('|', $cf['ld_category_list']);

//정렬
$order_by = $cf['ld_sort_field'] ? "ORDER BY {$cf['ld_sort_field']}" : "ORDER BY ldg_id DESC";

//쿼리 초기화
$ld_sql = "";
$search_sql = "";
$sca_sql = "";

//랜딩페이지 아이디
if($ld_page) $ld_sql.= "AND ld_page = '{$ld_page}' ";

//카테고리
if($sca) $sca_sql.= "AND ldg_cate = '{$sca}' ";

//검색 sfl, stx
if($_GET['sfl']){
    $sfl = htmlspecialchars(addslashes(urldecode(trim($_GET['sfl']))), ENT_QUOTES);
    $stx = htmlspecialchars(addslashes(urldecode(trim($_GET['stx']))), ENT_QUOTES);

    if (!in_array($sfl, array('ldg_field_1', 'ldg_field_2', 'ldg_field_3', 'ldg_field_4', 'ldg_field_5'))) {
        alert('잘못된 경로로 접속하셨습니다.\n정상적인 방법으로 접속하여 주시기 바랍니다.');
        exit;
    }

    if($sfl === 'ldg_field_1'){
        $search_sql.= "AND INSTR(ldg_field_1, '{$stx}')  > 0 ";
    }
    if($sfl === 'ldg_field_2'){
        $search_sql.= "AND INSTR(ldg_field_2, '{$stx}')  > 0 ";
    }
    if($sfl === 'ldg_field_3'){
        $search_sql.= "AND INSTR(ldg_field_3, '{$stx}')  > 0 ";
    }
    if($sfl === 'ldg_field_4'){
        $search_sql.= "AND INSTR(ldg_field_4, '{$stx}')  > 0 ";
    }
    if($sfl === 'ldg_field_5'){
        $search_sql.= "AND INSTR(ldg_field_5, '{$stx}')  > 0 ";
    }
}


//총 레코드 수
$sql = " SELECT COUNT(*) AS cnt FROM {$g5['landing_log']} WHERE (1) {$ld_sql} {$sca_sql} {$search_sql} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

//페이징
$page = $_GET['page']  ?: 1;
$page_rows = $config['cf_page_rows'];
$total_page = ceil($total_count / $page_rows);
$from_record = ($page - 1) * $page_rows;
$paging = get_paging(5, $page, $total_page, G5_ADMIN_URL."/landing_log_list.php?ld_page=".$ld_page."&sfl=".$sfl."&stx=".$stx);

$str_sql = "SELECT * FROM {$g5['landing_log']} WHERE (1) {$ld_sql} {$sca_sql} {$search_sql} {$order_by} LIMIT {$from_record}, {$page_rows}";
$result = sql_query($str_sql);

while ($row = sql_fetch_array($result)){
    $row_data = $row;
    $list[] = $row_data;
}

$colspan = 4;
if($ld_use_category) $colspan++;
if($is_use_field_1) $colspan++;
if($is_use_field_2) $colspan++;
if($is_use_field_3) $colspan++;
if($is_use_field_4) $colspan++;
if($is_use_field_5) $colspan++;

?>

<style>
    /* 상단 툴바 컨테이너 */
    .admin-toolbar{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        padding:12px 16px;
        border:1px solid #e5e7eb;
        border-radius:8px;
        background:#fafafa;
        margin:10px 0 15px;
    }

    /* 상태 탭 */
    .admin-tabs{
        display:flex; gap:6px; list-style:none; padding:0; margin:0;
    }
    .admin-tabs .tab{
        display:inline-block;
        padding:5px 10px;
        border:1px solid #d1d5db;
        border-radius:6px;
        background:#fff;
        color:#374151; text-decoration:none; font-size:14px;
    }
    .admin-tabs .tab:hover{ background:#f3f4f6; }
    .admin-tabs .tab.is-active{
        background:#3f51b5; color:#fff; border-color:#1d4ed8;
    }

    /* 검색 폼 (카테고리 줄 + 검색 줄을 세로 배치) */
    .admin-search{
        display:flex;
        flex-direction:column;
        gap:8px;
        flex:1;
    }

    /* 각 줄 */
    .admin-search__row{
        display:flex;
        align-items:center;
        gap:8px;
    }

    /* 카테고리 전용 줄: 폭 넓게 */
    .admin-search__row--full select{
        min-width:260px;
    }

    /* 셀렉트/인풋 공통 */
    .admin-search .sel,
    .admin-search .inp{
        height:30px;
        border:1px solid #d1d5db;
        border-radius:6px;
        padding:0 10px;
        background:#fff;
    }
    .admin-search .inp{
        min-width:220px;
    }
    .admin-search .inp:focus,
    .admin-search .sel:focus{
        border-color:#6366f1;
        box-shadow:0 0 0 3px rgba(99,102,241,.15);
        outline:0;
    }

    /* 버튼 */
    .btn{
        display:inline-flex; align-items:center; justify-content:center;
        height:30px; padding:0 12px; border-radius:6px;
        text-decoration:none; cursor:pointer; user-select:none;
    }
    .btn-primary{ background:#3f51b5; color:#fff; border:1px solid #1d4ed8; }
    .btn-primary:hover{ background:#1d4ed8; }
    .btn-line{ background:#fff; color:#374151; border:1px solid #d1d5db; }
    .btn-line:hover{ background:#f3f4f6; }

    /* 표와 간격 정리(선택) */
    .tbl_head01{ margin-top:10px; }

</style>

<section>
    <?php if($ld_use_category): ?>
        <div class="admin-toolbar">
            <!-- 카테고리 탭 -->
            <ul class="admin-tabs" role="tablist">
                <li><a href="?ld_page=<?= $ld_page ?>&sca=&sfl=<?= $sfl ?>&stx=<?= $stx ?>" class="tab <?= ($sca === '') ? 'is-active' : '' ?>">전체</a></li>
                <?php foreach ($arr_cate as $cate): ?>
                    <?php $is_active = $sca === $cate ? 'is-active' : '' ; ?>
                    <li><a href="?ld_page=<?= $ld_page ?>&sca=<?= $cate?>&sfl=<?= $sfl ?>&stx=<?= $stx ?>" class="tab <?= $is_active ?>"><?= $cate?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if($ld_use_search): ?>
    <!-- 검색 폼 시작 -->
    <form class="admin-search" method="get" action="<?= $_SERVER['PHP_SELF'] ?>" style="display: block;">
        <input type="hidden" name="ld_page" value="<?= $ld_page ?>">
        <input type="hidden" name="sca" value="<?= $sca ?>">
        <!-- 검색 줄 -->
        <div class="admin-search__row">
            <label for="search_field" class="sound-only">검색조건</label>
            <select name="sfl" id="search_field" class="sel">
                <?php $fields = array($fn_1, $fn_2, $fn_3, $fn_4, $fn_5); ?>
                <?php foreach ($fields as $i => $label): ?>
                <?php if(!$label) continue; ?>
                <?php $field_name = "ldg_field_".($i + 1); ?>
                <option value="<?= $field_name ?>" <?= get_selected($field_name, $sfl); ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="stx" id="search_text" class="inp" placeholder="검색어 입력" value="<?= $stx ?>">
            <button type="submit" class="btn btn-primary">검색</button>
            <a href="./landing_log_list.php?ld_page=<?= $ld_page ?>" class="btn btn_02">초기화</a>
        </div>
    </form>
    <!-- 검색 폼 끝 -->
    <?php endif; ?>

    <div class="tbl_head01">
        <form id="contact-form" onsubmit="return false">
            <table>
                <thead>
                <tr style="">
                    <th>번호</th>
                    <?php if($is_use_field_1): ?>
                    <th><?= $fn_1 ?></th>
                    <?php endif; ?>
                    <?php if($is_use_field_2): ?>
                    <th><?= $fn_2 ?></th>
                    <?php endif; ?>
                    <?php if($is_use_field_3): ?>
                    <th><?= $fn_3 ?></th>
                    <?php endif; ?>
                    <?php if($is_use_field_4): ?>
                    <th><?= $fn_4 ?></th>
                    <?php endif; ?>
                    <?php if($is_use_field_5): ?>
                    <th><?= $fn_5 ?></th>
                    <?php endif; ?>
                    <th>날짜</th>
                    <?php if($ld_use_category): ?>
                        <th>상태변경</th>
                    <?php endif; ?>
                    <th>간단메모</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($list)): ?>
                    <?php foreach ($list as $i => $row): ?>
                    <?php $virtual_number = intval($total_count - ($i + $from_record)) ;?>
                        <tr>
                            <td><?= $virtual_number ?></td>
                            <?php if($is_use_field_1): ?>
                            <td><?= $row['ldg_field_1'] ?></td>
                            <?php endif; ?>
                            <?php if($is_use_field_2): ?>
                            <td><?= $row['ldg_field_2'] ?></td>
                            <?php endif; ?>
                            <?php if($is_use_field_3): ?>
                            <td><?= $row['ldg_field_3'] ?></td>
                            <?php endif; ?>
                            <?php if($is_use_field_4): ?>
                            <td><?= $row['ldg_field_4'] ?></td>
                            <?php endif; ?>
                            <?php if($is_use_field_5): ?>
                            <td><?= $row['ldg_field_5'] ?></td>
                            <?php endif; ?>
                            <td><?= date('Y-m-d H:i:s', strtotime($row['ldg_datetime'])) ?></td>
                            <?php if($ld_use_category): ?>
                                <td>
                                    <?php if($cf['ld_category_list'] && $cf['ld_use_category']): ?>
                                        <select id="ldg_cate" name="ldg_cate" data-ldg-id="<?= $row['ldg_id'] ?>">
                                            <?php foreach ($arr_cate as $cate): ?>
                                                <option value="<?= $cate ?>" <?= get_selected($row['ldg_cate'], $cate) ?>><?= $cate ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif;?>
                                </td>
                            <?php endif; ?>
                            <td style="display: flex;">
                                <input type="text" name="ldg_memo" value="<?= $row['ldg_memo'] ?>" style="width: 90%; margin-right: .5rem;">
                                <div style="display: flex; gap: .5rem; width: 235px;">
                                    <button type="button" class="btn btn_03 btn_memo_update" data-ldg-id="<?= $row['ldg_id'] ?>">메모저장</button>
                                    <button type="button" class="btn btn_01 btn_delete" data-ldg-id="<?= $row['ldg_id'] ?>">상담글삭제</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $colspan ?>">데이터가 없습니다.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <?php if($member['mb_id'] === 'admin'): ?>
            <div class="btn_fixed_top">
                <a href="./landing_form.php?w=u&ld_page=<?= $ld_page ?>" class="btn btn_02">랜딩페이지 설정</a>
            </div>
            <?php endif; ?>
    </div>

    <div class="paging"><?= $paging ?></div>

</section>

<script>
    const ld_page = <?= json_encode($ld_page) ?>;

    $(document).ready(function (){
        //상태변경
        $(document).on('change', '#ldg_cate', function(){
            const mode = 'update_category';
            const ldg_id = $(this).data('ldg-id');
            const ldg_cate = $(this).val();

            $.post("./landing_log_list_update.php", {mode, ld_page, ldg_id, ldg_cate}, function(data){
                if(data.state === 'success_update_category'){
                    self.location.reload();
                }
            }, 'json');
        });

        //메모저장
        $(document).on('click', '.btn_memo_update', function(){
            const mode = 'update_memo';
            const ldg_id = $(this).data('ldg-id');
            const ldg_memo = $(this).closest('td').find('input[type="text"]').val();

            $.post("./landing_log_list_update.php", {mode, ld_page, ldg_id, ldg_memo}, function(){
                if(data.state === 'success_update_memo'){
                    self.location.reload();
                }
            }, 'json');
        });

        //상담글삭제
        $(document).on('click', '.btn_delete', function(){
            if(!confirm("글을 삭제하시겠습니까?\n한번 삭제한 글은 복구할 수 없습니다.")) return false;
            const mode = 'delete';
            const ldg_id = $(this).data('ldg-id');

            $.post("./landing_log_list_update.php", {mode, ld_page, ldg_id}, function(data){
                if(data.state === 'success_delete'){
                    self.location.reload();
                }
            }, 'json');
        });
    });
</script>

<?php
require_once './admin.tail.php';