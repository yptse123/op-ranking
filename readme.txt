SQL file: admin/sql

Update DB config: admin/PM/classes/Data/Setting/Database.class.php

Include docker compose this time, 
simply run 
```
docker compose -f 'docker-compose.yml' up -d --build
```

can build the image and do db migration, please aware of the migration is clearing
all data in mysql, don't use in production.