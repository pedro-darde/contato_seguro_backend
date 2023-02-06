# Contato Seguro Backend

Projeto criado em formato de Api Rest para lidar com o CRUD de Empresas e Usuários





## Instalação

Instale o projeto com:
- _Antes de rodar o comando abaixo, verifique que não tenha nada rodando na porta __5432__, pois o __Postgres__ estara rodando na mesma_.

```bash
  docker-compose up -d
```

Após servir a aplicação entre dentro do container dela atraves do comando:

```bash
 docker exec -it contato-seguro-webservice bash
```

Ao entrar no container rode o seguinte comando (para criar as tabelas):

```bash
 php artisan migrate
```
Após os comandos acima a Api estara rodando em http://localhost:8000/api
## Stack utilizada

**Database**: PostgreSQL, escolhido pois atualmente é o banco cujo mais tenho contato e facilidade para desenvolvimento

**Framework**: Laravel, utilizado para facilitar as operacoes de Crud e criacao das rotas HTTP. Vejo que o Laravel auxilia muito em construir um sistema robusto, com uma grande suporte para personalização e auxílio no controle de dependencias



### O que faltou?
- __Criacao dos testes de integração__. Ao começar os testes pude perceber que o ambiente em __Laravel__ não facilita muito a criação de testes unitarios, teria que isolar as __Models__, que no Laravel sao uma representação do banco, para poder testar as funcionalidades do CRUD. Sendo assim acabei não fazendo os testes.