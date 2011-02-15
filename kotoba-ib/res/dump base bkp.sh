#!/bin/sh
mysqldump --routines -u root -r "`date +%F\ %T` kotoba bkp.sql" -e --databases kotoba
