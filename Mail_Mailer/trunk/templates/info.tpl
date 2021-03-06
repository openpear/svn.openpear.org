<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
	<style type="text/css">
	{literal}
		body {background-color: #ffffff; color: #000000;}
		body, td, th, h1, h2 {font-family: sans-serif;}
		pre {margin: 0px; font-family: monospace;}
		a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
		a:hover {text-decoration: underline;}
		table {border-collapse: collapse;}
		.center {text-align: center;}
		.center table { margin-left: auto; margin-right: auto; text-align: left;}
		.center th { text-align: center !important; }
		td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
		h1 {font-size: 150%;}
		h2 {font-size: 125%;}
		.p {text-align: left;}
		.e {background-color: #ccccff; font-weight: bold; color: #000000;}
		.h {background-color: #9999cc; font-weight: bold; color: #000000;}
		.v {background-color: #cccccc; color: #000000;}
		.vr {background-color: #cccccc; text-align: right; color: #000000;}
		img {float: right; border: 0px;}
		hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
	{/literal}
	</style>
	<title>makeinfo()</title>
	<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
</head>
<body>
	<div class="center">

	<table border="0" cellpadding="3" width="600">
	<tr class="h">
		<td>
			<h1 class="p">{$name}</h1>
		</td>
	</tr>
	</table>
	<br />

	<h2><a name="Function">{$title}</a></h2>
	<table border="0" cellpadding="3" width="600">
	{foreach from=$contents item=items key=keys}
		<tr>
			<td class="e">
				{$keys}
			</td>
			<td class="v">
				{$items}
			</td>
		</tr>
	{/foreach}
	</table>
	<br />

	</div>
</body>
</html>