<?php

$apiKey = ''; // YOURE API KEY HERE.

$cmd = (isset($_POST['cmd'])) ? htmlspecialchars($_POST['cmd']) : null;
$targetValue = (isset($_POST['ust_targetValue'])) ? htmlspecialchars($_POST['ust_targetValue']) : null;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Ustream Search</title>

<style type="text/css">
* {
    padding: 0;
    margin: 0;
}

#header {
    background: #eee;
    height: 60px;
    padding: 10px;
}

.ust_form {
    width: 60%;
    margin: 15px;
    padding: 3px;
    border: 1px solid #999;
}
.ust_form fieldset {
    border: 0;
    margin: 3px;
}
.ust_form span {
    font-weight: bold;
}
fieldset.submit {
    text-align: center;
}
.result {
    overflow: scroll;
    height: 480px;
    background: #000;
    color: #fff;
}
div.error {
    text-align:center;
}
div.error_msg {
    width: 600px;
    background:lime;
    margin: 50px auto;
    padding: 30px;
}
div.error_msg p {
    font-weight: bold;
}
</style>
</head>
<body>
<div id="header">
<h1><a href="search.php">Ustream Search Sample</a></h1>
<p><a href="http://developer.ustream.tv/docs">@see Ustream API Documentation</a></p>
</div>
<?php if (!$apiKey) : ?><div style="background:red;margin:30px;padding: 30px;text-align:center;">Get Your API Key : <a href="http://developer.ustream.tv/member/register">http://developer.ustream.tv/member/register</a></div><?php exit; endif; ?>
<div class="ust_form">
<form action="search.php" method="post">
<input type="hidden" name="cmd" value="search" />

<fieldset><span>Command : </span>
<select name="ust_command">
<option value="user">User</option>
<option value="channel">Channel</option>
<option value="stream">Stream</option>
<option value="video">Video</option>
</select>
</fieldset>

<fieldset><span>Scope / Sorting : </span>
<input type="radio" name="ust_opt" value="all" checked />All
<input type="radio" name="ust_opt" value="newest" />Newest
<input type="radio" name="ust_opt" value="!newest" />!Newest
<input type="radio" name="ust_opt" value="recent" />Recent
<input type="radio" name="ust_opt" value="!recent" />!Recent
<input type="radio" name="ust_opt" value="popular" />Popular
<input type="radio" name="ust_opt" value="live" />Live
</fieldset>

<fieldset><span>Target : </span>
<select name="ust_target">
<option value="username" selected>Username [string] (Stream, User, Channel)</option>
<option value="title">Title [string] (Stream, Channel, Video)</option>
<option value="description">Description [string] (Stream)</option>
<option value="starttime">StartTime [timestamp] (Stream)1</option>
<option value="rating">Rating [integer] (Stream, User, Channel, Video)</option>
<option value="views">Views [integer] (Channel, Video)</option>
<option value="created">Created [date] (Channel, Video)</option>
<option value="registerdate">Register Date [date] (User)</option>
<option value="length">Length [integer] (Video)</option>
</select>
* TargetName [datatype] (API Command[s])
</fieldset>

<fieldset><span>Target Property : </span>
<input type="radio" name="ust_targetProperty" value="like" checked />Like
<input type="radio" name="ust_targetProperty" value="eq" />eq
<input type="radio" name="ust_targetProperty" value="lt" />lt
<input type="radio" name="ust_targetProperty" value="gt" />gt
</fieldset>

<fieldset><span>Target Value : </span>
<input type="text" name="ust_targetValue" value="<?php echo $targetValue ?>" />
</fieldset>
<fieldset>
<span>Page (integer) : </span>
<input type="text" name="page" size="2" value="1" maxlength="3" />
<span>Result limit : </span>
<select name="limit"><?php for ($i = 1; $i <= 20; $i++) : ?><option value="<?php echo $i ?>"<?php if ($i == 20) : ?> selected<?php endif; ?>><?php echo $i; ?></option><?php endfor; ?></select>
</select>
</fieldset>
<fieldset class="submit">
<input type="submit" value="    Search    " />
</fieldset>
</form>
</div>
<?php


if ($cmd == 'search') {
    $command = (isset($_POST['ust_command'])) ? htmlspecialchars($_POST['ust_command']) : null;
    $opt = (isset($_POST['ust_opt'])) ? htmlspecialchars($_POST['ust_opt']) : 'all';
    $target = (isset($_POST['ust_target'])) ? htmlspecialchars($_POST['ust_target']) : 'username';
    $targetProperty = (isset($_POST['ust_targetProperty'])) ? htmlspecialchars($_POST['ust_targetProperty']) : 'like';
    $page = (isset($_POST['page'])) ? (int) htmlspecialchars($_POST['page']) : 1;
    $limit = (isset($_POST['limit'])) ? (int) htmlspecialchars($_POST['limit']) : 20;
    
    //var_dump($_POST);
    try {
        if ($command == null) {
            throw new Exception('empty: Command.');
        }
        if ($targetValue == null) {
            throw new Exception('empty: TargeValue');
        }
        require_once 'Services/Ustream.php';
        $targetPropertyFunc = $targetProperty;
        $ust = Services_Ustream::factory('search', $apiKey);
        $result = $ust->command($command)->scope($opt)->where($target)
                        ->setPage($page)->setLimit($limit)
                        ->$targetPropertyFunc($targetValue)->query();



?>
<div class="result">
<pre>
<?php print_r($result); ?>
</pre>
</div>
<?php
    } catch (Exception $e) {
?>

<div class="error">
<div class="error_msg"><p>error</p><?php echo $e->getMessage() ?></div>
</div>

<?php
    }
} else {

?>

<?php
}
?>
</body>
</html>


