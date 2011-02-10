<?php
//------------------------------------------------------------------------------
// Web-based Dynamic DNS Update
// @author: Jaeyoun Kim & Calyce.fr
// @homepage: http://code.google.com/p/dynamic-dnsupdate/
// @version: 1.1
// @date: Fev 2, 2011
//------------------------------------------------------------------------------
include './config.php';

// http://pear.php.net/manual/fr/package.networking.net-dns.php

$remote_ip=$_SERVER['REMOTE_ADDR'];
if (isset($_GET['domain']) && $_GET['domain'] != 'NULL')
	$domain=$_GET['domain'];
else
	$domain=NULL;
if (isset($_GET['command']))
	$command=$_GET['command'];
else
	$command=NULL;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
	<head>
		<title>Web-based Dynamic DNS Update</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
		<link rel="stylesheet" href="style.css" />
		<script type="text/javascript">
		// <![CDATA[
			function PrintId(baliseId)
			{
				if (document.getElementById && document.getElementById(baliseId) != null)
				{
					document.getElementById(baliseId).style.visibility='visible';
					document.getElementById(baliseId).style.display='block';
				}
			}

			function HideId(baliseId)
			{
				if (document.getElementById && document.getElementById(baliseId) != null)
				{
					document.getElementById(baliseId).style.visibility='hidden';
					document.getElementById(baliseId).style.display='none';
				}
			}
			function changeForm(type)
			{
				PrintId('host');
				HideId('poid');
				switch (type) {
				case 'MX':
					document.getElementById('ttl_input').value ='<?php echo $ttl_default["MX"]; ?>' ;
					HideId('host');
					PrintId('poid');
				break;
				case 'AAAA':
					document.getElementById('ttl_input').value ='<?php echo $ttl_default["AAAA"]; ?>' ;
				break;
				case 'A':
					document.getElementById('ttl_input').value ='<?php echo $ttl_default["A"]; ?>' ;
				break;
				case 'CNAME':
					document.getElementById('ttl_input').value ='<?php echo $ttl_default["CNAME"]; ?>' ;
				break;
				case 'NS':
					document.getElementById('ttl_input').value ='<?php echo $ttl_default["NS"]; ?>' ;
					HideId('host');
				break;
				/*case 'PTR':
					document.getElementById('ttl_input').value ='<?php echo $ttl_default["PTR"]; ?>' ;
					HideId('host');
				break;
				case 'TXT':
					document.getElementById('ttl_input').value ='<?php echo $ttl_default["TXT"]; ?>' ;
					HideId('host');
				break;*/
				}
			}
		// ]]>
		</script>
	</head>
	<body> 
	<h1><a href="http://code.google.com/p/dynamic-dnsupdate/"><img src="./images/domain.jpg" alt="0" height="100" width="300" /></a>
	<br />Web-based Dynamic DNS Update Program</h1>
	<p class="version">
		<i>Project Homepage: <a href="http://code.google.com/p/dynamic-dnsupdate/"><?php echo $version; ?></a></i>
		</p>
		<p> This is a web-based dynamic DNS update program that can add, replace or delete DNS resource records in a master server.
		</p>
		<p> 
		Your IP address:<b> <?php echo $remote_ip; ?></b>.
		</p>
		<form method="get" action="./dnsupdate.php">
			<label>Domain name: </label>
			<select name="domain"> 
			<?php
			if ($domain == NULL)
				echo "<option selected=\"selected\" value=\"NULL\"> - </option>";
			foreach ($mydomain_tab as $mydomain) 
			{ 
				if ($domain == $mydomain)
					echo "<option selected=\"selected\" value=\"".$mydomain."\">".$mydomain."</option>";
				else
					echo "<option value=\"".$mydomain."\">".$mydomain."</option>";
			}
			?>
			</select> 
			<input name="command" type="submit" value="Go" /> 
			<input name="command" type="submit" value="Authcode" /> 
			<span><a href="authcode.html" class="small">What is authcode?</a></span>
		</form>		
<?php
if ($domain != NULL)
{
		if ($command == "Authcode")
		{
			// http://www.z-host.com/scripts/ipasswd/
			$salt = str_replace(".", "", $domain);

			// Password to be encrypted for a .htpasswd file
			$clearTextPassword = $salt;

			// Encrypt password
			$password = crypt($clearTextPassword, base64_encode($clearTextPassword));

			// Encrypt password Again
			$password2 = crypt($password, base64_encode($password));

			echo "Your auth code is <strong>" . $password . "</strong>";

			$file_contents = file_get_contents($file);
			$fh = fopen($file, "w");
			$file_contents = "dnsupdate:" . $password2;
			fwrite($fh, $file_contents);
			fclose($fh);
		}
?>
		<h3 style="font-family: Arial;">
			<img src="./images/btn_dns_bg.gif" alt="Image" align="bottom" width="20" /> Add a resource record</h3>
		<form style="font-family: Arial;" method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
			<p><label>Domain </label>
				<input name="domain" class="box" value="<?php if ($domain == NULL) { echo $mydomain; } else { echo $domain; } ?>" disabled="disabled" /></p>
			<p><label>Nameserver : </label>
				<input name="nameserver" class="box" value="<?php echo $ns1; ?>" disabled="disabled" /></p>
			<p><label>Type : </label> 
				<input name="type" value="A" type="radio" onclick="changeForm('A')" checked="checked" />A 
				<input name="type" value="AAAA" type="radio" onclick="changeForm('AAAA')" />AAAA
				<input name="type" value="CNAME" type="radio" onclick="changeForm('CNAME')" />CNAME 
				<input name="type" value="NS" type="radio" onclick="changeForm('NS')" />NS 
				<input name="type" value="MX" type="radio"  onclick="changeForm('MX')" />MX 
				<input name="type" value="TXT" type="radio" onclick="changeForm('TXT')" /><s>TXT</s>
				<input name="type" value="PTR" type="radio" onclick="changeForm('PTR')" /><s>PTR</s> </p>
			<p id="host"><label>Host :</label> 
				<input name="host" id="host_input" class="box" value="" /></p>
			<p><label>TTL : </label> 
				<input name="ttl" id="ttl_input" class="box" value="" type="text" />
				<small>60 / 3600 (1 Hour) / 86400 (1 Day) / 604800 (1 Week)</small></p>
			<p id="ttl_exemple"></p>
			<p id="poid"><label>Poid : </label> 
				<input name="poid" class="box" value="5" /></p>
			<p><label>Type Value : </label> 
				<input name="type_value" class="box" value="" type="text" /></p>
			<input name="domain" style="font-weight: bold;" value="<?php if ($domain == NULL) { echo $mydomain1; } else { echo $domain; } ?>" type="hidden" /> 
			<input name="command" style="font-weight: bold;" value="Add" type="submit" /> 
		</form><h3>
			<img src="./images/btn_dns_bg.gif" alt="Image" align="bottom" width="20" /> Delete a resource record</h3>
			
		<form method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
			<table border="1" cellpadding="3" cellspacing="0">
				<tr> <td>Check</td> 
					<td width="200">Domain</td> 
					<td width="120">TTL (Time-To-Live)</td> 
					<td width="80">Type</td> 
					<td width="250">Value</td>
				</tr>
<?php 
include 'Net/DNS.php';
$resolver = new Net_DNS_Resolver();
$resolver->nameservers = array( $ns1 );

// debug output (0 : disalbe, 1 : enable)
$resolver->debug = 0;
if ($domain == NULL) 
{ 
	$axfrdomain = $mydomain_tab[0]; 
} 
else 
{ 
	$axfrdomain = $domain; 
}
$response = $resolver->axfr($axfrdomain);
$i = 0;
if (count($response))
{
  foreach ($response as $rr) 
  {              	
		if ( ($response[$i]->type) == "A" )
		{
			echo "<tr>";
			echo "<td style='background-color: rgb(255, 255, 204);'>";
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"checkbox\" />";
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
		else if ( ($response[$i]->type) == "AAAA"  )
		{
			echo "<tr>";
			echo "<td style='background-color: rgb(200, 255, 204);'>";
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"checkbox\" />";
			echo "</td>";
			echo "<td style='background-color: rgb(200, 255, 204);'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: rgb(200, 255, 204);'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: rgb(200, 255, 204);'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: rgb(200, 255, 204);'>";
			echo $response[$i]->address;
			echo "</td>";
			echo "</tr>";
			
		}
		else if ( ($response[$i]->type) == "MX" )
		{
			echo "<tr>";
			echo "<td style='background-color: rgb(255, 204, 153);'>";
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"checkbox\" />";
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
			echo "<br />";
			echo "Exchanger: <b>" . $response[$i]->exchange . "</b>";
			echo "</td>";
			echo "</tr>";
		}
		else if ( ($response[$i]->type) == "NS" )
		{
			echo "<tr>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"checkbox\" />";
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
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"checkbox\" />";
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
		else if ( ($response[$i]->type) == "PTR" )
		{
			echo "<tr>";
			echo "<td style='background-color: #ECCCFF;'>";
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"checkbox\" />";
			echo "</td>";
			echo "<td style='background-color: #ECCCFF;'>";
			echo $response[$i]->name;
			echo "</td>";
			echo "<td style='background-color: #ECCCFF;'>";
			echo $response[$i]->ttl;
			echo "</td>";
			echo "<td style='background-color: #ECCCFF;'>";
			echo $response[$i]->type;
			echo "</td>";
			echo "<td style='background-color: #ECCCFF;'>";
			echo $response[$i]->ptrdname;
			echo "</td>";
			echo "</tr>";
		}
		else if ( ($response[$i]->type) == "SOA" )
		{
			echo "<tr>";
			echo "<td style='background-color: #99FF99;'>";
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"hidden\" disabled=\"disabled\" />N/A";
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
			echo "<br />";
			echo "Refresh: <b>" . $response[$i]->refresh . "</b>";
			echo "<br />";
			echo "Retry: <b>" . $response[$i]->retry . "</b>";
			echo "<br />";
			echo "Expire: <b>" . $response[$i]->expire . "</b>";
			echo "<br />";
			echo "Minimum: <b>" . $response[$i]->minimum . "</b>";
			echo "</td>";
			echo "</tr>";
		}
		else if ( ($response[$i]->type) == "TXT" )
		{
			echo "<tr>";
			echo "<td style='background-color: #99FF99;'>";
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"checkbox\" />";
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
			foreach($response[$i]->text as $txt)
			{
				echo $txt."<br />";
			}
			echo "</td>";
			echo "</tr>";
		}
		else
		{
			echo "<tr>";
			echo "<td style='background-color: #CCFFFF;'>";
			echo "<input name=\"rrnumber[]\" value=\"".$i."\" type=\"checkbox\" />";
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
			<br /> 
			<input name="host" style="font-weight: bold;" value="<?php if ($domain == NULL) { echo $mydomain_tab[0]; } else { echo $domain; } ?>" type="hidden" /> 
			<input name="command" style="font-weight: bold;" value="Delete" type="submit" /> 
			<input name="command" style="font-weight: bold;" value="Cancel" type="reset" />
		</form>
		<p class="copyright">
			<?php echo $copyright; ?>  <a href="http://validator.w3.org/check?uri=referer">Valid XHTML 1.0 Transitional</a>
		</p> 
	<script type="text/javascript">
	changeForm('A')
	// No script compatible
	</script>
<?php
}
?>
	</body>
</html>