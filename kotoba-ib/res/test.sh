echo "DirectoryIndex index.php
RewriteEngine On
RewriteRule ^([\w]{1,16})/$ $2/boards.php?board=\$1 [NE,L]
RewriteRule ^([\w]{1,16})$ $2/boards.php?board=\$1 [NE,L]
RewriteRule ^([\w]{1,16})/([\d]+)/$ $2/threads.php?board=\$1&thread=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/([\d]+)$ $2/threads.php?board=\$1&thread=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/p([\d]+)/$ $2/boards.php?board=\$1&page=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/p([\d]+)$ $2/boards.php?board=\$1&page=\$2 [NE,L]" > $1/.htaccess