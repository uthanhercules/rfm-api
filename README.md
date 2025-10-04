# Rodando pela primeira vez

O projeto possui poucas tabelas, mas uma delas precisa de um seed. Para isso, foi criado um script que executa tudo o que é necessário para rodar a API sem problemas, precisando apenas sincronizar os dados.

## Passo a passo

1. **Clone o repositório:**

```bash
git clone git@github.com:uthanhercules/rfm-api.git
cd rfm-api
```

2. **Execute o setup inicial:**

```bash
npm run setup
```

Esse comando irá:

- Instalar os pacotes necessários;
- Rodar as migrations e criar o banco de dados;
- Preparar o ambiente para uso.

> **Importante:** Antes de rodar o setup, **crie e configure seu banco de dados no arquivo `.env`**.

3. **Rode o projeto:**

```bash
npm run start
```

---

## Sincronizando os dados

Após configurar e criar seu banco, chegou a hora de sincronizar os dados.

Execute a seguinte rota:

```http
POST /sync/clients?start_date=1970-01-01
```

- O parâmetro `start_date` (formato YYYY-MM-DD) define a data de corte para a sincronização de **clientes** e também de **pedidos**.
- Os dados serão buscados na API externa configurada na chave `CO_API_BASE_URL` do seu arquivo `.env`.

> A sincronização é feita por meio de _jobs_. Portanto, **é necessário configurar o sistema de filas antes de sincronizar os dados**.

---

## Gerando a tabela de referência

Após a sincronização, gere a tabela de referência executando:

```http
POST /summary/generate
```

Essa rota gera rapidamente a tabela usada como base analítica. Em produção, ela será executada via cron.

---

## Rotas disponíveis

- `GET /categories/`  
  Retorna um array de objetos com as categorias dos clientes e seus respectivos códigos.

- `GET /clients/categories`  
  Retorna a contagem de clientes em cada categoria.

- `GET /clients/categories/:category_code`  
  Retorna todos os clientes de uma categoria específica.  
  (Use a rota `/categories` para obter os códigos disponíveis.)

- `GET /ping`  
  Rota simples para verificar se a API está respondendo corretamente.

---

Com isso, a API estará pronta para uso e exploração =)
