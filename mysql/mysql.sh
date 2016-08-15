#!/bin/sh -x
mysql -u root -praspberry<01_db.sql
mysql -u root -praspberry 02_user.sql
mysql -u root -praspberry 03_grant.sql
mysql -u root -praspberry 04_table.sql
