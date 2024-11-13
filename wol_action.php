<?php
require_once __DIR__ . '/winrm.php';


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
	
	private static $socket = 0;
	
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
		$addr = explode('.', $addr);
		$addr[3] = '255';
		$addr = implode('.', $addr);
		// self::throwError($addr);
		$macHex = str_replace(array(':', '-'), '', $mac);
		
		// Throw exception if mac address is not valid
		if (!ctype_xdigit($macHex) || strlen($macHex) != 12) {
			self::throwError('Error: Mac address is invalid!');
		}
		
		// Magic packet
		$packet = str_repeat(chr(255), 6) . str_repeat(pack('H12', $macHex), 16);
		
		// Send to the broadcast address using UDP
		self::$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		
		if (self::$socket !== false) {
		
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
	
	private static function throwError($msg = NULL) {
		if (empty($msg)) {
			$msg = "Error (" . socket_last_error(self::$socket) . "): " . socket_strerror(socket_last_error(self::$socket));
		}
		throw new Exception($msg);
	}
}

function wakeUp($mac, $broadcastIP)
{
    PHP_WOL::send($broadcastIP, $mac);
    return "Wake-up command sent successfully.";
}

function restartComputer($ipAddress, $hostName)
{
    return executeShutdownCommand($ipAddress, $hostName, '/r');
}

function shutdownComputer($ipAddress, $hostName)
{
    return executeShutdownCommand($ipAddress, $hostName, '/s');
}

function executeShutdownCommand($ipAddress, $hostName, $shutdownFlag)
{
    $command = "shutdown {$shutdownFlag} /t 0";
    // $command = "ipconfig";
    $client = new WinRmClient($ipAddress, $hostName, WINRM_PASSWORD);
    $result = $client->execute_command($command);
    
    if (strpos($result, 'ERROR') !== false) {
        throw new Exception("Failed to send " . ($shutdownFlag === '/r' ? 'restart' : 'shutdown') . " command. Error: " . $result);
    }
    
    return ($shutdownFlag === '/r' ? 'Restart' : 'Shutdown') . " command sent successfully. Result: " . $result;
}

$data = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json');

$response = new stdClass();

try {
    if (!isset($data['host']) || !isset($data['action'])) {
        throw new Exception('Invalid request.');
    }

    $host = $data['host'];
    $action = $data['action'];

    switch ($action) {
        case 'Wake':
            $message = wakeUp($host['macAddress'], $host['ipAddress']);
            break;
        case 'Restart':
            $message = restartComputer($host['ipAddress'], $host['hostName']);
            break;
        case 'Shutdown':
            $message = shutdownComputer($host['ipAddress'], $host['hostName']);
            break;
        default:
            throw new Exception('Invalid action.');
    }

    $response->status = "OK";
    $response->message = $message;
} catch (Exception $e) {
    $response->status = "Error";
    $response->error = $e->getMessage();
}

echo json_encode($response);
?>

