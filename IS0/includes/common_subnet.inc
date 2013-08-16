<?php

# This is basically just a set of tools for dealing with subnets.  Beginning 
# with a CIDR subnet definition (a.b.c.d/e) I wanted to be able to figure out
# how many IPs there were, what they were, what the subnet mask was, and so on.

# First, a function to be called from other functions.

class subnet
 { # Begin class subnet
	private $cidr; 		# The CIDR format network definition
	private $ip_array;	# An array with the IP and the CIDR netmask
	private $ip;			# The IP, as a decimal
	private $netmask;		# The binary netmask
	private $network;			# The network number
	private $bc;			# beats me, but it's used all over the place.

#private $bin = '';
	public function __construct($cidr)
	 { # Begin __construct
		$this->cidr = $cidr;
		$this->ip_array = explode('/', $this->cidr);
		$this->ip = ip2long($this->ip_array[0]);
		
		# NOTE:  I'm not actually sure how this does what it does.  I pulled it 
		# verbatim from someone else's code, and haven't figure it out yet.
		# It basically creates a binary version of the network number, but I 
		# don't know how.
		for($i = 1; $i <= 32; $i++)
			{ $this->bin .= $this->ip_array[1] >= $i ? '1' : '0'; }
		$this->ip_array[1] = bindec($this->bin);
		$this->netmask = ip2long($this->ip_array[1]);
		$this->network = ($this->ip & $this->netmask);
		# This next one needs explanation.  The OR of $network and ~netmask
		# winds up a 64 bit negative number.  A 32 bit binary string of all
		# ones is equal to binary 4294967295, so we AND that with the original 
		# OR, and end up with a 32 bit positive number again.  There's probably 
		# a better way to do this, but I don't know it.
		$this->bc = ($this->network | (~$this->netmask)) & 4294967295;
	 } # End __construct

	public function generate_mask()
	 { # Begin function generate_mask
		$ip_mask = long2ip($this->netmask);
		return $ip_mask;
	 } # End function generate_mask

	# Return the first ip in the range as an IP
	public function first_ip()
	 { # Begin function first_ip
		$first = long2ip($this->network + 1);
		return $first;
	 } # End function first_ip

	# Return the first ip in the range as a long
	public function first_long()
	 { # Begin function first_long
		$first = $this->network + 1;
		return $first;
	 } # End function first_long

	# Return the last ip in the range as an IP
	public function last_ip()
	 { # Begin function last_ip
		$last = long2ip($this->bc - 1);
		return $last;
	 } # End function last_ip

	# Return the last ip in the range as a long
	public function last_long()
	 { # Begin function last_long
		$last = $this->bc - 1;
		return $last;
	 } # End function last_long

	# Return the total possible number of addresses
	public function number_of_addresses()
	 { # Begin function number_addresses
		$number = $this->last_long() - $this->first_long() - 1;
		return $number;
	 } # End function number_addresses

 } # End class subnet

?>
