<?php
// High-side backup script for Rosaic environment
$host = 'localhost';
$user = 'microcms_loguser';
$pass = 'LsXJNW9S#';
$name = 'microcms_rosaic';

$command = "mysqldump --opt -h $host -u $user -p$pass $name > backup_" . date('Y-m-d') . ".sql";

system($command);
echo "Database exported to backup folder.";
