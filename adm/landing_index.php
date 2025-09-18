<?php
//$sub_menu = "800200";
include_once('./_common.php');

$mb_id = $member['mb_id'];

$sql = "SELECT * FROM {$g5['landing']} ORDER BY ld_id ASC";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)){$landing_data[] = $row;}

$filtered = array_filter($landing_data, function($row) use ($mb_id){
    $ids = explode('|', $row['ld_access_id']);
    return in_array($mb_id, $ids, true);
});

$target_ld_page = $filtered[0]['ld_page'];

if ($target_ld_page) {
    goto_url(G5_ADMIN_URL.'/landing_log_list.php?ld_page='.$target_ld_page);
} else {
    alert('접근 가능한 페이지가 없습니다.');
}
