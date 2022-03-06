#!/bin/bash

source /srv/caracracha/etc/caracracha.conf

${DIR_BIN}/gerar_dhcp.php

${DIR_BIN}/rc.firewall restart

${DHCPD} restart
