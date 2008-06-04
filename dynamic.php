<?php 
include 'Net/DNS.php';

$update=$_GET[update];
$domain=$_GET[domain];
$host=$_GET[host];
$ttl=$_GET[ttl];
$type=$_GET[type];
$type_value=$_GET[type_value];
$rrnumber=$_GET[rrnumber];

if ($host != NULL)
{
	$domain = $host . "." . $domain;
}

if ( $type == "A" || $type == "NS" || $type == "MX" || $type == "CNAME" )
{
	$input = $domain . ". " . $ttl . " IN " . $type . " " . $type_value;
}
else
{
	$input = $domain . ". " . $ttl . " IN " . $type . " " . $type_value;
}

recordAdd($input);

/*----------------------------------------------------------------------------*/
function recordAdd($input)
{
	$resolver = new Net_DNS_Resolver();

	$resolver->nameservers = array('ns1.cslab.net');

	$packet = new Net_DNS_Packet();

	$packet->header = new Net_DNS_Header();
	$packet->header->id = $resolver->nextid();
	$packet->header->qr = 0;
	$packet->header->opcode = "UPDATE";
	
	$packet->question[0] = new Net_DNS_Question("freeserver.kr", "SOA", "IN");

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
	  print "\nUpdate Result: Dynamic update is successful.\n";
	}
	else if ($response->header->rcode != "NOERROR")
	{
	  return($response->header->rcode);
	}
}

?>