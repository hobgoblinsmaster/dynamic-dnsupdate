<html>
	<head>
		<title>Web-based Dynamic DNS Update
		</title>
		<meta name="author" content="김재연">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	</head>
	<body>
		<h1 style="font-family: Arial;">Web-based Dynamic DNS Update</h1>
		<style="font-family: Arial;">
		<span style="font-family: Arial;">This is a web-based dynamic DNS update program that can add, replace or delete DNS resource records in a master server.
		</span><br style="font-family: Arial;">
		<h3 style="font-family: Arial;">Add a resource record</h3>
		<form style="font-family: Arial;" method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
			<table border="1">
				<tbody>
					<tr><td> Nameserver</td> 
						<td width="370"> 
							<input name="nameserver" class="box" value="ns1.cslab.net" disabled="disabled"></td>
					</tr>
					<tr><td> Domain</td> 
						<td width="370"> 
							<input name="domain" class="box" value="freeserver.kr"></td>
					</tr>
					<tr><td> Host</td>
						<td width="370"> 
							<input name="host" class="box" value="test1"></td>
					</tr>
					<tr><td> TTL</td> 
						<td width="370"> 
							<input name="ttl" value="60" checked="checked" type="radio">60 
							<input name="ttl" value="3600" type="radio">3600 
							<input name="ttl" value="86400" type="radio">86400</td>
					</tr>
					<tr><td> Type</td> 
						<td width="370"> 
							<input name="type" value="A" checked="checked" type="radio">A 
							<input name="type" value="CNAME" type="radio">CNAME 
							<input name="type" value="NS" type="radio">NS 
							<input name="type" value="MX" type="radio">MX 
							<input name="type" value="TXT" type="radio">TXT </td>
					</tr>
					<tr><td> Type Value&nbsp;</td><td> 
							<input name="type_value" class="box" value="192.168.0.1" type="text"></td>
					</tr>
					<tr> 
						<td colspan="2"> 
							<input name="update" style="font-weight: bold;" value="Add" type="submit"> 
							<input name="update" style="font-weight: bold;" value="Cancel" type="reset"></td>
					</tr>
				</tbody>
			</table>
		</form> 
<?php 
include 'Net/DNS.php';
$resolver = new Net_DNS_Resolver();
// debug output (0 : disalbe, 1 : enable)
$resolver->debug = 0;
$response = $resolver->axfr('freeserver.kr');
?>
<hr style="font-family: Arial;">
<h3 style="font-family: Arial;"> Delete a resource record (You can delete only one RR at a time.)</h3>
<form style="font-family: Arial;" method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
<pre>
<?php 
$i = 0;
if (count($response)) 
{
  foreach ($response as $rr) 
  {              	
  	$compare = $rr->string();
    $i = $i + 1;
    if ( strstr($compare, 'SOA') || strstr($compare, 'NS') || strstr($compare, '211.208.163.118') )
    {
			echo "<input name=resourceNumber value=$i type=checkbox disabled> ";
		}
		else
		{
			echo "<input name=resourceNumber value=$i type=checkbox> ";
		}		
		echo "$i.\t";
		
		echo $value;		
    $rr->display();
  }
}
?>
<input name="host" value="freeserver.kr" type="hidden"> <input name="update" style="font-weight: bold;" value="Delete" type="submit"> <input name="update" style="font-weight: bold;" value="Cancel" type="reset"><hr> Copyright (c) 2008-<?php ECHO date("Y"); ?> CSLab.net  All rights reserved.</pre>
		</form>
		<p>
<a href="http://code.google.com/p/dynamic-dnsupdate/">Project Page</a>
	</body>
</html>