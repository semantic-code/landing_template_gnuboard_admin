<?php
include_once('../common.php');

$ld_page = isset($_GET['ld_page']) && $_GET['ld_page'] !== '' ? $_GET['ld_page'] : null;
$row = sql_fetch("SELECT ld_use FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ");
$is_use = $row['ld_use'];

?>

<?php ob_start() ;?>
<div class="landing-wrap">
    <h1>Default Landing Page</h1>
    <?php if ($ld_page == null) :?>
    <p>기본 래딩페이지 입니다. 랜딩 ID를 지정하지 않으셨습니다.</p>
    <?php elseif ($is_use == 0) :?>
    <p>랜딩페이지가 '사용안함'으로 되어 있습니다.</p>
    <?php endif; ?>
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
if ($ld_page == null || $is_use == 0) die($empty_ld_page);

// 아이디가 있는 경우만 스킨 경로 생성
$skin_path = __DIR__ . "/{$ld_page}";
$skin_url  = G5_URL . "/landing/{$ld_page}";

if (!is_file("{$skin_path}/landing.skin.php")) die($empty_file);

// CSS 적용
add_stylesheet('<link rel="stylesheet" href="'.$skin_url.'/style.css">', 0);

// 출력
include $skin_path.'/landing.skin.php';

<?php
include_once('../common.php');

$ld_page = isset($_GET['ld_page']) && $_GET['ld_page'] !== '' ? $_GET['ld_page'] : null;
$row = sql_fetch("SELECT ld_use FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ");
$is_use = $row['ld_use'];

?>

<?php ob_start() ;?>
<div class="landing-wrap">
    <h1>Default Landing Page</h1>
    <?php if ($ld_page == null) :?>
    <p>기본 래딩페이지 입니다.</p> 
    <p>랜딩 ID를 지정하지 않으셨습니다.</p>
    <?php elseif ($is_use == 0) :?>
    <p>랜딩페이지가 '사용안함'으로 되어 있습니다.</p>
    <?php endif; ?>
</div>
<?php $empty_ld_page = ob_get_clean() ;?>

<?php ob_start() ;?>
    <div class="landing-wrap">
        <h1>Default Landing Page</h1>
        <p>기본 랜딩페이지입니다.</p>
        <p>랜딩페이지의 파일이 없습니다.</p>
        <?php if($is_admin === 'super'):?>
            <p>랜딩페이지 스킨을 생성하지 않은 경우, 스킨 생성을 먼저 하십시오.</p>
        <?php endif; ?>
    </div>
<?php $empty_file = ob_get_clean() ;?>

<?php
if ($ld_page == null || $is_use == 0) die($empty_ld_page);

// 아이디가 있는 경우만 스킨 경로 생성
$skin_path = __DIR__ . "/{$ld_page}";
$skin_url  = G5_URL . "/landing/{$ld_page}";

if (!is_file("{$skin_path}/landing.skin.php")) die($empty_file);

// CSS 적용
add_stylesheet('<link rel="stylesheet" href="'.$skin_url.'/style.css">', 0);

// 출력
include $skin_path.'/landing.skin.php';
