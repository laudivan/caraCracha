#!/bin/bash
source /srv/caracracha/etc/caracracha.conf

rm -fr ${DIR_DB_USUARIOS}/${1}  ${DIR_DB_SESSAO}/${1}

/srv/caracracha/bin/aplicar_alteracoes.sh > /dev/null

