## Description ##
  * This is a simple web-based dynamic DNS update program that can add, replace or delete DNS resource records in a master server. (very user friendly)
  * This program is developed based on a PEAR (PHP Extension and Application Repository)'s package Net\_DNS without using a PHP's external program execution function (system).
  * This program is very useful for individuals or SMEs' DNS administers who want to manage their DNS resource records easily.

### Prerequisites ###
  * Apache 2.x.x
  * PHP 5.x.x
  * PEAR Net\_DNS2 http://pear.php.net/package/Net_DNS2
    * No Database Backend Required

### History ###
  * Rev 1. 2008-06-01 : Added Authcode function
  * Rev 2. 2008-06-06 : Added TXT RR editing function

## Screen Shots ##
| ![http://www.cslab.net/dnsupdate/images/nsupdate.gif](http://www.cslab.net/dnsupdate/images/nsupdate.gif) |
|:----------------------------------------------------------------------------------------------------------|
| ![http://www.cslab.net/dnsupdate/images/nsupdate2.gif](http://www.cslab.net/dnsupdate/images/nsupdate2.gif) |

## Commd line DNS Update ##
  * You can update DNS entries with either of these two command lines in Linux.
```
wget -O - --http-user=dnsupdate --http-passwd=AUTHCODE 'http://cslab.net/dnsupdate/dynamic.php?domain=freeserver.kr&host=test1&ttl=60&type=A&type_value=192.168.0.1'
```
```
lynx -source -auth=dnsupdate:AUTHCODE 'http://cslab.net/dnsupdate/dynamic.php?domain=freeserver.kr&host=test1&ttl=60&type=A&type_value=192.168.0.1'
```

## Libraries ##
  * http://pear.php.net/package/Net_DNS2

## Similar softwares ##
  * http://www.unicom.com/sw/web-nsupdate

## SVN ##
  * svn checkout http://dynamic-dnsupdate.googlecode.com/svn/trunk/ dynamic-dnsupdate-read-only
