<?php

// Author: Jon Belanger
// Date: Nov. 2004
// comments: native password hashing routines for PHP
//	     php4-mcrypt is required
//	     NT and LM code was downloaded, not mine

// An NT hash is actually an all upper case MD4 hash
// parameters: plain text password string
// usage: $nt=nt_hash('n3wp@sswd');
function nt_hash($passwd) {
	exec("util/md4sum.pl ".escapeshellarg($passwd),$arrOut);
	return strtoupper($arrOut[0]);
}

// LM hashes are two DES hashes of two 7 char length
// uppercase strings appended togother
// paramaters: plain text password string
// usage: $lm=lm_hash('n3wp@sswd');
function lm_hash($passwd) {
	exec("util/lm.pl ".escapeshellarg($passwd),$arrOut);
	return $arrOut[0];
}

// sha hash using native sha1 call
// parameters: plain text password string
// usage: $sha=sha_hash('n3wp@sswd');
function sha_hash($password) {
	return "{SHA}".base64_encode(hex2bin(sha1($password)));
}

// salted sha hash using native sha1 call
// parameters: plain text password string
// usage: $ssha=ssha_hash('n3wp@sswd');
function ssha_hash($password) {
	$salt = rfc2440_salted_s2k($password, 4);
	$hash = "{SSHA}".base64_encode(hex2bin(sha1($password.$salt)).$salt);
	return $hash;
}

// md5 hash using native md5 call.
// parameters: plain text password string
// usage: $md5=md5_hash('n3wp@sswd');
function md5_hash($password) {
	return "{MD5}".base64_encode(hex2bin(md5($password)));
}

// salted md5 hash using native md5 call
// parameters: plain text password string
// usage: $smd5=smd5_hash('n3wp@sswd');
function smd5_hash($password) {
	$salt = rfc2440_salted_s2k($password, 4);
	$hash = "{SMD5}".base64_encode(hex2bin(md5($password.$salt)).$salt);
	return $hash;
}

// returns salted_s2k conformed key
// parameters: plain text password, number of bytes to return
// usage: $salt=rfc2440_salted_s2k('n3wp@sswd',4);
function rfc2440_salted_s2k($pass, $bytes) {
	// reset the timer
        mt_srand((double)microtime()*1000000);
        // get a random salt (8 bytes)
	$salt=substr(pack('h*',md5(mt_rand())),0,8);
        // prepend the above salt to the passed in password
	// hash the new string and return the first number
	// of requested bytes
	return substr(pack('H*', md5($salt.$pass)),0,$bytes);
}

// returns hex formatted string has raw binary
// parameters: hex formatted string
// usage: $binstring=hex2bin('aa00ffeedd33');
function hex2bin($source) {
	for ($i=0;$i<strlen($source);$i=$i+2) {
                $bin .= chr(hexdec(substr($source,$i,2)));
        }
        return $bin;
}

?>
