<?php

define('API_KEY', 'xxxxxxxxxxxxxxxx');

header('Content-Type: text/html; charset=UTF-8');

require_once 'Services/Recruit.php';

foreach (array('searchType', 'keyword') as $name)
{
    $html_name = 'html_'.$name;

    $$name = array_key_exists($name, $_GET) == true? $_GET[$name]: '';
    $$html_name = htmlspecialchars($$name, ENT_QUOTES);
}

echo <<<EOD
<html>
<head>
  <title></title>
<style type="text/css">
<!--
h1
{
    clear: both;
}

ul#searchType
{
    margin-left: 0px;
    padding-left: 0px;
}

ul#searchType li
{
    float: left;
    list-style: none;
    margin-left: 0px;
    margin-right: 1em;
    padding-left: 0px;
}

br.clear
{
    clear: both;
}

-->
</style>
</head>
<body>
<h1>Search:</h1>
<div>
  <form action="index.php" method="GET">
    <dl>
      <dt>検索対象:</dt>
      <dd>
        <ul id="searchType">
          <li><input type="radio" id="groumet" name="searchType" value="gourmet" /><label for="groumet">グルメ</label></li>
          <li><input type="radio" id="shop" name="searchType" value="shop" /><label for="shop">店名</label></li>
          <li><input type="radio" id="genre" name="searchType" value="genre" /><label for="genre">ジャンル</label></li>
          <li><input type="radio" id="food" name="searchType" value="food" /><label for="food">料理名</label></li>
        </ul>
        <br class="clear" />
      </dd>
      <dt>キーワード:</dt>
      <dd>
        <input type="text" name="keyword" value="$html_keyword" />
      </dd>
      <dt>実行:</dt>
      <dd><input type="submit" value="この内容で検索する" />
  </form>
</div>

<h1>Result:</h1>
<fieldset>
  <legend>参照結果</legend>
  <div style="height: 200px; overflow: scroll; font-size: 70%;">
EOD;

if ($keyword != '')
{
    $obj = Services_Recruit::factory(API_KEY, 'hotpepper');

    switch ($searchType) {
    case 'gourmet':
        $result = $obj->searchGourmet(array('keyword'=>$keyword), 1, 3);
        break;

    case 'shop':
        $result = $obj->searchShop(array('keyword'=>$keyword), 1, 3);
        break;

    case 'genre':
        $result = $obj->searchGenre(array('keyword'=>$keyword), 1, 3);
        break;

    case 'food':
        $result = $obj->searchFood(array('keyword'=>$keyword), 1, 3);
        break;

    default:
        $result = 'エラー';
    }
    echo '<pre>';
    var_dump($result);
    echo '<hr>';
    var_dump(get_class_methods($obj));
    echo '</pre>';
}
?>
  </div>
</fieldset>
</body>
</html>
