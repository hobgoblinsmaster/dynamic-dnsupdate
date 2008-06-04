<?php
//------------------------------------------------------------------------------
// Web-based Dynamic DNS Update
// @author: Jaeyoun Kim
// @homepage: http://code.google.com/p/dynamic-dnsupdate/
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
		<h1>Web-based Dynamic DNS Update</h1>
		<p>
		This is a web-based dynamic DNS update program that can add, replace or delete DNS resource records in a master server. 
		</p>
		<p>
		Your IP address is <?php echo $remote_ip; ?>
		</p>
		<form method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
		<select name="domain1">
			  <option value="<?php echo $mydomain1; ?>" selected><?php echo $mydomain1; ?></option>
			  <option value="<?php echo $mydomain2; ?>"><?php echo $mydomain2; ?></option>
		</select>
		<INPUT TYPE=SUBMIT VALUE="Go">
		</FORM>
		</p>
		<h3 style="font-family: Arial;">Add a resource record</h3>
		<form style="font-family: Arial;" method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
			<table border="1" cellpadding="3" cellspacing="0">
				<tbody>
					<tr><td> Domain</td> 
						<td width="370"> 
							<input name="domain" class="box" value="freeserver.kr">
							</td>
					</tr>
					<tr><td> Nameserver</td> 
						<td width="370"> 
							<input name="nameserver" class="box" value="ns1.cslab.net" disabled="disabled"></td>
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
				</tbody>
			</table>
			<br>
			<input name="update" style="font-weight: bold;" value="Add" type="submit"> 
			<input name="update" style="font-weight: bold;" value="Cancel" type="reset">
		</form>
<h3> Delete a resource record</h3>
<form method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
<table border="1" cellpadding="3" cellspacing="0">
<tr>
	<td>Check</td>
	<td width="200">Domain</td>
	<td width="50">TTL</td>
	<td width="50">Type</td>
	<td width="150">Address</td>
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
			echo "<input name=rrnumber value=$i type=checkbox>";
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
			echo $response[$i]->preference;
			echo "&nbsp;";
			echo $response[$i]->exchange;
			echo "</td>";
			echo "</tr>";
		}
		else if ( ($response[$i]->type) == "NS" )
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
<hr>Copyright (c) <?php ECHO date("Y"); ?> CSLab.net  All rights reserved.
<p>
</body>
</html>