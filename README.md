# phpWOL
Forked from [castab/phpWOL](https://github.com/castab/phpWOL). Replaced the backend with PHP_WOL developed by [Radovan Janjic](https://radovanjanjic.com)
## Modifications
- Replace backend to be able to send Magic Packet to xxx.xxx.xxx.255 broadcast address.
## Requirements
- PHP
  - Sockets module
- WebServer
## A web-driven Wake-on-LAN Service
php driven Wake on LAN webapp for remote computer startup

The purpose of this webapp is to remotely turn on computers that have WoL enabled.

Many routers disable/ignore the broadcast address of a subnet that originate from outside of the local network.  To circumvent this, a PHP server placed within the network is able to receive a command in the form of a submission and then dynamically generate a WoL packet to specific computers in the same subnet.

Much of the credit goes to, Toni Uebernickel <tuebernickel@whitestarprogramming.de>, who coded the entire WoL packet generator function.  I only wrapped it into a web page and added a few more tweaks:

1. Some authentication (not completely secure, but better than nothing)
2. A GUI in the form of a webpage
