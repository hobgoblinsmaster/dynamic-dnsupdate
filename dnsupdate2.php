<?php 
include 'Net/DNS.php';

$update=$_GET[update];
$host=$_GET[host];
$ttl=$_GET[ttl];
$type=$_GET[type];
$type_value=$_GET[type_value];

echo "Information for Dynamic DNS Update is submitted.";
echo "<hr>Debug Information -> <br>";
echo "$update";
echo "<br>";
echo "$host";
echo "<br>";
echo "$ttl";
echo "<br>";
echo "$type";
echo "<br>";
echo "$type_value";
echo "<hr>";

$input = $host . ". " . $ttl . " IN " . $type . " " . $type_value;
$input2 = $host . ". " . "0 NONE " . $type . " " . $type_value;

$resolver = new Net_DNS_Resolver();

// We should only send the request to the master server
// accepting DNS updates for the zone.
$resolver->nameservers = array('ns1.cslab.net');

// We must manually construct the DNS packet for a DNS update
// First we must instantiate the packet object
$packet = new Net_DNS_Packet();

// Create the header for the update packet.  Most of the defaults are
// acceptable, but we must set the header OPCODE to "UPDATE"
$packet->header = new Net_DNS_Header();
$packet->header->id = $resolver->nextid();
$packet->header->qr = 0;
$packet->header->opcode = "UPDATE";

// As specified in RFC2136, the question section becomes the "ZONE" section.
// This specifies the zone for which we are requesting a change.  This
// reflects the zone configuration as specified in the nameserver
// configuration.
$packet->question[0] = new Net_DNS_Question('freeserver.kr', "SOA", "IN");

// The "ANSWER" section of the packet becomes the "PREREQUISITE" section of
// the packet. Section 2.4 of RFC2136 defines the possible values for this
// section.  As show below, without any prerequisites the nameserver will
// attempt to perform the update unconditionally.
$packet->answer = array();

// The AUTHORITY section becomes the "UPDATE" section of the DNS packet.  The
// UPDATE section is a collection of resource record updates that should be
// modified in one form or another.  RRs can be deleted or added based on the
// values passed in the RR object.  These values are specified in RFC2136 but
// are summarized here:
//
// Adding an RR
// A complete RR object with a non-zero TTL is considered an addition.
//
// Deleting an RR
// A complete RR object with a zero TTL is considered an deletion. An RR that
// matches (exactly) with all values except for the TTL will be removed.
//
// Deleting an RRset
// A complete RR object with a zero TTL and a type of ANY is considered a
// deletion of all RRs with the specified name and type. An RR that matches
// (exactly) with all values except for the TTL and the TYPE will be removed.
//
// Deleting all RRsets for a name 
// A complete RR object with a zero TTL, a type of ANY, and a class of ANY is
// considered a deletion of all RRs with the specified name. Any RR that
// matches the name section of the query will be removed.
//
// The following specification will delete the RR that has a name of
// "example.com", class of "IN", type of "A", and an address of
// "192.0.34.166".

if ($update == "Delete")
{
	echo $input2;
	$rrDelete =& Net_DNS_RR::factory($input2);
	//$rrDelete =& Net_DNS_RR::factory("testdns.freeserver.kr. 0 NONE A 192.168.0.253");
	$packet->authority[0] = $rrDelete;
}
//
// The following specification will add an RR that has a name of example.com,
// a TTL of 1 hour, a class of "IN", type of "A", and an address of
// "192.0.34.155").  Note that the only difference between this RR and the
// previous RR is the value of the TTL.

if ($update == "Add")
{
	echo $input;
	$rrAdd =& Net_DNS_RR::factory($input);
	$packet->authority[0] = $rrAdd;
}

//
// The RR modifications are added to the authority (UPDATE) section of the DNS
// packet.
//$packet->authority[0] = $rrDelete;
//$packet->authority[1] = $rrAdd;
//
// The signature must be present in any packet sent to a nameserver that
// requires authentication.  The TSIG RR is added to the additional section of
// the DNS packet.
//$tsig =& Net_DNS_RR::factory("keyname.as.specified.in.server. TSIG ThisIsMyKey");
//$packet->additional = array($tsig);

// Net_DNS does not automatically calculate the number of records stored in
// each section.  This calculation must be done manually.
$packet->header->qdcount = count($packet->question);
$packet->header->ancount = count($packet->answer);
$packet->header->nscount = count($packet->authority);
$packet->header->arcount = count($packet->additional);
//
// After creating your packet, you must send it to the name server for
// processing.  DNS updates must use the send_tcp() method: 
$response = $resolver->send_tcp($packet, $packet->data());

//
// The response from the server will vary.  If the update was successfuly, the
// server will have a response code of "NOERROR".  Any other error types will
// be reported in the response packet's header "rcode" variable.

echo "<br>";
echo "Update Result: " . $response->header->rcode;

if ($response->header->rcode != "NOERROR")
{
  return($response->header->rcode);
}

$resolver1 = new Net_DNS_Resolver();

// debug output (0 : disalbe, 1 : enable)
$resolver1->debug = 0;

$response = $resolver1->axfr('freeserver.kr');

echo "<br><br>Asynchronous Full Transfer Zone<hr>";
echo "<PRE>";

if (count($response)) 
{
  foreach ($response as $rr) 
  {
		$rr->display();
  }
}
echo "<hr>";

?>

<a href="./dnsupdate.php">Go back to the DNS Update</a>
