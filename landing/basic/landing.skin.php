<?php
//<title>
$config['cf_title'] = '성내동 치과';

include_once(G5_PATH.'/head.sub.php');

$bo_table = 'landing';

$sql = "SELECT * FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ";
$get_data = sql_fetch($sql);

//이미지 파일 경로 $img_src_1, $img_src_2, $img_src_3
$idx = 1;
$file = get_file($bo_table, $get_data['ld_id']);
foreach ($file as $row) {
    if (!empty($row['file'])) {
        ${"img_src_".$idx} = G5_DATA_URL. "/file/".$bo_table."/".$row['file'];
        $idx++;
    }
}

//카테고리 설정 $ldg_cate
list($ldg_cate,) = explode('|', $get_data['ld_category_list']);
//입력필드명 설정 $ld_field_1, $ld_field_2, $ld_field_3...
$arr_field = explode('|', $get_data['ld_fields']);
$idx = 1;
foreach ($arr_field as $row) {
    if (!empty($row)) {
        ${"ld_field_".$idx} = $row;
        $idx++;
    }
}

/**
 * 입력필드명 : $ld_field_1, $ld_field_2, $ld_field_3, $ld_field_4... (예: 지역, 이름, 나이...)
 * 입력필드 name : ldg_field_1, ldg_field_2, ldg_field_3, ldg_field_4...
 *
 * 이미지 src : $img_src_1, $img_src_2, $img_src_3, $img_src_4...
 **/

?>

    <div class="landing-container">

        <!-- 상단 배너 -->
        <div class="landing-banner">
            <?php /** $img_src[0] 1번째 이미지, $img_src[1] 두번째 이미지 **/ ?>
            <img src="<?= $img_src_1 ?>" alt="이벤트 배너" class="banner-img">
            <img src="<?= $img_src_2 ?>" alt="이벤트 배너" class="banner-img">
        </div>

        <!-- 본문 설명 -->
        <div class="landing-content">
            <h1>이벤트 안내</h1>
            <p>여기에 랜딩 페이지 설명을 넣습니다.<br>
                캠페인 안내 문구나 혜택 정보를 간단히 넣어주세요.</p>
        </div>

        <!-- 하단 고정 입력폼 -->
        <div class="landing-form-wrap">
            <form id="landing_form" action="<?= $skin_url ?>/landing.skin_update.php" method="post" class="landing-form">
                <input type="hidden" name="ld_page" value="<?= $get_data['ld_page'] ?>">
                <input type="hidden" name="ldg_cate" value="<?= $ldg_cate ?? '대기중' ?>">

                <?php if($ld_field_1): ?>
                    <label>
                        <span><?= $ld_field_1 ?></span>
                        <input type="text" name="ldg_field_1" value="">
                    </label>
                <?php endif; ?>

                <?php if($ld_field_2): ?>
                    <label>
                        <span><?= $ld_field_2 ?></span>
                        <input type="text" name="ldg_field_2" value="">
                    </label>
                <?php endif; ?>

                <?php if($ld_field_3): ?>
                    <label>
                        <span><?= $ld_field_3 ?></span>
                        <input type="text" name="ldg_field_3" value="">
                    </label>
                <?php endif; ?>

                <?php if($ld_field_4): ?>
                    <label>
                        <span><?= $ld_field_4 ?></span>
                        <input type="text" name="ldg_field_4" value="">
                    </label>
                <?php endif; ?>

                <!--                --><?php //foreach ($arr_field as $i => $row): ?>
                <!--                    <label>-->
                <!--                        <span>--><?php //= $row ?: '입력필드' ?><!--</span>-->
                <!--                        <input type="text" name="ldg_field_--><?php //= $i + 1 ?><!--" required value="--><?php //= time() ?><!--">-->
                <!--                    </label>-->
                <!--                --><?php //endforeach ?>

                <button type="submit">신청하기</button>
            </form>
        </div>
    </div>

<?php
include_once(G5_PATH.'/tail.sub.php');


