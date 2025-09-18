<?php
include_once('../common.php');
?>

<?php ob_start() ;?>
<div class="landing-wrap">
    <h1>Default Landing Page</h1>
    <p>이 페이지는 랜딩 ID를 지정하지 않았을 때 보여지는 기본 랜딩페이지입니다.</p>
</div>
<?php $empty_ld_page = ob_get_clean() ;?>

<?php ob_start() ;?>
    <div class="landing-wrap">
        <h1>Default Landing Page</h1>
        <p>이 페이지는 랜딩 페이지 파일이 없을 때 보여지는 기본 랜딩페이지입니다.</p>
        <?php if($is_admin === 'super'):?>
        <p>랜딩페이지 스킨을 생성하지 않은 경우, 스킨 생성을 먼저 하십시오.</p>
        <?php endif; ?>
    </div>
<?php $empty_file = ob_get_clean() ;?>

<?php
$ld_page = isset($_GET['ld_page']) && $_GET['ld_page'] !== '' ? $_GET['ld_page'] : null;

if ($ld_page === null) die($empty_ld_page);

// 아이디가 있는 경우만 스킨 경로 생성
$skin_path = __DIR__ . "/{$ld_page}";
$skin_url  = G5_URL . "/landing/{$ld_page}";

if (!is_file("{$skin_path}/landing.skin.php")) die($empty_file);

// CSS 적용
add_stylesheet('<link rel="stylesheet" href="'.$skin_url.'/style.css">', 0);

// 출력
include $skin_path.'/landing.skin.php';
