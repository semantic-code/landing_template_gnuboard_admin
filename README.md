- /data/dbconfig.php

```php
//추가
$g5['landing'] = G5_TABLE_PREFIX.'landing';
$g5['landing_log'] = G5_TABLE_PREFIX.'landing_log';
```

- /adm/index.php
```php
if($member['mb_id'] !== 'admin') goto_url('./landing_index.php')
```
- /adm/landing_form.php
```php
<?= file_upload_html('notice', $list['file'] ?? array()) ?>
```
- /adm/landing_form_update.php
```javascript
<script>
    <?= get_file_upload_js() ?>
</script>
```



- g5_landing_log.sql
```sql
-- --------------------------------------------------------
-- Table structure for `g5_landing_log`
-- 생성일: 2025-09-22
-- --------------------------------------------------------

DROP TABLE IF EXISTS `g5_landing_log`;

CREATE TABLE `g5_landing_log` (
  `ldg_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '로그 고유번호',
  `ld_page` VARCHAR(100) NULL DEFAULT NULL COMMENT '참조 랜딩 ID' COLLATE 'utf8mb4_general_ci',
  `ldg_cate` VARCHAR(255) NULL DEFAULT NULL COMMENT '카테고리' COLLATE 'utf8mb4_general_ci',
  `ldg_subject` VARCHAR(255) NULL DEFAULT NULL COMMENT '제출 제목' COLLATE 'utf8mb4_general_ci',
  `ldg_content` TEXT NULL DEFAULT NULL COMMENT '상담내용' COLLATE 'utf8mb4_general_ci',
  `ldg_field_1` VARCHAR(255) NULL DEFAULT NULL COMMENT '필드1 값' COLLATE 'utf8mb4_general_ci',
  `ldg_field_2` VARCHAR(255) NULL DEFAULT NULL COMMENT '필드2 값' COLLATE 'utf8mb4_general_ci',
  `ldg_field_3` VARCHAR(255) NULL DEFAULT NULL COMMENT '필드3 값' COLLATE 'utf8mb4_general_ci',
  `ldg_field_4` VARCHAR(255) NULL DEFAULT NULL COMMENT '필드4 값' COLLATE 'utf8mb4_general_ci',
  `ldg_field_5` VARCHAR(255) NULL DEFAULT NULL COMMENT '필드5 값' COLLATE 'utf8mb4_general_ci',
  `ldg_memo` TEXT NULL DEFAULT NULL COMMENT '관리자 간단메모' COLLATE 'utf8mb4_general_ci',
  `ldg_ip` VARCHAR(45) NULL DEFAULT NULL COMMENT '작성자 IP' COLLATE 'utf8mb4_general_ci',
  `ldg_datetime` DATETIME NULL DEFAULT current_timestamp() COMMENT '저장날짜',
  PRIMARY KEY (`ldg_id`) USING BTREE,
  INDEX `idx_ld_page` (`ld_page`) USING BTREE
)
ENGINE=InnoDB
AUTO_INCREMENT=13
DEFAULT CHARSET=utf8mb4
COLLATE='utf8mb4_general_ci';
```
