<?php
require_once("./phpQuery-onefile.php");
$html = file_get_contents("https://www.ei-navi.jp/dictionary/content/dog/");
echo phpQuery::newDocument($html)->find(".en")->text();
?>
