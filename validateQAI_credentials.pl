#!/usr/bin/perl

if (scalar(@ARGV) != 2) { die 'Need 2 args'; }

use DBI;
require "/var/www/cgi-bin/QC_Report_Scripts/setup_oracle_authentication.pl";
my ($env_oracle_home, $host, $port, $sid, $user, $password) = activateOracle();
my $db=DBI->connect("dbi:Oracle:host=$host;sid=$sid;port=$port", $user, $password);

my ($login, $password) = @ARGV;
$salt = 'hatdance';
use Digest::SHA1 qw(sha1_hex);
$hash = sha1_hex($salt . "--" . $password . "--");

my $query = "SELECT login, password, email FROM specimen.qcs_users WHERE login = '$login' AND password = '$hash'";
my $sth = $db->prepare($query);
$sth->execute();

if (my @row = $sth->fetchrow_array()) { $sth->finish(); $db->disconnect(); print "SUCCESS"; }
else { $sth->finish(); $db->disconnect(); print "FAILURE"; }

exit;
