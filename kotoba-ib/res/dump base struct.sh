#/bin/sh
mysqldump -d --routines -u root -r "kotoba struct.sql" -e --databases kotoba2
