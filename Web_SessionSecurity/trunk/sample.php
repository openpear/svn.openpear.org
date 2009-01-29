<?php
ini_set("include_path", dirname(__FILE__)."/src/" . PATH_SEPARATOR . ini_get("include_path"));
require_once "Web/SessionSecurity.php";

// Test
session_start();
$sess_security = Web_SessionSecurity::validate("hoge", "__foo__", 10, 600,  10);
switch($sess_security->getAlert()) {
case Web_SessionSecurity::DIFF_REMOTE_ADDR:
    echo "<font color=red>Security Alert!!  Remote Addr is different.</font>";
    break;
case Web_SessionSecurity::DIFF_SECURITY_CODE:
    echo "<font color=red>Security Alert!!  Security code is different.</font>";
    break;
}

if (!isset($_SESSION['count'])) {
    $_SESSION['count'] = 0;
}
$_SESSION['count']++;

function echo_cookie_val($key) {
    if (isset($_COOKIE[$key])) {
        echo $_COOKIE[$key];
    }
}

?>
<html>
<head>
<title>Web_SessionSecurity Test</title>
<script>
function init() {
    var ck = ""+document.cookie;
    document.getElementById("CK").value=ck.replace(/;/g,";\n");
}
function setCookie(key, cvalue) {
    var value = document.getElementById(cvalue).value;
    document.cookie= key+"="+value;
    init();
}
</script>
</head>
<body onload="init()">
<h1>Web_SessionSecurity Test</h1>
<div style="float:left;width:40%;">
<table border=1>
<h2>Operation</h2>
<ol>
<li>Open 2 browser window (A, B).</li>
<li>Push Reload A and B</li>
<li>A copy cookie(<?php echo session_name();?>, <?php echo session_name();?>_HASH) of B.</li>
<li>Push Reload A and B</li>
<li>After 20 seconds..</li>
<li>Push Reload A and B</li>
<li>show "Security Alert!!"</li>
</ol>
<h2>Copy Cookie</h2>
<tr><th>key</th><th>value</th></tr>
<tr>
<td><?php echo session_name();?></td><td><input id="cvalue1"></td>
<td colspan=2 align=center><button onclick="setCookie('<?php echo session_name();?>', 'cvalue1')">Save</button></td>
</tr>
<tr>
<td><?php echo session_name();?>_HASH</td><td><input id="cvalue2"></td>
<td colspan=2 align=center><button onclick="setCookie('<?php echo session_name();?>_HASH', 'cvalue2')">Save</button></td>
</tr>
</table>
<h2>Cookie</h2>
<textarea id="CK" style="width:100%;height:250px;"></textarea>
</div>
<div style="float:right;width:60%;">
<h2>Session Data</h2>
<button onclick="location.reload()">Reload</button>
<table border=1>
<tr>
    <td>Count</td>
    <td><?php echo $_SESSION['count'];?></td>
</tr>
<tr>
    <td><?php echo session_name();?></td>
    <td><?php echo session_id();?></td>
</tr>
<tr>
    <td><?php echo session_name();?>_HASH</td>
    <td><?php echo_cookie_val(session_name().'_HASH');?></td>
</tr>
<tr>
    <td>Last check time</td>
    <td><pre><?php echo $sess_security->getLastCheckedtime();?></pre></td></tr>
<tr>
    <td>Security code</td>
    <td><pre><?php echo $sess_security->getSecurityCode();?></pre></td>
</tr>
<tr>
    <td>Security code lifetime</td>
    <td><pre><?php echo $sess_security->getLifetime();?></pre></td>
</tr>
<tr>
    <td>Security alert</td>
    <td><pre><?php echo $sess_security->getAlert();?></pre></td>
</tr>
<tr>
    <td>Current time(diff)</td>
    <td><?php echo time() ?> (<?php echo time()-$sess_security->getLastCheckedtime() ?>)</td>
</tr>
</table>
</div>
</body>
</html>