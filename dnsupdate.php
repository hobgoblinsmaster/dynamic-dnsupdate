<?php
//------------------------------------------------------------------------------
// Web-based Dynamic DNS Update
// @author: Jaeyoun Kim / Calyce.fr & Novius (http://www.novius.com)
// @homepage: http://code.google.com/p/dynamic-dnsupdate/
// @version: 0.2
// @date: 07/2012
//------------------------------------------------------------------------------

include './config.php';
include 'Net/DNS2.php';
include './function.php';

if (isset($_GET['domain']) && $_GET['domain'] != 'NULL') {
	$domain=$_GET['domain'];
} else {
	$domain=NULL;
}
if (isset($_GET['command'])) {
	$command=$_GET['command'];
} else {
	$command=NULL;
}

$debug = isset($_GET['debug']);

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
			function PrintId(baliseId) {
				if (document.getElementById && document.getElementById(baliseId) != null) {
					document.getElementById(baliseId).style.visibility='visible';
					document.getElementById(baliseId).style.display='block';
				}
			}

			function HideId(baliseId) {
				if (document.getElementById && document.getElementById(baliseId) != null) {
					document.getElementById(baliseId).style.visibility='hidden';
					document.getElementById(baliseId).style.display='none';
				}
			}
			function changeForm(type) {
				PrintId('name');
				HideId('poid');
				switch (type) {
				case 'MX':
					document.getElementById('ttl_input').value ='<?= TTL_MX ?>' ;
					HideId('name');
					PrintId('poid');
				break;
				case 'AAAA':
					document.getElementById('ttl_input').value ='<?= TTL_AAAA ?>' ;
				break;
				case 'A':
					document.getElementById('ttl_input').value ='<?= TTL_A ?>' ;
				break;
				case 'CNAME':
					document.getElementById('ttl_input').value ='<?= TTL_CNAME ?>' ;
				break;
				case 'NS':
					document.getElementById('ttl_input').value ='<?= TTL_NS ?>' ;
					HideId('name');
				break;
                /*
				case 'PTR':
					document.getElementById('ttl_input').value ='<?= TTL_PTR ?>' ;
					HideId('name');
				break;
				case 'TXT':
					document.getElementById('ttl_input').value ='<?= TTL_TXT ?>' ;
					HideId('name');
				break;
                */
				}
			}
            function deleteRecord(rr, hrefRedirect) {
                if (confirm("Are you sure you want to run the delete command : \n\n\t " + rr + "?")) {
                    document.location = hrefRedirect;
                } else {
                    return false;
                }
            }
		// ]]>
		</script>
	</head>
	<body> 
    <?php if ($debug) echo 'Debug On'; ?>
	<h1><a href="http://code.google.com/p/dynamic-dnsupdate/"><img src="./images/domain.jpg" alt="0" height="30" width="130" /></a>
	Web-based Dynamic DNS Update Program</h1>
    <?php 
    if (isset($_GET['todo'])) {
        echo '<h3><img src="./images/btn_dns_bg.gif" alt="Image" align="bottom" width="20" /> Result of the action on a resource record : </h3>';
        formAction($_GET); 
        echo '<p><b>Do not refresh this page!</b></p>';
		echo '<p><a href="./dnsupdate.php?domain='.$domain.'">Go back to the DNS Update</a>';
    } else {
    ?>
    <!--
    ################# Select Domain form        
    -->
    <form method="get" action="./dnsupdate.php">
        <label>Domain name: </label>
        <select name="domain"> 
        <?php
            $domains = explode(';', DOMAIN);
            if ($domain == NULL) {
                echo '<option selected="selected" value="NULL"> - </option>';
            }
            foreach ($domains as $one_domain)  { 
                if ($domain == $one_domain) {
                    echo '<option selected="selected" value="'.$one_domain.'">'.$one_domain.'</option>';
                } else {
                    echo '<option value="'.$one_domain.'">'.$one_domain.'</option>';
                }
            }
        ?>
        </select> 
        <input name="command" type="submit" value="Go" /> 
    </form>
    <?php if ($domain != NULL) { ?>
    <!--
    ################# Add record 
    -->
    <div id="recordAddWizard">
    <h3 style="font-family: Arial;">
        <img src="./images/btn_dns_bg.gif" alt="Image" align="bottom" width="20" /> Add a resource record (<a href="javascript:PrintId('recordAdd');HideId('recordAddWizard');">Switch Manual</a>)
    </h3>
    <form style="font-family: Arial;" method="get" action="#" name="DynamicDNSUpdate">
        <input type="hidden" name="todo" value="recordAddWizard" />
        <?php if ($debug) echo '<input type="hidden" name="debug" value="1" />'; ?>
        <p><label>Domain </label>
            <input name="domain" class="box" value="<?= $domain?>" disabled="disabled" /><label>Nameserver : </label>
            <input name="nameserver" class="box" value="<?= NS1; ?>" disabled="disabled" /></p>
        <p><label>Type : </label> 
            <input name="type" value="A" type="radio" onclick="changeForm('A')" checked="checked" />A 
            <input name="type" value="AAAA" type="radio" onclick="changeForm('AAAA')" />AAAA
            <input name="type" value="CNAME" type="radio" onclick="changeForm('CNAME')" />CNAME 
            <input name="type" value="NS" type="radio" onclick="changeForm('NS')" />NS 
            <input name="type" value="MX" type="radio"  onclick="changeForm('MX')" />MX 
            <input name="type" value="TXT" type="radio" onclick="changeForm('TXT')" /><s>TXT</s>
            <input name="type" value="PTR" type="radio" onclick="changeForm('PTR')" /><s>PTR</s> </p>
        <p id="name"><label>Host :</label> 
            <input name="name" id="name_input" class="box" value="" />.<?= $domain ?> 
            <small>(@ for <?= $domain ?>)</small></p>
        <p><label>TTL : </label> 
            <input name="ttl" id="ttl_input" class="box" value="" type="text" />
            <small>3600 (1 Hour) / 86400 (1 Day) / 604800 (1 Week)</small></p>
        <p id="ttl_exemple"></p>
        <p id="poid"><label>Poid : </label> 
            <input name="poid" class="box" value="5" /></p>
        <p><label>Type Value : </label> 
            <input name="value" class="box" value="" type="text" /></p>
        <input name="domain" value="<?= $domain; ?>" type="hidden" /> 
        <input name="command" style="font-weight: bold;" value="Add" type="submit" /> 
    </form>
    </div>
    <div id="recordAdd">
    <h3 style="font-family: Arial;">
        <img src="./images/btn_dns_bg.gif" alt="Image" align="bottom" width="20" /> Add a resource record (<a href="javascript:HideId('recordAdd');PrintId('recordAddWizard');">Switch Wizard</a>)
    </h3>
    <form style="font-family: Arial;" method="get" action="#" name="DynamicDNSUpdate">
        <input type="hidden" name="todo" value="recordAdd" />
        <?php if ($debug) echo '<input type="hidden" name="debug" value="1" />'; ?>
        <p><label>Domain </label>
            <input name="domain" class="box" value="<?= $domain?>" disabled="disabled" /><label>Nameserver : </label>
            <input name="nameserver" class="box" value="<?= NS1; ?>" disabled="disabled" /></p>
        <p><label>Command : </label> 
            <input name="rr" class="box" value="" type="text" />
            <input name="domain" value="<?= $domain; ?>" type="hidden" /> 
            <input name="command" style="font-weight: bold;" value="Add" type="submit" /> 
            <small><a href="javascript:PrintId('exampleCommand');">View</a>/<a href="javascript:HideId('exampleCommand');">hide</a> example command</small>
            <pre id="exampleCommand">       Example command : 

example.com. 300 IN A 172.168.0.50',
example.com. 300 IN NS ns1.mrdns.com.',
example.com. 300 IN CNAME www.example.com.',
example.com. 300 IN SOA ns1.mrdns.com. help.mrhost.ca. 1278700841 900 1800 86400 21400',
example.com. 300 IN WKS 128.8.1.14 6 21 25',
1.0.0.127.in-addr.arpa. 300 IN PTR localhost.',
example.com. 300 IN HINFO "PC-Intel-700mhz" "Redhat \"Linux\" 7.1"',
example.com. 300 IN MX 10 mx1.mrhost.ca.',
example.com. 300 IN TXT "first record" "another records" "a third"',
example.com. 300 IN RP louie.trantor.umd.edu. lam1.people.test.com.',
example.com. 300 IN AFSDB 3 afsdb.example.com.',
example.com. 300 IN X25 "311 06 17 0 09 56"',
example.com. 300 IN ISDN "150 862 028 003 217" "42"',
example.com. 300 IN RT 2 relay.prime.com.',
example.com. 300 IN NSAP 0x47.0005.80.005a00.0000.0001.e133.aaaaaa000151.00',
example.com. 300 IN SIG DNSKEY 7 1 86400 20100827211706 20100822211706 57970 gov. KoWPhMtLHp8sWYZSgsMiYJKB9P71CQmh9CnxJCs5GutKfo7Jpw+nNnDLiNnsd6U1JSkf99rYRWCyOTAPC47xkHr+2Uh7n6HDJznfdCzRa/v9uwEcbXIxCZ7KfzNJewW3EvYAxDIrW6sY/4MAsjS5XM/O9LaWzw6pf7TX5obBbLI+zRECbPNTdY+RF6Fl9K0GVaEZJNYi2PRXnATwvwca2CNRWxeMT/dF5STUram3cWjH0Pkm19Gc1jbdzlZVDbUudDauWoHcc0mfH7PV1sMpe80NqK7yQ24AzAkXSiknO13itHsCe4LECUu0/OtnhHg2swwXaVTf5hqHYpzi3bQenw==',
example.com. 300 IN KEY 256 3 7 AwEAAYCXh/ZABi8kiJIDXYmyUlHzC0CHeBzqcpyZAIjC7dK1wkRYVcUvIlpTOpnOVVfcC3Py9Ui/x45qKb0LytvK7WYAe3WyOOwk5klwIqRC/0p4luafbd2yhRMF7quOBVqYrLoHwv8i9LrV+r8dhB7rXv/lkTSI6mEZsg5rDfee8Yy1',
example.com. 300 IN PX 10 ab.net2.it. o-ab.prmd-net2.admdb.c-it.',
example.com. 300 IN AAAA 1080:0:0:0:8:800:200c:417a',
example.com. 300 IN LOC 42 21 54.675 N 71 06 18.343 W 24.12m 30.00m 40.00m 5.00m',
example.com. 300 IN SRV 20 0 5269 xmpp-server2.l.google.com.',
example.com. 300 IN ATMA 39246f00e7c9c0312000100100001234567800',
example.com. 300 IN NAPTR 100 10 "S" "SIP+D2U" "!^.*$!sip:customer-service@example.com!" _sip._udp.example.com.',
example.com. 300 IN KX 10 mx1.mrhost.ca.',
example.com. 300 IN CERT 3 0 0 TUlJQ1hnSUJBQUtCZ1FDcXlqbzNFMTU0dFU1Um43ajlKTFZsOGIwcUlCSVpGWENFelZvanVJT1BsMTM0by9zcHkxSE1hQytiUGh3Wk1UYVd4QlJpZHBFbUprNlEwNFJNTXdqdkFyLzFKWjhnWThtTzdCdTh1RUROVkNWeG5rQkUzMHhDSjhHRTNzL3EyN2VWSXBCUGFtU1lkNDVKZjNIeVBRRE4yaU45RjVHdGlIa2E2OXNhcmtKUnJ3SURBUUFCQW9HQkFJaUtDQ1NEM2FFUEFjQUx1MjdWN0JmR1BYN3lDTVg0OSsyVDVwNXNJdkduQjcrQ0NZZ09QaVQybmlpMGJPNVBBOTlnZnhPQXl1WCs5Z3llclVQbUFSc1ViUzcvUndkNGorRUlOVW1DanJSK2R6dGVXT0syeGxHamFOdGNPZU5jMkVtelQyMFRsekxVeUxTWGpzMzVlU2NQK0loeVptM2xJd21vbWtNb2d1QkFrRUE0a1FsOVBxaTJ2MVBDeGJCelU4Nnphblo2b0hsV0IzMUh4MllCNmFLYXhjNkVOZHhVejFzNjU2VncrRDhSVGpoSllyeDdMVkxzZDBRaVZJM0liSjVvUUpCQU1FN3k0aHg0SCtnQU40MEdrYjNjTFZGNHNpSEZrNnA2QVZRdlpzREwvVnh3bVlOdE4rM0txT3NVcG11WXZ3a3h0ajhIQnZtckxUYStXb3NmRDQwS1U4Q1FRQ1dvNmhob1R3cmI5bmdHQmFQQ2VDc2JCaVkrRUlvbUVsSm5mcEpuYWNxQlJ5emVid0pIeXdVOGsvalNUYXJIMk5HQzJ0bG5JMzRyS1VGeDZiTTJIWUJBa0VBbXBYSWZPNkZKL1NMM1RlWGNnQ1A5U1RraVlHd2NkdnhGeGVCcDlvRDZ2cElCN2FkWlgrMko5dzY5R0VUSlI0U3loSGVOdC95ZUhqWm9YdlhKVGc3ZHdKQVpEamxwL25wNEFZV3JYaGFrMVAvNGZlaDVNSU5WVHNXQkhTNlRZNW0xRmZMUEpybklHNW1FSHNidWkvdnhuQ1JmRUR4ZlU1V1E0cS9HUkZuaVl3SHB3PT0=',
example.com. 300 IN DNAME frobozz-division.acme.example.',
example.com. 300 IN OPT 5 0',
example.com. 300 IN APL 1:224.0.0.0/4 2:a0:0:0:0:0:0:0:0/8 !1:192.168.38.0/28',
example.com. 300 IN DS 21366 7 2 96eeb2ffd9b00cd4694e78278b5efdab0a80446567b69f634da078f0d90f01ba',
example.com. 300 IN SSHFP 2 1 123456789abcdef67890123456789abcdef67890',
example.com. 300 IN IPSECKEY 10 2 2 2001:db8:0:8002:0:0:2000:1 AQNRU3mG7TVTO2BkR47usntb102uFJtugbo6BSGvgqt4AQ==',
example.com. 300 IN RRSIG DNSKEY 7 1 86400 20100827211706 20100822211706 57970 gov. KoWPhMtLHp8sWYZSgsMiYJKB9P71CQmh9CnxJCs5GutKfo7Jpw+nNnDLiNnsd6U1JSkf99rYRWCyOTAPC47xkHr+2Uh7n6HDJznfdCzRa/v9uwEcbXIxCZ7KfzNJewW3EvYAxDIrW6sY/4MAsjS5XM/O9LaWzw6pf7TX5obBbLI+zRECbPNTdY+RF6Fl9K0GVaEZJNYi2PRXnATwvwca2CNRWxeMT/dF5STUram3cWjH0Pkm19Gc1jbdzlZVDbUudDauWoHcc0mfH7PV1sMpe80NqK7yQ24AzAkXSiknO13itHsCe4LECUu0/OtnhHg2swwXaVTf5hqHYpzi3bQenw==',
example.com. 300 IN NSEC dog.poo.com. A MX RRSIG NSEC TYPE1234',
example.com. 300 IN DNSKEY 256 3 7 AwEAAYCXh/ZABi8kiJIDXYmyUlHzC0CHeBzqcpyZAIjC7dK1wkRYVcUvIlpTOpnOVVfcC3Py9Ui/x45qKb0LytvK7WYAe3WyOOwk5klwIqRC/0p4luafbd2yhRMF7quOBVqYrLoHwv8i9LrV+r8dhB7rXv/lkTSI6mEZsg5rDfee8Yy1',
example.com. 300 IN DHCID AAIBY2/AuCccgoJbsaxcQc9TUapptP69lOjxfNuVAA2kjEA=',
example.com. 300 IN NSEC3 1 1 12 AABBCCDD b4um86eghhds6nea196smvmlo4ors995 NS DS RRSIG',
example.com. 300 IN NSEC3PARAM 1 0 1 D399EAAB',
example.com. 300 IN HIP 2 200100107B1A74DF365639CC39F1D578 AwEAAbdxyhNuSutc5EMzxTs9LBPCIkOFH8cIvM4p9+LrV4e19WzK00+CI6zBCQTdtWsuxKbWIy87UOoJTwkUs7lBu+Upr1gsNrut79ryra+bSRGQb1slImA8YVJyuIDsj7kwzG7jnERNqnWxZ48AWkskmdHaVDP4BcelrTI3rMXdXF5D rvs.example.com. another.example.com. test.domain.org.',
example.com. 300 IN TALINK c1.example.com. c3.example.com.',
example.com. 300 IN CDS 21366 7 2 96eeb2ffd9b00cd4694e78278b5efdab0a80446567b69f634da078f0d90f01ba',
example.com. 300 IN SPF "v=spf1 ip4:192.168.0.1/24 mx ?all"',
example.com. 300 IN TKEY gss.microsoft.com. 3 123456.',
example.com. 300 IN URI 10 1 "http://mrdns.com"',
example.com. 300 IN CAA 0 issue "ca.example.net; policy=ev"',
example.com. 300 IN TA 21366 7 2 96eeb2ffd9b00cd4694e78278b5efdab0a80446567b69f634da078f0d90f01ba',
example.com. 300 IN DLV 21366 7 2 96eeb2ffd9b00cd4694e78278b5efdab0a80446567b69f634da078f0d90f01ba',</pre>
            </p>
        </p>
    </form>
    </div>
    <!--
    ################# View / delete record 
    -->
    <h3>
        <img src="./images/btn_dns_bg.gif" alt="Image" align="bottom" width="20" /> View / Delete a resource record (<a href="javascript:PrintId('recordDel');">Switch Manual</a>)
    </h3>
    <div id="recordDel">
        <form style="font-family: Arial;" method="get" action="#" name="DynamicDNSUpdate">
            <input type="hidden" name="todo" value="recordDel" />
            <?php if ($debug) echo '<input type="hidden" name="debug" value="1" />'; ?>
            <p><label>Domain </label>
                <input name="domain" class="box" value="<?= $domain?>" disabled="disabled" /><label>Nameserver : </label>
                <input name="nameserver" class="box" value="<?= NS1; ?>" disabled="disabled" /></p>
            <p><label>Command : </label> 
                <input name="rr" class="box" value="" type="text" />
                <input name="domain" value="<?= $domain; ?>" type="hidden" /> 
                <input name="command" style="font-weight: bold;" value="Delete" type="submit" /> 
            </p>
        </form>
    </div>
    <?php
        $r = new Net_DNS2_Resolver(array('nameservers' => array(NS1)));
        // add a authenticate the request
        if (SIGNSIGO != NULL) {
            $r->signSIG0('my-key', SIGNSIGO);
        } else if (SIGNTSIG != NULL) {
            $r->signTSIG('my-key', SIGNTSIG);
        }
        // execute the query request for the google.com MX servers
        try {
            $result = $r->query($domain, 'AXFR');
        } catch(Net_DNS2_Exception $e) {
            echo '<p class="error">Failed: '. $e->getMessage() . '</p>';
        }
    ?>
    <form method="get" action="./dnsupdate2.php" name="DynamicDNSUpdate">
        <table border="1" cellpadding="3" cellspacing="0">
            <tr> <td>Action</td> 
                <td width="200">Domain</td> 
                <td width="120">TTL (Time-To-Live)</td> 
                <td width="80">Type</td> 
                <td width="250">Value</td>
            </tr>
            <?php
                foreach ($result->answer  as $rr) 
                {
                    switch($rr->type)
                    {
                    case 'A';
                    case 'AAAA';
                        echo '<tr class="record'.$rr->type.'">';
                            $rr->value=$rr->address;
                            printTdAction(get_object_vars($rr));
                            echo '<td>'.$rr->name.'</td>';
                            echo '<td>'.$rr->ttl.'</td>';
                            echo '<td>'.$rr->type.'</td>';
                            echo '<td>'.$rr->address.'</td>';
                        echo '</tr>';
                    break;
                    case 'CNAME';
                        echo '<tr class="record'.$rr->type.'">';
                            $rr->value=$rr->cname;
                            printTdAction(get_object_vars($rr));
                            echo '<td>'.$rr->name.'</td>';
                            echo '<td>'.$rr->ttl.'</td>';
                            echo '<td>'.$rr->type.'</td>';
                            echo '<td>'.$rr->cname.'</td>';
                        echo '</tr>';
                    break;
                    case 'MX';
                        echo '<tr class="record'.$rr->type.'">';
                            $rr->poid=$rr->preference;
                            $rr->value=$rr->exchange;
                            printTdAction(get_object_vars($rr));
                            echo '<td>'.$rr->name.'</td>';
                            echo '<td>'.$rr->ttl.'</td>';
                            echo '<td>'.$rr->type.'</td>';
                            echo '<td>Preference: <b>' . $rr->preference . '</b><br />';
                            echo 'Exchanger: <b>' . $rr->exchange . '</b></td>';
                        echo '</tr>';
                    break;
                    case 'NS';
                        echo '<tr class="record'.$rr->type.'">';
                            $rr->value=$rr->nsdname;
                            printTdAction(get_object_vars($rr));
                            echo '<td>'.$rr->name.'</td>';
                            echo '<td>'.$rr->ttl.'</td>';
                            echo '<td>'.$rr->type.'</td>';
                            echo '<td><b>' . $rr->nsdname . '</b></td>';
                        echo '</tr>';
                    break;
                    case 'TXT';
                        echo '<tr class="record'.$rr->type.'">';
                            echo '<td></td>';
                            echo '<td>'.$rr->name.'</td>';
                            echo '<td>'.$rr->ttl.'</td>';
                            echo '<td>'.$rr->type.'</td>';
                            echo '<td>';
                            foreach($rr->text as $txt)
                            {
                                echo $txt.'<br />';
                            }
                            echo '</td>';
                        echo '</tr>';
                    break;
                    case 'PTR';
                        echo '<tr class="record'.$rr->type.'">';
                            $rr->value=$rr->ptrdname;
                            printTdAction(get_object_vars($rr));
                            echo '<td>'.$rr->name.'</td>';
                            echo '<td>'.$rr->ttl.'</td>';
                            echo '<td>'.$rr->type.'</td>';
                            echo '<td>'.$rr->ptrdname.'</td>';
                        echo '</tr>';
                    break;
                    //~ case 'SRV';
                        //~ echo '<tr class="record'.$rr->type.'">';
                            //~ echo '<td></td>';
                            //~ echo '<td>'.$rr->name.'</td>';
                            //~ echo '<td>'.$rr->ttl.'</td>';
                            //~ echo '<td>'.$rr->type.'</td>';
                            //~ echo '<td>'.$rr->nsdname.'</td>';
                        //~ echo '</tr>';
                    //~ break;
                    case 'SOA';
                        echo '<tr class="record'.$rr->type.'">';
                            echo '<td></td>';
                            echo '<td>'.$rr->name.'</td>';
                            echo '<td>'.$rr->ttl.'</td>';
                            echo '<td>'.$rr->type.'</td>';
                            echo '<td>Serial: <b>' . $rr->serial . '</b><br />';
                            echo 'Refresh: <b>' . $rr->refresh . '</b><br />';
                            echo 'Retry: <b>' . $rr->retry . '</b><br />';
                            echo 'Expire: <b>' . $rr->expire . '</b><br />';
                            echo 'Minimum: <b>' . $rr->minimum . '</b></td>';
                        echo '</tr>';
                    break;
                    default;
                        echo '<tr class="recordUnknown">';
                            echo '<td></td>';
                            echo '<td>'.$rr->name.'</td>';
                            echo '<td>'.$rr->ttl.'</td>';
                            echo '<td>'.$rr->type.'</td>';
                            echo '<td>Record not support</td>';
                        echo '</tr>';
                    break;
                    }
                }
            ?>
        </table> 
    </form>
    <script type="text/javascript">
        changeForm('A')
        HideId('exampleCommand');
        HideId('recordAdd');
        HideId('recordDel');
        // No script compatible
	</script>
    
    <?php } ?>
    
    <p>This is a web-based dynamic DNS update program that can add, replace or delete DNS resource records in a master server.</p>
    <p>Your IP address:<b> <?php echo $_SERVER['REMOTE_ADDR']; ?></b></p>
    <p class="copyright"><?= LICENSE ?> </p> 
    <p class="version"><i>Project Homepage: <a href="http://code.google.com/p/dynamic-dnsupdate/"><?= VERSION ?></a></i></p>
    
    <?php } ?>
     
	</body>
</html>
