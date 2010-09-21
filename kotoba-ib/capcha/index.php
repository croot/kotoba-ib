<?php
session_start();
$_SESSION['security_number']=rand(10000,99999);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Kotoba capcha v0.4</title>
</head>

<body>
Эй, введи ка капчу<br><br>
Да ты, сука, бот!<br><br>
<img src="/~sorc/simple_capcha_image/image.php" alt="Kotoba capcha v0.4" />
</body>
</html>
<!--
v0.3 Random word. 1 random line.

v0.3 Character now is curves. And 3 random lines.

v0.2 Added 5 random lines.

v0.1 Just draw the secure code.
-->