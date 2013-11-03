### ipsecFritzer

Just use the native iOS and Mac OS X VPN clients to connect your apple device to your integrated FritzBox!IPSEC-Server. Normally, this is a big and "bad-ass-not-documented" pain. Now, you can use ipsecFritzer - a small Python console script - to generate the whole FritzBox!-VPN-configuration.

#### Get started

1. Update config.py with your local network range, accounts you want to have generated, passwords and everything else
2. Use your terminal to generate your specific FritzBox!-VPN-Config in the output/-Folder (just type 'python ipsecFritzer.py')
3. Upload your generated vpn-configuration to your fritzbox, connect your iOS and Mac OS X devices (Cisco vpn mode) and enjoy :)

#### Links

1. How to connect your iPhone/Mac OS X device to your FritzBox! VPN server: [Tutorial by AVM](http://www.avm.de/de/Service/Service-Portale/Service-Portal/VPN_Interoperabilitaet/16206.php)
2. Php version of this script: [https://github.com/brgmn/ipsecfritzer](http://github.com/brgmn/ipsecfritzer)
