<?php
//------------------------------------------------------------------------------
// Web-based Dynamic DNS Update
// @author: Jaeyoun Kim & Calyce.fr
// @homepage: http://code.google.com/p/dynamic-dnsupdate/
// @version: 1.1
// @date: Fev 2, 2011
//------------------------------------------------------------------------------

include './config.php';
include 'Net/DNS.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
	<head>
		<title>Web-based Dynamic DNS Update</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
<?php
if (isset($_GET['domain']))
	$domain=$_GET['domain'];
else
	$domain=NULL;
$command=$_GET['command'];
$host=$_GET['host'];
if (isset($_GET['ttl']))
	$ttl=$_GET['ttl'];
else
	$ttl=NULL;
if (isset($_GET['poid']))
	$poid=$_GET['poid'];
else
	$poid=NULL;
if (isset($_GET['type']))
	$type=$_GET['type'];
else
	$type=NULL;
if (isset($_GET['type_value']))
	$type_value=$_GET['type_value'];
else
	$type_value=NULL;
if (isset($_GET['rrnumber']))
	$rrnumber=$_GET['rrnumber'];
else
	$rrnumber=NULL;

if ($host != NULL)
	$domain2 = $host . "." . $domain;
else
	$domain2 = $domain;
	
# http://www.manpagez.com/man/3/Net::DNS::RR/
switch($type)
{
    case 'MX';
        $input = $domain . ". " . $ttl . " IN " . $type . " " . $poid . " " . $type_value.".";
    break;
    case 'NS';
    case 'PTR';
        $input = $domain . ". " . $ttl . " IN " . $type . " " . $type_value;
    break;
    default;
		$input = $domain2 . ". " . $ttl . " IN " . $type . " " . $type_value;
    break;
}

switch($command)
{
    case 'Add';
		echo "<h3>Add a Resource Record</h3>";
		recordAdd($domain, $input);
		echo "<p><a href=\"./dnsupdate.php?domain=".$domain."\">Go back to the DNS Update</a>";
    break;
    case 'Delete';
		echo "<h3>Delete a Resource Record</h3>";
		foreach ($rrnumber as $number)
		{
			recordFind($host, $number);
		}
		echo "<p style=\"color: red\">Do not refresh this page!</p>";
		echo "<p><a href=\"./dnsupdate.php?domain=".$host."\">Go back to the DNS Update</a>";
    break;
}

echo "<p class=copyright>$copyright;</p>";

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
	// opcode - http://www.networksorcery.com/enp/protocol/dns.htm

	$packet->question[0] = new Net_DNS_Question($domain, "SOA", "IN");
	$packet->answer = array();
	 
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
	  echo "<p>Error : ".$response->header->rcode."</p>";
	}
	//recordAXFR($domain);
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
	
	// Delete a resource record by setting the ttl to 0 and the class
	// to "NONE".  Add the RR object to the authority/update section
	//$DELrr = new Net_DNS_RR("freeserver.kr 60 IN TXT v=spf1 a mx a:ripple ip4:211.208.163.118 ~all");
	//$DELrr = new Net_DNS_RR("www.example.com 3600 IN A 192.168.0.1");
	//$DELrr->ttl = 0;
	//$DELrr->class = "NONE";
	//$packet->authority[0] = $DELrr; // Authority is the update section

	#$rrDelete =& Net_DNS_RR::factory("a.calyce.fr 0 NONE A 127.0.0.1");
	$rrDelete =& Net_DNS_RR::factory($input);
	$packet->authority[0] = $rrDelete;

	$packet->header->qdcount = count($packet->question);
	$packet->header->ancount = count($packet->answer);
	$packet->header->nscount = count($packet->authority);
	$packet->header->arcount = count($packet->additional);

	// send_tcp - Sends a packet via TCP to the list of name servers.
	// object Net_DNS_Packet send_tcp( string $packet, string $packet_data)
	$response = $resolver->send_tcp($packet, $packet->data());
	
	if ($response->header->rcode == "NOERROR")
	{
	  echo "<p>Update Result: Dynamic update is successful.</p>";
	}
	else if ($response->header->rcode != "NOERROR")
	{
	  echo "<p>Error : ".$response->header->rcode."</p>";
	}
}
/*----------------------------------------------------------------------------*/
function recordFind($domain, $rrnumber)
{
	include './config.php';

	$resolver = new Net_DNS_Resolver();
	$resolver->nameservers = array( $ns1 );
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
					//echo $response[$i]->display();
					$input = $response[$i]->name . " 0 NONE A " . $response[$i]->address;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "AAAA" )
				{
					//echo $response[$i]->display();
					$input = $response[$i]->name . " 0 NONE AAAA " . $response[$i]->address;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "NS" )
				{
					$input = $response[$i]->name . " 0 NONE NS " . $response[$i]->nsdname;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "MX" )
				{
					$input = $response[$i]->name . " 0 NONE MX " . $response[$i]->preference . " " . $response[$i]->exchange;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "CNAME" )
				{
					$input = $response[$i]->name . " 0 NONE CNAME " . $response[$i]->cname;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "TXT" )
				{
					$input = $response[$i]->name . " 0 NONE TXT " . $response[$i]->text[0];
					echo "<p>Query: " . $input . "</p>";
				}
				else 
				{
					echo "<p>Not supported yet!</p>";
					echo "<p><a href=\"./dnsupdate.php?domain=".$domain."\">Go back to the DNS Update</a>";
					echo "<p class=copyright>$copyright;</p>";
					exit();
				}
				#echo "recordRemove($domain, $input);<br />";
				recordRemove($domain, $input);
				#exit();
			}
			$i = $i + 1;
	  }
	}
	if (count($response) == 0)
	{
		echo "<p>AXFR Failed</p>";
 	}
}
/*----------------------------------------------------------------------------*/
function recordAXFR($domain)
{
	$resolver1 = new Net_DNS_Resolver();
	$resolver1->debug = 0;
	
	$response = $resolver1->axfr($domain);
	echo "<p>Asynchronous Full Transfer Zone</p>";
	echo "<pre>";
	//print_r($response);
	if (count($response)) 
	{
	  foreach ($response as $rr) 
	  {
			$rr->display();
	  }
	}
	echo "</pre>";
}
/*----------------------------------------------------------------------------*/
?>
