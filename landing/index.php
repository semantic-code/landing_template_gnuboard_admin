<?php
include_once('../common.php');

if ($ld_page !== null) {
    $row = sql_fetch("SELECT ld_use FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ");
    $is_use = $row['ld_use'] ?? null;
}

$state = null;

$skin_path = __DIR__ . "/{$ld_page}";
$skin_url  = G5_URL . "/landing/{$ld_page}";

if (!is_file("{$skin_path}/landing.skin.php")) {
    $state = 'no_file';
}

if ($ld_page == null) {
    $state = 'no_id';
} elseif ($is_use == 0) {
    $state = 'not_use';
}

if ($state !== null) {
    die(render_empty($state, $is_admin));
}

// CSS 적용
add_stylesheet('<link rel="stylesheet" href="'.$skin_url.'/style.css">', 0);

// 출력
include $skin_path.'/landing.skin.php';

function render_empty($state, $is_admin) {
    ob_start();?>
    <div class="landing-wrap">
        <h1>Default Landing Page</h1>
        <p>기본 랜딩페이지 입니다.</p>
        <?php if ($state == 'no_id'): ?>
        <p>랜딩페이지 ID를 지정하지 않으셨습니다.</p>
        <?php elseif ($state == 'not_use'): ?>
            <?php if ($is_admin === 'super'):?>
            <p>랜딩페이지가 '사용안함'으로 설정되어 있습니다.</p>
            <?php endif; ?>
        <?php elseif ($state == 'no_file'): ?>
        <p>랜딩페이지 파일이 없습니다.</p>
            <?php if ($is_admin === 'super'):?>
            <p>랜딩페이지 스킨을 생성하지 않은 경우, 먼저 스킨을 생성하십시오.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php return ob_get_clean();
}
