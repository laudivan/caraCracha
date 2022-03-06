#!/bin/bash
# --------------------------------------------------------- #
# Script de <objetivo do script>                            #
# Criado por Laudivan Freire de Almeida                     #
# Email: laudivan@gmail.com                                 #
# --------------------------------------------------------- #
source /srv/caracracha/etc/caracracha.conf

# Setando dados da sessao

USUARIO_SESSAO=${1}

IP_SESSAO=$(cat ${DIR_DB_USUARIOS}/${USUARIO_SESSAO}/ip)

MAC_SESSAO=$(cat ${DIR_DB_USUARIOS}/${USUARIO_SESSAO}/mac)

NOME_SESSAO=${USUARIO_SESSAO}

ARQ_SESSAO=${DIR_DB_SESSAO}/${NOME_SESSAO}

# SESSOES_ABERTAS=$(ls ${DIR_DB_SESSAO}/${USUARIO_SESSAO})

LOG=${DIR_DB_LOG}/${USUARIO_SESSAO}.log

if [ -n "${SESSOES_ABERTAS}" ]; then
	echo "ERROR: $(date +%d/%m/%Y-%R) ${USUARIO_SESSAO}; tentou acessar de dois ou mais hosts" >> ${LOG}
	exit 1
fi

# Iniciando sessao

# - Criando arquivo de sessao
mkdir ${ARQ_SESSAO}

printf "${IP_SESSAO}" > ${ARQ_SESSAO}/ip
printf "${MAC_SESSAO}" > ${ARQ_SESSAO}/mac

chown www-data:www-data -R ${ARQ_SESSAO}
chmod 777 -R ${ARQ_SESSAO}

# - Limpando as CHAINS de cada usuário
iptables -t nat -F ${NOME_SESSAO}
iptables -t filter -F ${NOME_SESSAO}

#conectividade
iptables -t nat -A ${NOME_SESSAO} -d 200.201.174.207  -j ACCEPT
#Liberar Radio UOL
iptables -t nat -A ${NOME_SESSAO} -d 200.221.0.0/16 -j ACCEPT

iptables -t nat -A ${NOME_SESSAO} ! -d ${REDE_LAN} -p tcp --dport www -j REDIRECT --to-port ${PROXY_PORT}
iptables -t nat -A ${NOME_SESSAO} ! -d ${REDE_LAN} -p udp --dport www -j REDIRECT --to-port ${PROXY_PORT}

#Liberando acesso ao proxy
iptables -t filter -A ${NOME_SESSAO} -p tcp --dport ${PROXY_PORT} -j ACCEPT
iptables -t filter -A ${NOME_SESSAO} -p udp --dport ${PROXY_PORT} -j ACCEPT

# - Criando Log de sistema
echo "START: $(date +%d/%m/%Y-%R); ${USUARIO_SESSAO}; ${IP_SESSAO}; ${MAC_SESSAO}" >> ${LOG}
