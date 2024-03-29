+++
title = "Dicas para devs: desafio técnico"
subtitle = ""
date = "2020-12-18T08:33:24+02:00"
bigimg = ""

+++
Na última década ou mais, uma das tarefas mais importantes que eu tenho desempenhado é a contratação de pessoas para trabalharem como dev nos times que eu gerencio. Parte do processo de contratação geralmente é a análise de código de um desafio técnico e neste post vou citar algumas dicas que podem ser úteis para quem está avaliando ou sendo avaliado.

As expectativas em relação ao código mudam bastante de acordo com o nível de senioridade da vaga. Como na nossa área não existe um consenso sobre o que é júnior, pleno e sênior, eu vou usar a definição a seguir, que encontrei [neste post](https://swizec.com/blog/youre-not-asking-for-a-job-youre-selling-a-service) e que eu traduzi livremente.

> Quando você é júnior, a "coachabilidade" **(OBS: não encontrei uma tradução melhor, mas seria a capacidade de ser treinado)** é o que o faz ser contratado. Com que rapidez podemos treiná-lo para ser eficaz?

> Quando você é pleno, habilidades técnicas fazem com que você seja contratado. Você pode fazer um trabalho útil agora e preencher rapidamente as lacunas de conhecimento?

> Quando você é sênior, sua experiência e opiniões fazem com que você seja contratado. A empresa não sabe como resolver seus problemas. Eles nem conhecem os problemas. Eles apenas sabem que dói. Por que não estamos entregando? Por que nossa arquitetura está quebrada? Por que não podemos escalar?

Geralmente vagas procurando por pessoas sênior não envolvem desafios técnicos sendo que a entrevista é a parte mais importante. Por isso vou focar as dicas nos dois primeiros perfis.

Quando eu estou avaliando uma pessoa com pouca experiência um ponto que eu acho importante é tentar entender o raciocínio que ela usou para escrever o código. Uma forma de demonstrar isso no desafio técnico é organizando o histórico de commits do código. Antes de começar a programar, tente quebrar o problema em partes menores e fazer commits incrementais demonstrando como você se organizou para resolver o problema. Fez primeiro todas as tarefas do backend? Implementou toda a lógica e depois fez a parte visual? etc. 

Isso mostra muito sobre sua forma de pensar e se organizar, o que é bem importante para quem está iniciando na área. Uma dica legal é dar atenção às mensagens de commit e para isso eu sugiro o uso de um padrão como o [Conventional Commits](https://www.conventionalcommits.org/pt-br/v1.0.0/). 

Outra dica é usar bem os comentários nos códigos para demonstrar seu raciocínio e decisões. Mas ao invés de escrever no comentário **o que** o código, faz prefira falar sobre **o porque**. Por que você quebrou o código em funções? Qual o motivo de ter escolhido esta abordagem e não outra? etc.

A próxima dica é sobre o uso ou não de frameworks, especialmente se você for pleno. Se a vaga pede especificamente experiência em determinado framework é esperado que você o use e será cobrado por isso. Mas se a vaga tiver uma descrição mais aberta, não fazendo menção a nenhum framework específico, a escolha da ferramenta vai contar bastante na sua avaliação. 

Neste caso eu sugiro que você evite usar frameworks "full stack" como o Laravel, Rails, Django, Buffalo, etc. Eles são ótimos frameworks, mas várias das decisões importantes já foram tomadas pelos seus criadores, como arquitetura, componentes, padrões de código, estrutura de diretórios, etc. E isso vai tirar uma boa oportunidade de demonstrar seus conhecimentos. Prefira escolher micro-frameworks como o Lumen, ou bibliotecas como o Gin, para citar apenas dois exemplos, em PHP e Go.  Escolhendo estes micro-frameworks/bibliotecas permite que você demonstre as decisões importantes como estrutura de diretórios do projeto, arquitetura escolhida, formato dos testes, etc. E isso conta muito para alguém nos níveis pleno e sênior. 

E quanto a testes? Para vagas júnior eu considero a existência de testes no código um bônus muito valioso e que conta muitos pontos. Mas para uma vaga pleno a ausência de testes pode ser considerada uma falha bem grave! Não é necessário cobertura de testes de todo o código, mas procure escrever códigos para as partes mais importantes. A decisão do que é mais importante na sua concepção mostra bastante sobre sua maturidade, então analise bem o que e como testar para encantar a pessoa que está fazendo a avaliação. 

Para finalizar, mas não menos importante, capriche no README.md (ou outro nome que quiser dar para a documentação do projeto). Descreva os passos que a pessoa precisa fazer para executar o seu código, fale sobre as decisões e escolhas que fez, acrescente um *To do List* com as coisas extras que gostaria de implementar ou que você tentou fazer e não teve tempo, etc. Isso vale para desafios de qualquer nível e mostra bastante sobre sua organização, sua preocupação em facilitar a vida dos colegas, etc. 

 
Lembrou de mais alguma dica importante? Contribua nos comentários que vai ser bem útil para quem está procurando emprego ou para quem tem a importante tarefa de avaliar pessoas para suas equipes. 

P.S. se você gostou destas dicas talvez o meu e-book gratuito possa te interessar: [Dicas de carreira para devs](https://leanpub.com/dicas-carreira-devs)