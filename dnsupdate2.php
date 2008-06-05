<?php
//------------------------------------------------------------------------------
// Web-based Dynamic DNS Update
// @author: Jaeyoun Kim
// @homepage: http://code.google.com/p/dynamic-dnsupdate/
// @version: 1.0
// @date: June 1, 2008
//------------------------------------------------------------------------------
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
<?php
include 'Net/DNS.php';
include './config.php';
$domain=$_GET[domain];
$command=$_GET[command];
$host=$_GET[host];
$ttl=$_GET[ttl];
$type=$_GET[type];
$type_value=$_GET[type_value];
$rrnumber=$_GET[rrnumber];
if ($host != NULL)
{
	$domain2 = $host . "." . $domain;
}
if ( $type == "A" || $type == "NS" || $type == "MX" || $type == "CNAME" )
{
	$input = $domain2 . ". " . $ttl . " IN " . $type . " " . $type_value;
}
else
{
	$input = $domain2 . ". " . $ttl . " IN " . $type . " " . $type_value;
}
if ($command == 'Add')
{
	echo "<h3>Add a Resource Record</h3>";
	recordAdd($domain, $input);
}
if ($command == 'Delete')
{
	echo "<h3>Delete a Resource Record</h3>";
	recordFind($host, $rrnumber);
}
/*----------------------------------------------------------------------------*/
function recordAdd($domain, $input)
{
	include './config.php';
	echo "<p>Query: " . $input . "</p>";
	$resolver = new Net_DNS_Resolver();
	$resolver->nameservers = array($ns1);
	$packet = new Net_DNS_Packet();
	$packet->header = new Net_DNS_Header();
	$packet->header->id = $resolver->nextid();
	$packet->header->qr = 0;
	$packet->header->opcode = "UPDATE";
	
	$packet->question[0] = new Net_DNS_Question($domain, "SOA", "IN");
	$packet->answer = array();
	
	//$rrAdd =& Net_DNS_RR::factory("www1.freeserver.kr. 60 IN A 192.168.0.1");
	$rrAdd =& Net_DNS_RR::factory($input);
	$packet->authority[0] = $rrAdd;
	$packet->header->qdcount = count($packet->question);
	$packet->header->ancount = count($packet->answer);
	$packet->header->nscount = count($packet->authority);
	$packet->header->arcount = count($packet->additional);
	$response = $resolver->send_tcp($packet, $packet->data());
	if ($response->header->rcode == "NOERROR")
	{
	  echo "<p>Update Result: Dynamic update is successful.</p>";
	}
	else if ($response->header->rcode != "NOERROR")
	{
	  return($response->header->rcode);
	}
	echo "<p><a href=./dnsupdate.php>Go back to the DNS Update</a></p>";
	echo "<hr><p class=copyright>$copyright;</p>";
}
/*----------------------------------------------------------------------------*/
function recordRemove($domain, $input)
{
	include './config.php';
	$resolver = new Net_DNS_Resolver();
	$resolver->nameservers = array($ns1);
	$packet = new Net_DNS_Packet();
	$packet->header = new Net_DNS_Header();
	$packet->header->id = $resolver->nextid();
	$packet->header->qr = 0;
	$packet->header->opcode = "UPDATE";
	$packet->question[0] = new Net_DNS_Question($domain, "SOA", "IN");
	$packet->answer = array();
	$rrDelete =& Net_DNS_RR::factory($input);
	//$rrDelete =& Net_DNS_RR::factory("test1.freeserver.kr. 0 NONE A 192.168.0.1");
	$packet->authority[0] = $rrDelete;
	$packet->header->qdcount = count($packet->question);
	$packet->header->ancount = count($packet->answer);
	$packet->header->nscount = count($packet->authority);
	$packet->header->arcount = count($packet->additional);
	$response = $resolver->send_tcp($packet, $packet->data());
	
	if ($response->header->rcode == "NOERROR")
	{
	  echo "Update Result: Dynamic update is successful.";
	}
	else if ($response->header->rcode != "NOERROR")
	{
	  return($response->header->rcode);
	}
	echo "<p><a href=./dnsupdate.php>Go back to the DNS Update</a>";
	echo "<hr><p class=copyright>$copyright;</p>";
}
/*----------------------------------------------------------------------------*/
function recordFind($domain, $rrnumber)
{
	$resolver = new Net_DNS_Resolver();
	$resolver->debug = 0;
	$response = $resolver->axfr($domain);
	$i = 0;
	if (count($response)) 
	{
	  foreach ($response as $rr) 
	  {
	  	if ($i == $rrnumber)
	  	{
				if ( $response[$i]->type == "A" )
				{
					$input = $response[$i]->name . " 0 NONE A " . $response[$i]->address;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "NS" )
				{
					$input = $response[$i]->name . " 0 NONE NS " . $response[$i]->nsdname;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $pieces[4] == "MX" )
				{
					$input = $response[$i]->name . " 0 NONE MX " . $response[$i]->preference . " " . $response[$i]->exchange;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "CNAME" )
				{
					$input = $response[$i]->name . " 0 NONE CNAME " . $response[$i]->cname;
					echo "<p>Query: " . $input . "</p>";
				}
				else 
				{
					echo "<p>Not supported yet!</p>";
					echo "<hr><p class=copyright>$copyright;</p>";
					exit();
				}
				recordRemove($domain, $input);
				echo "<hr><p class=copyright>$copyright;</p>";
				exit();
			}
			$i = $i + 1;
	  }
	}
	if (count($response) == 0)
	{
  	echo "AXFR Failed";
 	}
}
/*----------------------------------------------------------------------------*/
function recordAXFR($domain)
{
	$resolver1 = new Net_DNS_Resolver();
	// debug output (0 : disalbe, 1 : enable)
	$resolver1->debug = 0;
	
	$response = $resolver1->axfr($host);
	echo "<p>Asynchronous Full Transfer Zone</p>";
	echo "<pre>";
	if (count($response)) 
	{
	  foreach ($response as $rr) 
	  {
			$rr->display();
	  }
	}
	echo "</pre>"
		} /*----------------------------------------------------------------------------*/ ?>