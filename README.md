# phpWOL
Forked from [castab/phpWOL](https://github.com/castab/phpWOL). Replaced the backend with PHP_WOL developed by [Radovan Janjic](https://radovanjanjic.com)

## Modifications
- Replace backend to be able to send Magic Packet to xxx.xxx.xxx.255 broadcast address.
- Added shutdown and restart functionality using a lightweight WinRmClient (based on [vmatt/phpwinrm](https://github.com/vmatt/phpwinrm)).

## Features
- Wake-on-LAN: Turn on computers remotely that have WoL enabled.
- Shutdown: Remotely shut down Windows computers.
- Restart: Remotely restart Windows computers.

## Requirements
- PHP
  - Sockets (for Wake-on-LAN)
  - CURL (for WinRM operations)
- WebServer
- Windows computers with WinRM enabled (for shutdown and restart operations)

## A web-driven Wake-on-LAN and Remote Management Service
This webapp allows for remote computer startup, shutdown, and restart operations.

The Wake-on-LAN functionality works by having a PHP server within the network receive a command and then dynamically generate a WoL packet to specific computers in the same subnet. This circumvents the issue of many routers disabling/ignoring broadcast addresses from outside the local network.

The shutdown and restart functionality uses Windows Remote Management (WinRM) to securely execute commands on remote Windows machines.

## Credits
- Toni Uebernickel <tuebernickel@whitestarprogramming.de>: Original WoL packet generator function
- [Radovan Janjic](https://radovanjanjic.com): PHP_WOL backend
- [vmatt/phpwinrm](https://github.com/vmatt/phpwinrm): Lightweight WinRM client

## Security Note
While basic authentication is implemented, it's recommended to further secure this application, especially when exposed to the internet. Consider implementing stronger authentication methods and ensuring all communications are encrypted.
