<?php
include_once('./_common.php');

$bo_table = 'landing';

$sql = "SELECT * FROM {$g5['landing']} WHERE ld_page = '{$ld_page}' ";
$list = sql_fetch($sql);
$get_file = get_file('landing', $list['ld_id']);

$first_file = null;
foreach ($get_file as $key => $file) {
    if ($key === 'count') continue;
    $first_file = $file;
    break;
}
$list['file'] = $first_file;

?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>랜딩 미리보기</title>
    <style>
        body {margin:20px; font-family:sans-serif;}
        .content img {max-width:100%;}
    </style>
</head>
<body>
<?php if ($list): ?>
    <div class="content">
        <?php $src = G5_DATA_URL."/file/{$bo_table}/{$list['file']['file']}"; ?>
        <img src="<?= $src ?>" alt="<?= $list['file']['source']?>">
    </div>
<?php else: ?>
    <p>해당 랜딩 데이터가 없습니다.</p>
<?php endif; ?>
</body>
</html>
