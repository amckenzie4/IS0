<?

// Author: Jon Belanger
// Date: Nov. 2004
// comments: native password hashing routines for PHP
//	     php4-mhash and php4-mcrypt are required
//	     NT and LM code was downloaded, not mine

// An NT hash is actually an all upper case MD4 hash
// parameters: plain text password string
// usage: $nt=nt_hash('n3wp@sswd');
function nt_hash($passwd) {
	return strtoupper(bin2hex(mhash(MHASH_MD4, unicodify($passwd))));
}

// Creates an LM hash using native function calls
// all LM hashes are two DES hashes of two 7 char length
// uppercase strings appended togother
// paramaters: plain text password string
// usage: $lm=lm_hash('n3wp@sswd');
function lm_hash($passwd) {
  $magic = pack('H16', '4B47532140232425');
  while (strlen($passwd) < 14) { $passwd .= chr(0); }
  $lm_pw = substr($passwd, 0, 14);
  $lm_pw = strtoupper($lm_pw);
  $key = convert_key(substr($lm_pw, 0, 7)) . 
    convert_key(substr($lm_pw, 7, 7));
  $td = mcrypt_module_open (MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
  mcrypt_generic_init ($td, substr($key, 0, 8) , '12352ff9');
  $enc1 = mcrypt_generic ($td, $magic);
  $td = mcrypt_module_open (MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
  mcrypt_generic_init ($td, substr($key, 8, 8) , '24os139x');
  $enc2 = mcrypt_generic ($td, $magic);
  return strtoupper(bin2hex($enc1 . $enc2));
}

// helper function for nt_hash and lm_hash
function unicodify($str) {
  $newstr = '';
  for ($i = 0; $i < strlen($str); ++$i) {
    $newstr .= substr($str, $i, 1) . chr(0);
  }
  return $newstr;
}

// helper function for lm_hash
function convert_key($in_key) {
  $byte = array();
  $result = '';
  $byte[0] = substr($in_key, 0, 1);
  $byte[1] = chr(((ord(substr($in_key, 0, 1)) << 7) & 0xFF) | 
    (ord(substr($in_key, 1, 1)) >> 1));
  $byte[2] = chr(((ord(substr($in_key, 1, 1)) << 6) & 0xFF) | 
    (ord(substr($in_key, 2, 1)) >> 2));
  $byte[3] = chr(((ord(substr($in_key, 2, 1)) << 5) & 0xFF) | 
    (ord(substr($in_key, 3, 1)) >> 3));
  $byte[4] = chr(((ord(substr($in_key, 3, 1)) << 4) & 0xFF) | 
    (ord(substr($in_key, 4, 1)) >> 4));
  $byte[5] = chr(((ord(substr($in_key, 4, 1)) << 3) & 0xFF) | 
    (ord(substr($in_key, 5, 1)) >> 5));
  $byte[6] = chr(((ord(substr($in_key, 5, 1)) << 2) & 0xFF) | 
    (ord(substr($in_key, 6, 1)) >> 6));
  $byte[7] = chr((ord(substr($in_key, 6, 1)) << 1) & 0xFF);
  for ($i = 0; $i < 8; $i++) {
    $byte[$i] = set_odd_parity($byte[$i]);
    $result .= $byte[$i];
  }
  return $result;
}

// helper function for convert_key
function set_odd_parity($byte) {
  $parity = 0;
  $ordbyte = '';
  $ordbyte = ord($byte);
  for ($i = 0; $i < 8; $i++) {
    if ($ordbyte & 0x01) {$parity++;}
    $ordbyte >>= 1;
  }
  $ordbyte = ord($byte);
  if ($parity % 2 == 0) {
    if ($ordbyte & 0x01) {
      $ordbyte &= 0xFE;
    } else {
      $ordbyte |= 0x01;
    }
  }
  return chr($ordbyte);
}

// sha hash using native mhash call
// parameters: plain text password string
// usage: $sha=sha_hash('n3wp@sswd');
function sha_hash($password) {
	return "{SHA}".base64_encode(mhash(MHASH_SHA1,$password));
}

// salted sha hash using native mhash call
// parameters: plain text password string
// usage: $ssha=ssha_hash('n3wp@sswd');
function ssha_hash($password) {
	mt_srand((double)microtime()*1000000);
	$salt = mhash_keygen_s2k(MHASH_SHA1, $password, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
	$hash = "{SSHA}".base64_encode(mhash(MHASH_SHA1, $password.$salt).$salt);
	return $hash;
}

// md5 hash using native mhash call. PHP md5() function returns on base64
// so it can't be used
// parameters: plain text password string
// usage: $md5=md5_hash('n3wp@sswd');
function md5_hash($password) {
	return "{MD5}".base64_encode(mhash(MHASH_MD5,$password));
}

// salted md5 hash using native mhash call
// parameters: plain text password string
// usage: $smd5=smd5_hash('n3wp@sswd');
function smd5_hash($password) {
	$salt = rfc2440_salted_s2k($password, 8);
	$hash = "{SMD5}".base64_encode(mhash(MHASH_MD5, $password.$salt).$salt);
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


?>
