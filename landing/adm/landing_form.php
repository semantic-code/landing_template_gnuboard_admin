<?php
$sub_menu = "800100"; // 원하는 메뉴 코드 등록
require_once './_common.php';

$bo_table = "landing";
$html_title = '랜딩페이지';

$landing = array();

if($w == 'u'){
    $html_title.= ' 수정';

    //랜딩 페이지
    if (isset($ld_page) && $ld_page) {
        $sql = "SELECT * FROM {$g5['landing']} WHERE ld_page = '{$ld_page}'";
        $landing = sql_fetch($sql);
        if (!$landing) alert('존재하지 않는 랜딩입니다.');
    }

    //파일 데이터
    $landing['file'] = array();
    $sql = "SELECT * FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = {$landing['ld_id']} ORDER BY bf_no ASC ";
    $result = sql_query($sql);
    for ($i = 0; $row = sql_fetch_array($result); $i++){
        $landing['file'][$i] = $row;
    }


}else{
    $html_title.= ' 등록';
}

$g5['title'] = $html_title;
include_once(G5_ADMIN_PATH.'/admin.head.php');

?>

<form name="frmLanding" id="frmLanding" action="./landing_form_update.php" method="post" enctype="multipart/form-data" onsubmit="return on_submit(this)">
    <input type="hidden" name="bo_table" value="landing">
    <input type="hidden" name="ld_page" value="<?php echo $landing['ld_page'] ?? ''; ?>">
    <input type="hidden" name="w" value="<?= $w ?>">
    <input type="hidden" name="chk_ld_page" value="1">
    <input type="hidden" name="wr_content" value="내용 - <?= time() ?>">

    <div class="local_ov01 local_ov">
        <span class="btn_ov01"><span class="ov_txt">랜딩페이지 추가/수정</span></span>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
            <caption>랜딩 정보</caption>
            <colgroup>
                <col class="grid_4">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row"><label for="ld_page">랜딩 ID</label></th>
                <td>
                    <input type="text" name="ld_page" id="ld_page" value="<?php echo $landing['ld_page'] ?? ''; ?>" required class="frm_input" size="30" >
                    <?php if($w !== 'u'): ?>
                        <button type="button" class="btn btn_02" id="btn_id_check" style="margin-left: .5rem;">중복확인</button>
                    <?php endif; ?>
                    <span class="frm_info">URL 식별자 (예: landing.php?ld_page=여기에 필요한 값, 'basic' 제외)</span>
                    <span class="frm_info">생성된 아이디는 수정할 수 없습니다.</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ld_access_id">접근 아이디 설정</label></th>
                <td>
                    <input type="text" name="ld_access_id" id="ld_access_id" value="<?php echo $landing['ld_access_id'] ?? ''; ?>" class="frm_input" size="80">
                    <span class="frm_info">admin을 제외한 아이디 설정 (예: master|adm|user01)</span>
                </td>

            </tr>
            <tr>
                <th scope="row"><label for="ld_category_list">카테고리</label></th>
                <td>
                    <input type="text" name="ld_category_list" id="ld_category_list" value="<?php echo $landing['ld_category_list'] ?? ''; ?>" class="frm_input" size="80">
                    <label style="margin-left: .5rem;">
                        <input type="checkbox" name="ld_use_category" value="1" <?= ($landing['ld_use_category'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span>사용</span>
                    </label>
                    <span class="frm_info">예: 대기중|상담완료 (|로 구분)</span>
                </td>
            </tr>
            <tr>
                <th scope="row">검색 기능 사용</th>
                <td>
                    <label>
                        <input type="checkbox" name="ld_use_search" id="ld_use_search" value="1" <?= ($landing['ld_use_search'] ?? '0') == '1' ? 'checked' : ''; ?>>
                        <span>사용</span>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ld_subject">제목</label></th>
                <td><input type="text" name="ld_subject" id="ld_subject" value="<?php echo $landing['ld_subject'] ?? ''; ?>" required class="frm_input" size="80"></td>
            </tr>
            <tr>
                <th scope="row"><label for="ld_content">이미지 첨부</label></th>
                <td><?= file_upload_html('landing', 'bf_file[]', 'file_input') ?></td>
            </tr>
            <tr>
                <th scope="row"><label for="ld_fields">폼 필드명 정의</label></th>
                <td>
                    <input type="text" name="ld_fields" id="ld_fields" value="<?php echo $landing['ld_fields'] ?? ''; ?>" class="frm_input" size="80">
                    <span class="frm_info">예: 이름|연락처|이메일 (|로 구분)</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ld_sort_field">목록 정렬 정의</label></th>
                <td>
                    <input type="text" name="ld_sort_field" id="ld_sort_field" value="<?= $landing['ld_sort_field'] ?? ''; ?>" class="frm_input" size="80">
                    <span class="frm_info">예: ld_id desc(기본: 최신글이 맨 위로)</span>
                </td>
            </tr>
            <tr>
                <th scope="row">사용 여부</th>
                <td>
                    <label>
                        <input type="checkbox" name="ld_use" value="1" <?= ($landing['ld_use'] ?? '1') === '1' ? 'checked' : ''; ?>>
                        <span>사용</span>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_fixed_top">
        <a href="./landing_log_list.php?ld_page=<?= $landing['ld_page']?>" class="btn btn_02">내용관리</a>
        <a href="./landing_list.php" class="btn btn_03">목록</a>
        <input type="submit" value="저장" class="btn_submit btn">
    </div>
</form>

<script>
    var $target = $("input[name='chk_ld_page']");
    function on_submit(f){
        if(Number($target.val()) === 0){
            alert('랜딩 아이디 중복확인이 필요합니다.');
            return false;
        }

        return true;
    }

    $(document).ready(function(){
        //파일 업로드 썸네일 보기
        <?php echo get_file_upload_js('file_input'); ?>

        $(document).on('click', '#btn_id_check', function(){
            const ld_page = $('#ld_page').val();

            if(!ld_page) return alert('랜딩 ID값이 누락되었습니다.'), false;

            $.post('landing_form_check.php', {ld_page}, function(data){
                if(data.state === 'success'){
                    alert(data.msg);
                    $target.val(1);
                    return false;
                }else{
                    alert(data.msg);
                    $target.val(0);
                    return false;
                }
            }, 'json');
        });
    });

</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');


