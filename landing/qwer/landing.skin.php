<?php
include_once(G5_PATH.'/head.sub.php');

$sql = "SELECT * FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ";
$get_data = sql_fetch($sql);
list($ldg_cate,) = explode('|', $get_data['ld_category_list']);

//에디터 삽입 이미지
foreach(get_editor_image($get_data['ld_content'], 0)[1] as $i => $img) {
    $img_src[$i] = $img;
}

/** $arr_field[0], $arr_field[1] 입력 필드값 직접 사용하기 **/

/** $arr_field[0], $arr_field[1] 입력 필드값 자동으로 생성하기 **/
$arr_field = explode('|', $get_data['ld_fields']);

?>

    <div class="landing-container">

        <!-- 상단 배너 -->
        <div class="landing-banner">
            <?php /** $img_src[0] 1번째 이미지, $img_src[1] 두번째 이미지 **/ ?>
            <img src="<?= $img_src[0] ?>" alt="이벤트 배너" class="banner-img">
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

                <?php if($arr_field[0]): ?>
                    <label>
                        <span><?= $arr_field[0] ?></span>
                        <input type="text" name="ldg_field_1" value="">
                    </label>
                <?php endif; ?>

                <?php if($arr_field[1]): ?>
                    <label>
                        <span><?= $arr_field[1] ?></span>
                        <input type="text" name="ldg_field_2" value="">
                    </label>
                <?php endif; ?>

                <?php if($arr_field[2]): ?>
                    <label>
                        <span><?= $arr_field[2] ?></span>
                        <input type="text" name="ldg_field_3" value="">
                    </label>
                <?php endif; ?>

                <?php if($arr_field[3]): ?>
                    <label>
                        <span><?= $arr_field[3] ?></span>
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
