-- --------------------------------------------------------
-- Table structure for `g5_landing`
-- 생성일: 2025-09-22
-- --------------------------------------------------------

DROP TABLE IF EXISTS `g5_landing`;

CREATE TABLE `g5_landing` (
  `ld_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '랜딩 고유번호',
  `ld_page` VARCHAR(50) NOT NULL COMMENT 'URL id (?id_page=)' COLLATE 'utf8mb4_general_ci',
  `ld_subject` VARCHAR(255) NOT NULL COMMENT '랜딩 제목' COLLATE 'utf8mb4_general_ci',
  `ld_content` TEXT NULL DEFAULT NULL COMMENT '랜딩 내용(이미지)' COLLATE 'utf8mb4_general_ci',
  `ld_fields` TEXT NOT NULL COMMENT '폼 필드 정의 (예: 이름|나이|연락처|이메일)' COLLATE 'utf8mb4_general_ci',
  `ld_category_list` TEXT NOT NULL COMMENT '카테고리 (예: 대기중|상담완료)' COLLATE 'utf8mb4_general_ci',
  `ld_use_category` INT(10) NULL DEFAULT '0' COMMENT '카테고리 사용',
  `ld_files` TEXT NULL DEFAULT NULL COMMENT '에디터에 저장되는 파일 이름' COLLATE 'utf8mb4_general_ci',
  `ld_access_id` TEXT NULL DEFAULT NULL COMMENT 'lid별 접근 아이디' COLLATE 'utf8mb4_general_ci',
  `ld_sort_field` TEXT NULL DEFAULT NULL COMMENT '정렬 (예 ld_id asc)' COLLATE 'utf8mb4_general_ci',
  `ld_use_search` INT(10) NULL DEFAULT '0',
  `ld_use` INT(10) NULL DEFAULT '1' COMMENT '사용 여부',
  `ld_use_search` INT(10) NULL DEFAULT '0' COMMENT '검색 사용',
  `ld_title` VARCHAR(255) NULL DEFAULT NULL COMMENT '어드민 메뉴 타이틀 (기본값: 랜딩페이지)' COLLATE 'utf8mb4_general_ci',
  `ld_datetime` DATETIME NULL DEFAULT current_timestamp() COMMENT '저장날짜',
  PRIMARY KEY (`ld_id`) USING BTREE
)
ENGINE=InnoDB
AUTO_INCREMENT=70
DEFAULT CHARSET=utf8mb4
COLLATE='utf8mb4_general_ci';
