<?php
//------------------------------------------------------------------------------
// Web-based Dynamic DNS Update
// @author: Jaeyoun Kim
// @homepage: http://code.google.com/p/dynamic-dnsupdate/
// @version: 1.0
// @date: June 1, 2008
//------------------------------------------------------------------------------
include './config.php';

// http://www.php.net/manual/en/reserved.variables.php
$remote_ip=$_SERVER['REMOTE_ADDR'];

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
	<table border="0" cellpadding="10" cellspacing="10">
	<tr><td>
		<img src="http://toddand.com/blog/wp-content/uploads/2007/07/domain.jpg" border="0" alt="Image" height="100" width="300">
		<h1>Web-based Dynamic DNS Update Program <small>(Ver. <?php echo $version; ?>)</small></h1>
		<p>
		This is a web-based dynamic DNS update program that can add, replace or delete DNS resource records in a master server. 
		</p>
		<p>
		* Project Home: <a href="http://code.google.com/p/dynamic-dnsupdate/">http://code.google.com/p/dynamic-dnsupdate/</a>
		</p>
		<p>
		* Your IP address is <b><?php echo $remote_ip; ?></b>.
		</p>
		<p>
		* <u><b>To Do List</b></u>: Domain Navigation Dropdrop, <a href=http://www.freeserver.kr/login.php>Admin Login</a>, Support for TXT RR, TSIG, Key Generator, Multiple RRs Delete
		</p>
		<hr>
		<form method="get" action="./dnsupdate.php" name="DynamicDNSUpdate">
		Domain: <select name="domain1" disabled>
			  <option value="<?php echo $mydomain1; ?>" selected><?php echo $mydomain1; ?></option>
			  <option value="<?php echo $mydomain2; ?>"><?php echo $mydomain2; ?></option>
		</select>
		<INPUT name="" TYPE=SUBMIT VALUE="Go" disabled>
		<INPUT name="key" TYPE=SUBMIT VALUE="Generate a Security Key" disabled>
		</FORM>
		<hr>
		<h3 style="font-family: Arial;"><img src="http://hostingdocs.fast.net/images/btn_dns_bg.gif" alt="Image" align="bottom" width="20"> Add a resource record</h3>
		<form style="font-family: Arial;" method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
			<table border="1" cellpadding="3" cellspacing="0">
				<tbody>
					<tr>
						<td width="100"> Domain</td> 
						<td width="400"> 
							<input name="domain" class="box" value="freeserver.kr" disabled style='background-color: #FFFFFF;'>
							</td>
					</tr>
					<tr><td> Nameserver</td> 
						<td width="370"> 
							<input name="nameserver" class="box" value="ns1.cslab.net" disabled="disabled"  style='background-color: #FFFFFF;'></td>
					</tr>
					<tr><td> Host</td>
						<td width="370"> 
							<input name="host" class="box" value="test1"></td>
					</tr>
					<tr><td> TTL</td> 
						<td width="370"> 
							<input name="ttl" value="60" checked="checked" type="radio">60
							<input name="ttl" value="3600" type="radio">3600 (1 Hour)
							<input name="ttl" value="86400" type="radio">86400 (1 Day)
							<input name="ttl" value="604800" type="radio">604800 (1 Week)
							</td>
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
				</tbody>
			</table>
			<br>
			<input name="domain" style="font-weight: bold;" value="freeserver.kr" type="hidden"> 
			<input name="update" style="font-weight: bold;" value="Add" type="submit"> 
			<input name="update" style="font-weight: bold;" value="Cancel" type="reset">
		</form>
<h3><img src="http://hostingdocs.fast.net/images/btn_dns_bg.gif" alt="Image" align="bottom" width="20"> Delete a resource record</h3>
<form method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
<table border="1" cellpadding="3" cellspacing="0">
<tr>
	<td>Check</td>
	<td width="200">Domain</td>
	<td width="120">TTL (Time-To-Live)</td>
	<td width="80">Type</td>
	<td width="250">Address</td>
</tr>
<?php 
include 'Net/DNS.php';

$resolver = new Net_DNS_Resolver();
// debug output (0 : disalbe, 1 : enable)
$resolver->debug = 0;
$response = $resolver->axfr('freeserver.kr');

$i = 0;
if (count($response))
{
  foreach ($response as $rr) 
  {              	
		if ( ($response[$i]->type) == "A" )
		{
			echo "<tr>";
			echo "<td style='background-color: rgb(255, 255, 204);'>";
			if ($response[$i]->address == "211.208.163.118")
			{
			echo "<input name=rrnumber value=$i type=checkbox disabled>";
			}
			else
			{
			echo "<input name=rrnumber value=$i type=checkbox>";
			}
			echo "</td>";
			echo "<td style='background-color: rgb(255, 255, 204);'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: rgb(255, 255, 204);'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: rgb(255, 255, 204);'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: rgb(255, 255, 204);'>";
			echo $response[$i]->address;
			echo "</td>";
			echo "</tr>";
			
		}
		else if ( ($response[$i]->type) == "MX" )
		{
			echo "<tr>";
			echo "<td style='background-color: rgb(255, 204, 153);'>";
			echo "<input name=rrnumber value=$i type=checkbox>";
			echo "</td>";
			echo "<td style='background-color: rgb(255, 204, 153);'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: rgb(255, 204, 153);'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: rgb(255, 204, 153);'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: rgb(255, 204, 153);'>";
			echo "Preference: <b>" . $response[$i]->preference . "</b>";
			echo "<br>";
			echo "Exchanger: <b>" . $response[$i]->exchange . "</b>";
			echo "</td>";
			echo "</tr>";
		}
		else if ( ($response[$i]->type) == "NS" )
		{
			echo "<tr>";
			echo "<td style='background-color: #CCFFFF;'>";
			if ($response[$i]->nsdname == "ns1.cslab.net")
			{
			echo "<input name=rrnumber value=$i type=checkbox disabled>";
			}
			else
			{
			echo "<input name=rrnumber value=$i type=checkbox>";
			}
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo "<b>" . $response[$i]->nsdname . "</b>";
			echo "</td>";
			echo "</tr>";
		}
		else if ( ($response[$i]->type) == "CNAME" )
		{
			echo "<tr>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo "<input name=rrnumber value=$i type=checkbox>";
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->cname;
			echo "</td>";
			echo "</tr>";
		}
		else if ( ($response[$i]->type) == "SOA" )
		{
			echo "<tr>";
			echo "<td style='background-color: #99FF99;'>";
			echo "<input name=rrnumber value=$i type=hidden disabled>N/A";
			echo "</td>";
			echo "<td style='background-color: #99FF99;'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: #99FF99;'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: #99FF99;'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: #99FF99;'>";
			echo "Serial: <b>" . $response[$i]->serial . "</b>";
			echo "<br>";
			echo "Refresh: <b>" . $response[$i]->refresh . "</b>";
			echo "<br>";
			echo "Retry: <b>" . $response[$i]->retry . "</b>";
			echo "<br>";
			echo "Expire: <b>" . $response[$i]->expire . "</b>";
			echo "<br>";
			echo "Minimum: <b>" . $response[$i]->minimum . "</b>";
			echo "</td>";
			echo "</tr>";
		}
		else if ( ($response[$i]->type) == "TXT" )
		{
			echo "<tr>";
			echo "<td style='background-color: #99FF99;'>";
			echo "<input name=rrnumber value=$i type=checkbox>";
			echo "</td>";
			echo "<td style='background-color: #99FF99;'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: #99FF99;'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: #99FF99;'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: #99FF99;'>";
			echo $response[$i]->text[0];
			echo $response[$i]->text[1];
			echo $response[$i]->text[2];
			echo "</td>";
			echo "</tr>";
		}
		else
		{
			echo "<tr>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo "<input name=rrnumber value=$i type=checkbox>";
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo $response[$i]->nsdname;
			echo "</td>";
			echo "</tr>";
		}
    $i = $i + 1;
  }
}
?>
</table>
<br>
<input name="host" style="font-weight: bold;" value="<?php echo $response[1]->name; ?>" type="hidden">
<input name="update" style="font-weight: bold;" value="Delete" type="submit">
<input name="update" style="font-weight: bold;" value="Cancel" type="reset">
</form>
<pre>
<?php
//print_r($response);
?>
</pre>
<hr>
<p>
<?php echo $copyright; ?>
</p>
</td></tr>
</table>
</body>
</html>