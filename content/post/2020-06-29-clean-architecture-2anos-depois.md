+++
title = "Clean Architecture, 2 anos depois"
subtitle = ""
date = "2020-06-29T10:54:24+02:00"
bigimg = ""

+++
Em Fevereiro de 2018 escrevi aquele que viria a ser o mais relevante texto que já publiquei: [Clean Architecture using Golang](https://eltonminetto.dev/en/post/2018-03-05-clean-architecture-using-go/). Com mais de 105 mil views o assunto gerou apresentações em alguns eventos de Go e PHP, além de me proporcionar a oportunidade de conversar sobre o assunto com várias pessoas. 

Conforme fomos usando esta arquitetura para o desenvolvimento dos produtos da [Codenation](https://codenation.dev) fomos ganhando experiência, resolvendo problemas e também gerando novos posts:

- [Golang: usando build tags para armazenar configurações](https://eltonminetto.dev/post/2018-06-25-golang-usando-build-tags/)
- [Integração contínua em projetos usando monorepo](https://eltonminetto.dev/post/2018-08-01-monorepo-drone/)
- [Monitorando uma aplicação Golang com o Supervisor](https://eltonminetto.dev/post/2018-11-28-monitorando-app-go-com-supervisor/)
- [Migração de dados com Golang e MongoDB](https://eltonminetto.dev/post/2019-01-23-migracao-de-dados-com-go-e-mongodb/)
- [Usando Golang como linguagem de script](https://eltonminetto.dev/post/2019-08-08-golang-linguagem-script/)
- [Criando mocks para testes usando GoMock](https://eltonminetto.dev/post/2019-12-19-usando-gomock/)
- [Usando Prometheus para coletar métricas de aplicações Golang](https://eltonminetto.dev/post/2020-03-12-golang-prometheus/)
- [Fazendo profiling de aplicações Golang usando pprof](https://eltonminetto.dev/post/2020-04-08-golang-pprof/)
- [Testando APIs em Golang usando apitest](https://eltonminetto.dev/post/2020-04-10-golang-apitest/)

Depois dessa experiência toda posso afirmar com certeza:

> Escolher a Clean Architecture foi a melhor decisão técnica que tomamos! 

A segunda melhor foi a escolha da linguagem Go. Fiz uma palestra sobre essa escolha. Os [slides](https://speakerdeck.com/eminetto/por-que-e-como-usamos-go-na-codenation) e o [video](https://www.youtube.com/watch?v=Z-JQOCSdxdU) estão disponíveis caso queira ver mais detalhes.

Além de ressaltar o sucesso que tivemos com a Clean Architecture, este post serve para divulgar um [repositório](https://github.com/eminetto/clean-architecture-go-v2) que criei com uma nova versão do exemplo de implementação em Go. Ele é uma atualização com melhorias na organização dos códigos e diretórios, bem como é um exemplo mais completo para quem está querendo implementar esta arquitetura.

Abaixo faço uma explicação do que significa cada diretório do projeto.

## Camada Entity

Vamos começar pela camada mais interna da arquitetura:

[![entity](/images/posts/1-entity_book.png)](/images/posts/1-entity_book.png)

Neste pacote temos a definição da entidade (`book.go`) e da interface do `Repository` e `UseCase` (em `interface.go`). Também temos as implementações do repositório em `MySQL` (`repository_mysql.go`) e em memória (`repository_inmem.go`), assim como a implementação do `UseCase` (em `service.go`). O `UseCase` deste pacote implementa as operações básicas em relação a entidade, o famoso `CRUD`. Também encontramos os `mocks` gerados pelo `Gomock`, conforme explicado neste [post](https://eltonminetto.dev/post/2019-12-19-usando-gomock/). Estes mocks são usados pelas demais camadas da arquitetura durante os testes.

## Camada UseCase

[![domain](/images/posts/2-domain_loan.png)](/images/posts/2-domain_loan.png)

Nos demais pacotes dentro de `domain` implementamos as regras de negócio do nosso produto. De acordo com a definição da arquitetura, estes `UseCases` fazem uso das entidades e dos serviços que fazem o tratamento das mesmas (o `CRUD`). Também é possível ver a existência de mocks, que são usados pelas outras camadas da arquitetura.

## Camada Controller

Neste aplicação de exemplo existem duas formas de acesso aos `UseCases`. A primeira é através de uma `API` e a segunda é usando um aplicativo de linha de comando (`CLI`). 

A estrutura do `CLI` é bem simples:

[![cli](/images/posts/4-cmd.png)](/images/posts/4-cmd.png)

Ele faz uso dos pacotes de domínio para realizar uma busca de livros:

```go
dataSourceName := fmt.Sprintf("%s:%s@tcp(%s:3306)/%s?parseTime=true", config.DB_USER, config.DB_PASSWORD, config.DB_HOST, config.DB_DATABASE)
   db, err := sql.Open("mysql", dataSourceName)
   if err != nil {
      log.Fatal(err.Error())
   }
   defer db.Close()
   repo := book.NewMySQLRepoRepository(db)
   service := book.NewService(repo)
   all, err := service.Search(query)
   if err != nil {
      log.Fatal(err)
   }
   for _, j := range all {
      fmt.Printf("%s %s \n", j.Title, j.Author)
   }
```	

No exemplo acima é possível ver o uso do pacote `config`. Sua estrutura pode ser vista abaixo e mais detalhes encontrados neste [post](https://eltonminetto.dev/post/2018-06-25-golang-usando-build-tags/). 

[![config](/images/posts/3-config.png)](/images/posts/3-config.png)

A estrutura da `API` é um pouco mais complexa e composta por três pacotes: `handler`, `presenter` e `middleware`.

O pacote `handler` é responsável pelo tratamento das `requests` e `responses` `HTTP`, bem como usar as regras de negócio existentes no `domain`. 

[![handler](/images/posts/5-handler.png)](/images/posts/5-handler.png)

Os `presenters` são responsáveis pela representação dos dados que serão gerados como `response` pelos `handlers`. 


[![presenter](/images/posts/6-presenter.png)](/images/posts/6-presenter.png)


Desta forma, a entidade `User`:

```go
type User struct {
	ID        entity.ID
	Email     string
	Password  string
	FirstName string
	LastName  string
	CreatedAt time.Time
	UpdatedAt time.Time
	Books     []entity.ID
}
```

Pode ser transformada em: 

```go
type User struct {
	ID        entity.ID `json:"id"`
	Email     string    `json:"email"`
	FirstName string    `json:"first_name"`
	LastName  string    `json:"last_name"`
}
```

Com isso ganhamos maior controle em relação a como uma entidade será entregue pela `API`.

No último pacote da `API` encontramos os `middlewares`, que são usados por vários `endpoins`:

[![middlware](/images/posts/7-middleware.png)](/images/posts/7-middleware.png)

## Pacotes auxiliares

Além dos pacotes comentados acima, podemos incluir na nossa aplicação outros trechos de código que podem ser utilizados por várias camadas. São pacotes que fornecem funcionalidades comuns como criptografia, log, tratamento de arquivos, etc. Estas funcionalidades não fazem parte do domínio da nossa aplicação, e podem ser inclusive reutilizados por outros projetos:

[![pkg](/images/posts/8-pkg.png)](/images/posts/8-pkg.png)

No [README.md do repositório](https://github.com/eminetto/clean-architecture-go-v2) constam mais detalhes, como instruções para compilação e exemplos de uso.

Espero com este post fortalecer minha recomendação quanto a esta arquitetura e também receber feedbacks quanto aos códigos. Se você quer aprender a usar esta arquitetura em sua linguagem favorita, fica a sugestão para usar este repositório como exemplo para este aprendizado. Assim podemos ter diferentes implementações, em diferentes linguagens, para facilitar a comparação.

