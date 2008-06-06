<?php
include './config.php';
?>
<html>
	<head>
		<title>Web-based Dynamic DNS Update
		</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
		<link rel="stylesheet" href="style.css">
	</head>
	<body> 
		<img src="./images/domain.jpg" border="0" alt="Image" height="100" width="300"><h1>Web-based Dynamic DNS Update Program</h1>
		<p class="version">Ver. 
			<?php echo $version; ?>
		</p>
		<table width="300" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
			<tr>
				<form name="login" method="post" action="dnsupdate.php"> <td>
						<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
							<tr> 
								<td colspan="3"><strong>Admin Login </strong></td>
							</tr>
							<tr> 
								<td width="78">Username</td> 
								<td width="6">:</td> 
								<td width="294">
									<input name="username" type="text" id="username" value="demo" disabled></td>
							</tr>
							<tr> <td>Password</td> <td>:</td> <td>
									<input name="password" type="text" id="password" value="demo" disabled></td>
							</tr>
							<tr> <td>&nbsp;</td> <td>&nbsp;</td><td>
									<input type="submit" name="Submit" value="Login"></td>
							</tr>
						</table></td>
				</form>
			</tr>
		</table>
		<p>
		</p>
		<hr>
		<p class="copyright">
			<?php echo $copyright; ?>
		</p> 
		<br> 
		<br> 
		<br>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-4194094-3");
pageTracker._initData();
pageTracker._trackPageview();
</script>
</body>
</html>
