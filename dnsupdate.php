<html>
	<head>
		<title>Web-based Dynamic DNS Update
		</title>
		<META name="author" content="Jaeyoun Kim">
		<META http-equiv="Content-Type" content="text/html; charset=utf-8">
		<META http-equiv="Cache-Control" content="no-cache, must-revalidate">
	</head>
	<body> 
		<FONT face="Arial"><SPAN style="font-size:10pt;">  
				<br>Web-based Dynamic DNS Update 
				<br> 
				<br>
				<form method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
					<table>
						<tbody>
							<tr><td>Nameserver</td> 
								<td width="370"> 
									<input name="nameserver" class="box" value="ns1.cslab.net" disabled></td>
							</tr>
							<tr>        <td>HOST</td> 
								<td width="370"> 
									<input name="host" class="box" value="www1.freeserver.kr"></td>
							</tr>
							<tr>        <td>TTL</td> 
								<td width="370"> 
									<input name="ttl" value="60" type="radio" checked>60
									<input name="ttl" value="60" type="radio">3600 
									<input name="ttl" value="86400" type="radio">86400</td>
							</tr>
							<tr>        <td>TYPE</td> 
								<td width="370"> 
									<input name="type" value="A" type="radio" checked>A 
									<input name="type" value="CNAME" type="radio">CNAME 
									<input name="type" value="NS" type="radio">NS 
									<input name="type" value="MX" type="radio">MX 
									<input name="type" value="TXT" type="radio">TXT         </td>
							</tr>
							<tr>        <td>TYPE VALUE &nbsp;</td>        <td> 
									<input name="type_value" class="box" type="text" value="192.168.0.1"></td>
							</tr>
							<tr> 
								<td colspan="2"> 
									<input name="update" style="font-weight: bold;" value="Add" type="submit"> 
									<input name="update" style="font-weight: bold;" value="Delete" type="submit"> 
									<input name="update" style="font-weight: bold;" value="Cancel" type="reset">        </td>
							</tr>
						</tbody>
					</table>
				</form> 
				<br>
<pre>
<?php
include 'Net/DNS.php';
$resolver = new Net_DNS_Resolver();

// debug output (0 : disalbe, 1 : enable)
$resolver->debug = 0;

$response = $resolver->axfr('freeserver.kr');
echo "Asynchronous Full Transfer Zone";
echo "<hr>";
if (count($response)) 
{
  foreach ($response as $rr) 
  {
		$rr->display();
  }
}
echo "<hr>";
?>
</body></html>
