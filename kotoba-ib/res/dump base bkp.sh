#!/bin/sh
mysqldump --routines -u root -r "kotoba bkp.sql" -e --databases kotoba
