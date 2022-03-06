#!/bin/bash
# --------------------------------------------------------- #
# Script de <objetivo do script>                            #
# Criado por Laudivan Freire de Almeida                     #
# Email: laudivan@psl-pe.softwarelivre.org                  #
# --------------------------------------------------------- #
# Ultima alteração em 19 de agosto de 2008

. /srv/caracracha/etc/caracracha.conf

# Setando dados da sesso a ser excluda

NOME_SESSAO=${1}
IP=$(cat ${DIR_DB_SESSAO}/${NOME_SESSAO}/ip)
MAC=$(cat ${DIR_DB_SESSAO}/${NOME_SESSAO}/mac)
ARQ_SESSAO=${DIR_DB_SESSAO}/${NOME_SESSAO}
LOG=${DIR_DB_LOG}/${USUARIO_SESSAO}.log

#Limpando regras do cliente
iptables -t nat -F ${NOME_SESSAO}
iptables -t filter -F ${NOME_SESSAO}

#Redirecionando conexões para os servidor para exibir a autenticação
iptables -t nat -A ${NOME_SESSAO} -j DNAT --to ${IP_LAN}

# Excluindo arquivo de sessao
rm -fr ${ARQ_SESSAO}

echo "END: $(date +%d/%m/%Y-%R); ${USUARIO_SESSAO}; ${IP_SESSAO}; ${MAC_SESSAO}" >> ${LOG}
