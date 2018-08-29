<?php

//var_dump($_GET);
//var_dump($_SERVER);

$path = 'C:\\';
$folders = [];
$files = [];
if (isset($_GET['path'])) {
  $path = $_GET['path'];
}
$dir = new DirectoryIterator($path);
$content = '<h5>' . $dir->getPath() . '</h5>';
foreach ($dir as $item) {
  if ($dir->isDir()) {
    if ($dir->isDot()) {
      if ($item->getBasename() === '.') {
        continue;
      }
      array_push($folders,
        "<p><a href='{$_SERVER['PHP_SELF']}?path=" . $dir->getRealPath() . "'>--назад</a></p>");
    } else {
      array_push($folders,
        "<div><a href='{$_SERVER['PHP_SELF']}?path=" . $dir->getRealPath() . "'>{$item}</a></div>");
    }
  } else {
    array_push($files, "<div>{$item}" . ($item->isFile() ? ' - ' . $item->getSize() . ' байт' : '') . "</div>");
  }
}
$content .= implode('', $folders);
$content .= implode('', $files);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
<?= $content ?>
</body>
</html>