<?php
include_once('./_common.php');

$row = sql_fetch("SELECT ld_subject, ld_content FROM {$g5['landing']} WHERE ld_page = '{$ld_page}'");
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
<?php if ($row): ?>
    <div class="content">
        <?php $src = get_editor_image($row['ld_content'], false); ?>
        <img src="<?= $src[1][0] ?>" alt="">
    </div>
<?php else: ?>
    <p>해당 랜딩 데이터가 없습니다.</p>
<?php endif; ?>
</body>
</html>
