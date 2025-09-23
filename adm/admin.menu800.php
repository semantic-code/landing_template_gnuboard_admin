<?php
$sql = "SELECT * FROM {$g5['landing']} ORDER BY ld_id ASC";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)){
    $get_data[] = $row;
}

print_r($get_data);

if ($member['mb_id'] === 'admin') {
    $menu['menu800'] = array (
        array('800000', '랜딩페이지', G5_ADMIN_URL.'/landing_list.php', 'board'),
        array('800100', '랜딩페이지 설정', G5_ADMIN_URL.'/landing_list.php', 'board'),
    );

    foreach ($get_data as $k => $v){
        $sub_code = ($ld_page === $v['ld_page']) ? "800200" : "";
        $menu['menu800'][] = array($sub_code, $v['ld_subject'], G5_ADMIN_URL.'/landing_log_list.php?ld_page='.$v['ld_page'], 'board');
    }

}else{
    $ld_title = array(
        "master" => "빠른상담"
    );
    $menu['menu800'] = array();
    $menu['menu800'][] = array('800000', $ld_title[$member['mb_id']] ?: '랜딩페이지',  '', 'board');
    $my_first_page = false;

    foreach ($get_data as $k => $v){
        // 접근 권한 없으면 건너뛰기
        if (!in_array($member['mb_id'], explode('|', $v['ld_access_id']), true)) continue;

        if(!$my_first_page){
            $menu['menu800'][0][2] = G5_ADMIN_URL.'/landing_log_list.php?ld_page='.$v['ld_page'];
            $my_first_page = true;
        }

        $sub_code = ($ld_page === $v['ld_page']) ? "800200" : "";
        $menu['menu800'][] = array($sub_code, $v['ld_subject'], G5_ADMIN_URL.'/landing_log_list.php?ld_page='.$v['ld_page'], 'board');
    }
}
