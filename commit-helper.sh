#!/bin/bash

# Verifica se o usuário está autenticado no Cody
if ! cody auth whoami &>/dev/null; then
  echo "Autenticando no Cody..."
  export SRC_ENDPOINT="https://sourcegraph.com/.api/graphql?kauedemagalhaes27-qbufc"
  export SRC_ACCESS_TOKEN="sgp_fd1b4edb60bf82b8_7da83c1cdafe906c93dc8b9f59f72b0ca9346cd9"
  cody auth login
else
  echo "Já autenticado no Cody."
fi

# Executa composer fix e interrompe caso falhe
echo "Executando composer fix..."
if ! composer fix; then
  echo "Erro: npm run fix falhou. Corrija os erros antes de continuar."
  exit 1
fi

# Adiciona as mudanças ao Git
echo "Adicionando mudanças ao Git..."
git add .

# Captura o contexto extra (se fornecido)
extra_context="$1"

# Gera a mensagem de commit automaticamente
echo "Gerando mensagem de commit com Cody..."
commit_message=$(git diff --cached | cody chat --stdin -m "Write a nice commit message for this diff using semantic commits and be elegant. $extra_context")

# Remove caracteres de controle e formata a saída corretamente
commit_message=$(echo "$commit_message" | sed -n '/```/,/```/p' | sed '1d;$d')

# Verifica se a mensagem foi gerada com sucesso
if [[ -z "$commit_message" ]]; then
  echo "Erro: Não foi possível gerar a mensagem de commit."
  exit 1
fi

echo "Mensagem de commit gerada:"
echo "--------------------------"
echo "$commit_message"
echo "--------------------------"

# Cria um arquivo temporário para armazenar a mensagem do commit
temp_file=$(mktemp)

# Escreve a mensagem gerada no arquivo temporário
echo "$commit_message" > "$temp_file"

# Abre o editor para revisar/editar a mensagem antes de commitar
git commit --no-verify --edit --file="$temp_file"

# Remove o arquivo temporário após o commit
rm "$temp_file"
