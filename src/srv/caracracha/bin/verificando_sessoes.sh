#!/bin/bash
# --------------------------------------------------------- #
# Script de <objetivo do script>                            #
# Criado por Laudivan Freire de Almeida                     #
# Email: laudivan@psl-pe.softwarelivre.org                  #
# --------------------------------------------------------- #
source /srv/caracracha/etc/caracracha.conf

for NOME_SESSAO in `ls ${DIR_DB_SESSAO}`; do
	IP_SESSAO=`cat ${DIR_DB_SESSAO}/${NOME_SESSAO}/ip`
	NOME_SESSAO=${NOME_SESSAO}

	if !(arping -I ${DEV_LAN} -c 5 ${IP_SESSAO} > /dev/null ) ; then
		sh ${DIR_BIN}/excluir_sessao.sh "${NOME_SESSAO}" > /dev/null
	fi	
done
