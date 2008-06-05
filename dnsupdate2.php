<FONT face="Arial"><SPAN style="font-size:10pt;">
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

if ($update == 'Add')
{
	echo "<h3>Add a Resource Record</h3>";
	recordAdd($input);
}

if ($update == 'Delete')
{
	echo "<h3>Delete a Resource Record</h3>";
	recordFind($host, $rrnumber);
}

/*----------------------------------------------------------------------------*/
function recordAdd($input)
{
	echo "<p>Query: " . $input . "</p>";

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
	  echo "<p>Update Result: Dynamic update is successful.</p>";
	}
	else if ($response->header->rcode != "NOERROR")
	{
	  return($response->header->rcode);
	}
	echo "<p><a href=./dnsupdate.php>Go back to the DNS Update</a></p>";
	echo "<hr>Copyright (c) 2008 CSLab.net  All rights reserved.";
}

/*----------------------------------------------------------------------------*/
function recordRemove($input)
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

	$rrDelete =& Net_DNS_RR::factory($input);
	//$rrDelete =& Net_DNS_RR::factory("system.freeserver.kr. 0 NONE CNAME www.freeserver.kr.");
	//$rrDelete =& Net_DNS_RR::factory("www3.freeserver.kr. 0 NONE A 192.168.0.1");
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
}

/*----------------------------------------------------------------------------*/
function recordFind($host, $rrnumber)
{
	$resolver = new Net_DNS_Resolver();
	$resolver->debug = 0;
	$response = $resolver->axfr($host);

	$i = 0;
	if (count($response)) 
	{
	  foreach ($response as $rr) 
	  {
	  	if ($i == $rrnumber)
	  	{
				$rrText = $rr->string();
				$pieces = explode("\t", $rrText);

				if ( $response[$i]->type == "A" )
				{
					//$rrDelete =& Net_DNS_RR::factory("www3.freeserver.kr. 0 NONE A 192.168.0.1");
			    //$pieces4 = substr_replace($pieces[4] , "", -1);
					$input = $response[$i]->name . " 0 NONE A " . $response[$i]->address;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "NS" )
				{
			    //$pieces5 = substr_replace($pieces[5] , "", -1);
					$input = $response[$i]->name . " 0 NONE NS " . $response[$i]->nsdname;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $pieces[4] == "MX" )
				{
			    $pieces5 = substr_replace($pieces[5] , "", -1);
					$input = $response[$i]->name . " 0 NONE MX " . $response[$i]->preference . " " . $response[$i]->exchange;
					echo "<p>Query: " . $input . "</p>";
				}
				else if ( $response[$i]->type == "CNAME" )
				{
				    $pieces4 = substr_replace($pieces[4] , "", -1);
					$input = $response[$i]->name . " 0 NONE CNAME " . $response[$i]->cname;
					echo "<p>Query: " . $input . "</p>";
				}
				else 
				{
					echo "<p>Not supported yet!</p>";
					echo "<hr>Copyright (c) 2008 CSLab.net  All rights reserved.";
					exit();
				}
				recordRemove($input);
				echo "<hr>Copyright (c) 2008 CSLab.net  All rights reserved.";
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
function recordAXFR($host)
{
	$resolver1 = new Net_DNS_Resolver();

	// debug output (0 : disalbe, 1 : enable)
	$resolver1->debug = 0;
	
	$response = $resolver1->axfr($host);

	echo "<br><br>Asynchronous Full Transfer Zone<hr>";
	echo "<pre>";

	if (count($response)) 
	{
	  foreach ($response as $rr) 
	  {
			$rr->display();
	  }
	}
	echo "<hr>";
}
/*----------------------------------------------------------------------------*/
?>