 <?php
/******************************************************************
 * 
 * Projectname:   PHP_WOL - PHP Wake On Lan
 * Version:       1.0
 * Author:        Radovan Janjic <hi@radovanjanjic.com>
 * Last modified: 29 09 2014
 * Copyright (C): 2014 IT-radionica.com, All Rights Reserved
 * 
 * GNU General Public License (Version 2, June 1991)
 *
 * This program is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 * 
 ******************************************************************/
/** Example:
 * PHP_WOL::send('192.168.1.2', '01:23:45:67:89:ab', 9);
 */

class PHP_WOL {
	
	/** Socket
	 * @var resource
	 */
	private static $socket = 0;
	
	/** Error code
	 * @var integer
	 */
	private static $errCode = 0;
	
	/** Error description
	 * @var string
	 */
	private static $errMsg = NULL;
	
	/** Send WOL package
	 * @param	string		$addr		- IP address
	 * @param	string		$mac		- Media access control address (MAC)
	 * @param	integer		$port		- Port number at which the data will be sent
	 * @return	boolean
	 */
	public static function send($addr, $mac, $port = 9) {
		// Throw exception if extension is not loaded
		if (!extension_loaded('sockets')) {
			self::throwError("Error: The sockets extension is not loaded!");
		}

		// Check if $addr is valid IP, if not try to resolve host
		if (!filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			// Try to get the IPv4 address of a given host name
			$originalAddr = gethostbyname($addr);
			if ($originalAddr == $addr) {
				self::throwError('Error: Domain name is unresolvable or IP address is invalid!');
			} else {
				$addr = $originalAddr;
			}
		}
		
		$macHex = str_replace(array(':', '-'), NULL, $mac);
		
		// Throw exception if mac address is not valid
		if (!ctype_xdigit($macHex) || strlen($macHex) != 12) {
			self::throwError('Error: Mac address is invalid!');
		}
		
		// Magic packet
		$packet = str_repeat(chr(255), 6) . str_repeat(pack('H12', $macHex), 16);
		
		// Send to the broadcast address using UDP
		self::$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		
		if (is_resource(self::$socket)) {
		
			// Set socket option
			if (!socket_set_option(self::$socket, SOL_SOCKET, SO_BROADCAST, TRUE)) {
				self::throwError();
			}
			
			// Send magic packet
			if (socket_sendto(self::$socket, $packet, strlen($packet), 0, $addr, $port) !== FALSE) {
				socket_close(self::$socket);
				return TRUE;
			}
		}
		self::throwError();
	}
	
	/** Throw Last Error
	 * @param	string		$msg	- Error message
	 * @return	void
	 */
	private static function throwError($msg = NULL) {
		// Take last error if err msg is empty
		if (empty($msg)) {
			self::$errCode = socket_last_error(self::$socket);
			self::$errMsg = socket_strerror(self::$errCode);
			$msg = "Error (" . self::$errCode . "): " . self::$errMsg;
		}
		throw new Exception($msg);
	}
}

function wakeUp($mac, $broadcastIP, &$msg)
{
    try
    {
    PHP_WOL::send($broadcastIP,$mac);
    }
    catch (SocketConnectionException $e)
    {
    // socket connection failed
    //echo 'The socket connection could not be established.', "\n", $e;
    $msg = 'The socket connection could not be established.'.$e;
    }
    catch (WakeOnLANException $e)
    {
    // wake on lan request failed
    //echo 'The Wake On LAN packet was not sent properly.', "\n", $e;
    $msg = 'The Wake On LAN packet was not sent properly.';
    }
}
