# fortigate-dns
Set up DNS services on a fortigate interface.
(I use a virtual interface and allow access from all internal VLAN)

Enable API access 

Place this website in a secure location, do not put it on the web!
The intent is to manage internal DNS names as simply as possible.

You'll need PHP capable webserver.  Drop these files in the folder and edit

firewall_config.php
   --  IP address of the API interface
   --  Your API key

Look at CloudFlareD if external access is necessary.

ToDo:

1)  Login for access
2)  Better documentation
