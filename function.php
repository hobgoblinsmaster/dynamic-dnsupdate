<?php
//------------------------------------------------------------------------------
// Web-based Dynamic DNS Update
// @author: Jaeyoun Kim / Calyce.fr & Novius (http://www.novius.com)
// @homepage: http://code.google.com/p/dynamic-dnsupdate/
// @version: 2.0
// @date: 07/2012
//------------------------------------------------------------------------------

function printTdAction($rr) {
    echo '<td class="action"><a Onclick="deleteRecord(\''.formatRR($rr).'\', \''.$_SERVER['REQUEST_URI'].'&todo=recordDel&rr='.urlencode(formatRR($rr)).'\')" onMouseOver="this.href=\'#\'" " href="'.$_SERVER['REQUEST_URI'].'&todo=recordDel&rr='.urlencode(formatRR($rr)).'" title="Delete">X</a></td>';
}

function formatRR($data)
{
    if ($data['name'] != NULL && $data['name'] != '@') {
        $data['name'] = $data['name'] . '.' . $data['domain'];
    } 
    #print_r($data);
    switch($data['type'])
    {
        case 'MX';
            return $data['name'] . '. ' . $data['ttl'] . ' IN ' . $data['type'] . ' ' . $data['poid'] . ' ' . $data['value'];
        break;
        default;
            # A / AAAA / CNAME / TXT / NS
            return $data['name'] . '. ' . $data['ttl'] . ' IN ' . $data['type'] . ' ' . $data['value'];
        break;
    }
    
}

function formAction($get) {
    // create a new Updater object
    $u = new Net_DNS2_Updater($get['domain'], array('nameservers' => array(NS1)));
    try {
        switch ($get['todo']) {
            case 'recordAdd':
                $record = Net_DNS2_RR::fromString($get['rr']);
                // add the record
                $u->add($record);
            break;
            case 'recordAddWizard':
                $cmd=formatRR($get);
                $record = Net_DNS2_RR::fromString($cmd);
                // add the record
                $u->add($record);
            break;
            case 'recordDel':
                $record = Net_DNS2_RR::fromString($get['rr']);
                // add the record
                $u->delete($record);
            break;
        }
        // add a TSIG to authenticate the request
        if (SIGNSIGO != NULL) {
            $r->signSIG0('my-key', SIGNSIGO);
        } else if (SIGNTSIG != NULL) {
            $r->signTSIG('updatekey', SIGNTSIG);
        }
        // execute the request
        $u->update();
        echo '<p class="success">Update Result: Dynamic update is successful.</p>';
    } catch(Net_DNS2_Exception $e) {
        echo '<p class="error">Failed: '. $e->getMessage() . '</p>';
    }
    if (isset($get['rr'])) {
        echo '<p><pre>'.$get['rr'].'</pre></p>';
    } else {
        echo '<p><pre>'.$cmd.'</pre></p>';
    }
}

?>
