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
