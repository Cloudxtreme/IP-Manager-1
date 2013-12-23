#!/usr/bin/perl
$|++;


use CGI::Carp fatalsToBrowser;
use CGI ':standard';
use Net::SNMP qw(oid_lex_sort);
use List::Util qw(sum);
use DBI;

### Connect to database ###
my $dbh = DBI->connect("dbi:mysql:ipmanager","root","") or die("Can't connect to the IPM database @ localhost: $dbh::errstr");

#####################
# Start HTML Output #
#####################

print header;
print start_html(-title=>'IP Manager 5.0 - Scanning device...',
					-style=>{'src'=>'../css/ipm5.css'});

my $device = param('device');

my $deviceQry = $dbh->prepare("SELECT * FROM portsdevices WHERE portsdevices.id = $device");
$deviceQry->execute() or die("Cannot execute device query on IPM database: $dbh");
$deviceQry->bind_columns(\$dev_id,\$dev_name,\$dev_addr,\$dev_descr,\$dev_type,\$dev_group,\$dev_ip,\$dev_community);
$deviceQry->fetch();

print "<img src='../images/ipm_banner.gif' alt='Logo'>";
$|++;

print h1, "Populating device cache [$dev_name]";
print h3, "Please wait...";
$|++;

if ($dev_ip eq "" || $dev_community eq "") {
	
	print h3 ({-class=>'text_red'},'Error: There is no management IPv4 address or SNMP community for the device.  Please update the device details and scan the device again');
	print p, "<a href=\"javascript:window.close()\">Close window</a>";
	print end_html();
	exit();

}

my $version = "2c";
	
print p, "Establishing SNMP connection to $dev_ip...";
$|++;

($session, $error) = Net::SNMP->session(
	-hostname      => $dev_ip,
	-version       => $version,
	-community     => $dev_community
	);
	
if ($error) {
	print h3 ({-class=>'text_red'}, "Error: Could not connect to device ($error)");
	print end_html();
	exit();
}

my $ifTable = '1.3.6.1.2.1.2.2.1.1';
my $ifDescr = '1.3.6.1.2.1.2.2.1.2';
my $ifName = '1.3.6.1.2.1.31.1.1.1.1';
#my $entTable = '1.3.6.1.2.1.47.1.3.3.1.1';
#my $entDescr = '1.3.6.1.2.1.47.1.1.1.1.2';
#my $ipAddrTable = '1.3.6.1.2.1.4.20.1.2';
#my $ipAddr = '1.3.6.1.2.1.4.20.1.1';

print h3, "Polling interfaces...";
print br;
$|++;

if (defined($result = $session->get_table(-baseoid => $ifTable))) {
	foreach (oid_lex_sort(keys(%{$result}))) {
		
	$ifDescr1 = $ifDescr.'.'.$result->{$_};
	$ifName1 = $ifName.'.'.$result->{$_};
	
	$result1 = $session->get_request(
       		-varbindlist    => [$ifDescr1]);
    
    $result2 = $session->get_request(
       		-varbindlist    => [$ifName1]);
			
	print br, "$result1->{$ifDescr1} $result2->{$ifName1}\n";		
	$|++;

	#$updateQry = "INSERT INTO interfaces (device,ifindex,ifdescr) VALUES ('$deviceid','$result->{$_}','$result1->{$ifDescr1}') ON DUPLICATE KEY UPDATE ifdescr='$result1->{$ifDescr1}'";
	#$dbh->do($updateQry);

	}
	
}

print end_html();
