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

if ($type == "NS" || $type == "MX" )
{
	$input = $domain . ". " . $ttl . " IN " . $type . " " . $type_value;
}
else
{
	$input = $host . "." . $domain . ". " . $ttl . " IN " . $type . " " . $type_value;
}


if ($update == 'Add')
{
	echo "<p>Add a Resource Record<p>";
	recordAdd($input);
	//recordAXFR($domain);
}

if ($update == 'Delete')
{
	echo "<p>Delete a Resource Record<p>";
	recordFind($host, $rrnumber);
}

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
	  echo "Update Result: Dynamic update is successful.";
	}
	else if ($response->header->rcode != "NOERROR")
	{
	  return($response->header->rcode);
	}
	echo "<p><a href=./dnsupdate.php>Go back to the DNS Update</a>";
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
				//$rr->display();
				$rrText = $rr->string();
				$pieces = explode("\t", $rrText);
				echo "0 :" . $pieces[0];
				echo "<br>";
				echo "1 :" . $pieces[1];
				echo "<br>";
				echo "2 :" . $pieces[2];
				echo "<br>";
				echo "3 :" . $pieces[3];
				echo "<br>";
				echo "4 :" . $pieces[4];
				echo "<br>";
				echo "5 :" . $pieces[5];
		  	$i = $i + 1;
			
				if ( $pieces[3] == "CNAME" )
				{
			    $pieces4 = substr_replace($pieces[4] , "", -1);
					$input = $pieces[0] . " 0 NONE " . $pieces[3] . " " . $pieces4;
					echo "<br>";
					echo $input;
				}
				else if ( $pieces[4] == "NS" )
				{
			    $pieces5 = substr_replace($pieces[5] , "", -1);
					$input = $pieces[0] . " 0 NONE " . $pieces[4] . " " . $pieces5;
					echo "<br>";
					echo $input;
				}
				else if ( $pieces[4] == "MX" )
				{
			    $pieces5 = substr_replace($pieces[5] , "", -1);
					$input = $pieces[0] . " 0 NONE " . $pieces[4] . " " . $pieces5;
					echo "<br>";
					echo $input;
				}
				else 
				{
					echo "<br>";
					$input = $pieces[0] . " 0 NONE " . $pieces[3] . " " . $pieces[4];
				}
				//echo $input;
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