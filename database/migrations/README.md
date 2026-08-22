# Migracoes do banco

Crie um arquivo SQL novo para cada alteracao de estrutura ou dados:

```text
AAAAMMDD_NNN_descricao.sql
```

Exemplo: `20260818_001_adicionar_indice_desenhos.sql`.

Depois execute `./atualizar-banco.sh`. O script faz backup antes da primeira
migracao pendente e registra os arquivos aplicados em `wl_schema_migrations`.
Nunca edite uma migracao que ja foi aplicada; crie outro arquivo.
