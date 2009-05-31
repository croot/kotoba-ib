#!/usr/bin/perl -w
# stress-test.pl: utility to make stress test for database (and other)
# using sample web-interface commited at revision 67-68
# you better ran this on other host if you like your cpu
use strict;
use IO::Socket;
use Time::HiRes qw(usleep);
use POSIX ":sys_wait_h";
use LWP::UserAgent;

## user-configured variables here
# number of child processes
my $children = 10;
# number of posts each child will make
my $posts = 100;

my $wipe_data = {
base => "http://localhost/new_database_kotoba",
board => 1,
page => 0,
post => 'post.php',
showboard => 'showboard.php'
};


## script variables. do not modify. нечего увидеть здесь.
$| = 1;

# ctrl-c flag
our $f = 1;

sub l {
	local $, = "|";
	print @_, "\n";
}

# create new thread
sub newthread {
	my $ua = shift;
	my $data = shift;
	my $r = HTTP::Request->new('GET', sprintf("%s/%s?action=newthread&board=%d&text=wipe", 
			$data->{base}, $data->{post}, $data->{board}));
	my $res = $ua->request($r);
	if($res->is_success) {
		l "thread created";
	}
	else {
		l $res->status_line;
	}
}
# create post in thread
sub post {
	my $ua = shift;
	my $thread = shift;
	my $data = shift;
	my $r = HTTP::Request->new('GET', sprintf("%s/%s?action=post&board=%d&thread=%d&text=wipepost", 
			$data->{base}, $data->{post}, $data->{board}, $thread));
	my $res = $ua->request($r);
	if($res->is_success) {
		l "posted in thread $thread";
	}
	else {
		l $res->status_line;
	}
}
# get threads on board and page
sub getthreads {
	my $ua = shift;
	my $data = shift;
	my $threads = [];
	my $r = HTTP::Request->new('GET', sprintf("%s/%s?id=%d&page=%d", 
			$data->{base}, $data->{showboard}, $data->{board}, $data->{page}));
	my $res = $ua->request($r);
	if($res->is_success) {
		my $c = $res->content;
		while($c =~ m/#(\d+):/g) {
			push @{$threads}, $1;
		}
	}
	else {
		l $res->status_line;
	}
	return $threads;
}

# child process
sub child {
	my $posts = shift;
	my $data = shift;

	my $ua = LWP::UserAgent->new;
	while($posts and $f) {
		srand($$*$posts);
		my $newthread = int(rand(100));
		# do not want create threads too often
		if($newthread > 10 and $newthread < 20) {
			newthread $ua, $data;
		}
		else {
			my $threads = getthreads($ua, $data);
			# random thread on board page 0
			my $randomthread = int(rand(scalar @$threads));
			post($ua, $threads->[$randomthread], $data);
		}
		usleep 1000_000;

		$posts --;
	}
}

$SIG{INT} = sub {$f = 0};
# $fnum: fork number
for(my $fnum = 0; $fnum < $children; $fnum ++) {
	my $pid;
	if (!defined($pid = fork())) {
		die "cannot fork: $!"; 
	} 
	elsif ($pid == 0) {
		child $posts, $wipe_data;
		exit; 
	}
	print "parent: run child process $pid\n";
}

# wait child processes to finish
my $kid;
do {
	$kid = waitpid(-1, WNOHANG);
	usleep 10;
} until $kid > 0;

print "\n", '=' x 15, "\n", "ALL DONE\n", '=' x 15, "\n";

