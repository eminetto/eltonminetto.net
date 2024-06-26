---
title: "Dicas de livros sobre complexidade"
date: 2022-11-25T13:00:19-03:00
draft: false
---
Eu sempre tive a impressão de que um dos maiores males do desenvolvimento de software moderno é a complexidade. Não me refiro a complexidade dos problemas que são resolvidos atualmente, pois esses são realmente maiores do que décadas atrás. Machine learning, carros autônomos, microsserviços, etc, esses cenários possuem uma complexidade inerente e pouco podemos fazer para mitigar isso. Eu me refiro a complexidade que incutimos aos nossos códigos. Já vi aplicações que eram pouco mais do que CRUDs com várias camadas e frameworks que só tornavam o desenvolvimento e manutenção tarefas hercúleas. 

Recentemente encontrei dois autores que corroboram com essa minha impressão. O primeiro deles é o [A Philosophy of Software Design](https://www.amazon.com.br/gp/product/B09B8LFKQL/), do professor John K. Ousterhout. Eu gostei tanto do livro que gerei alguns conteúdos inspirados na obra:

- Post [Sobre design de software e complexidade](https://medium.com/inside-picpay/sobre-design-de-software-e-complexidade-2df2a13f01c2) para o blog do PicPay
- [Palestra](https://speakerdeck.com/eminetto/reflexoes-sobre-design-de-software) que apresentei em diversos eventos esse ano
- [Videos no YouTube](https://www.youtube.com/watch?v=w3kJe53pEjA&list=PL0qudqr7_CuQ5lI5rFvLD8Bh_rMPlbuUt)
- [Live](https://www.youtube.com/watch?v=o9jjtpYF3ww) falando sobre o assunto no canal do Mauricio Linhares

O segundo livro é o *Code Simplicity: The Fundamentals of Software* que agora pode ser baixado de maneira gratuita no [site do autor](https://www.codesimplicity.com/post/code-simplicity-the-fundamentals-of-software-is-now-free/). 

Como já falei bastante sobre o primeiro livro, vou aproveitar esse post para comentar um pouco sobre o *"Code Simplicity"*. É um livro bem curto, 80 páginas, e bem direto. Ele dividiu o conteúdo em *Fatos*, *Regras* e *Leis* do design de software e usou as páginas do livro para detalhar exemplos e como esses conceitos se aplicam a praticamente todas as aplicações. Dentre algumas "pérolas" do livro posso destacar algumas (tradução minha):

- Fato: A diferença entre um mau programador e um bom programador é a compreensão. Ou seja, programadores ruins não entendem o que estão fazendo, e bons programadores entendem.
- Fato: Todo mundo que escreve software é um designer.
- Regra: O nível de qualidade do seu projeto deve ser proporcional ao tempo futuro em que seu sistema continuará a ajudar as pessoas.
- Entre outras.

E as suas 6 leis do design de software (novamente, tradução minha) com alguns comentários

- **O propósito do software é ajudar as pessoas.**

*Achei essa frase muito interessante e concordo 100% com ela. Ajuda muito a tomar decisões importantes, como priorizações e mesmo se uma feature deve ou não ser desenvolvida*

- **A equação do design de software. O que demonstra que é mais importante reduzir o esforço de manutenção do que reduzir o esforço de implementação.**

*No livro o autor faz uma equação para demonstrar isso, que eu deixei de fora deste post de propósito, para incentivar a leitura ;)*

- **A Lei da Probabilidade do Defeito: A chance de introduzir um defeito em seu programa é proporcional ao tamanho das mudanças que você faz nele.**

*Ou seja: pequenas e contínuas mudanças, pull requests pequenos, agilidade*

- **A Lei da Simplicidade: A facilidade de manutenção de qualquer porção de software é proporcional à simplicidade de suas porções individuais.**

*Códigos bem estruturados, pequenas funções ou classes, a visão de que um software é formado por vários pequenos componentes bem construídos e testados.*

- **A Lei do Teste: O grau em que você sabe como seu software se comporta é o grau em que você o testou com precisão.**

*Ou seja: testes, testes, testes :)*

Apenas citar as leis não tem tanto impacto quanto a leitura do livro, onde é possível ver exemplos e cenários, mas eu achei válido incluí-las neste texto para instigá-lo a ler o livro todo. 

Sei que o fato de ainda não ter tradução para o português pode ser uma barreira para muitas pessoas, mas mesmo assim vejo esses livros como ótimas recomendações para pessoas desenvolvedoras de todos os níveis. Acredito que o Google Translator ajude a diminuir essa barreira e recomendo a tentativa pois o conteúdo é bem importante. 