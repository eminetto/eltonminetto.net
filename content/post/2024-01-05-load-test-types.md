---
title: "Tipos de teste de carga"
date: 2024-01-05T19:00:43-03:00
draft: false
---
Quando falamos em “teste de carga” talvez a primeira coisa que venha na mente seja algo como “enviar tráfego para a aplicação até ela chorar” 🙂 Mas essa abordagem é apenas uma das formas de se testar a performance de uma aplicação (e descrita desta forma talvez seja a mais sádica). Neste post vou apresentar os principais tipos de teste de carga e em um próximo texto vou mostrar como implementá-los usando a ferramenta [k6](https://k6.io).

Os principais tipos de testes de carga se diferenciam entre si em dois aspectos: seus objetivos e a forma como realizamos os testes. Vamos a eles.

## Smoke testing

Também conhecida como “Build Verification Testing” ou “Build Acceptance Testing”.

### Objetivos

- Testar a funcionalidade básica da aplicação. Antes de testarmos se a aplicação suporta centenas ou milhares de usuários precisamos garantir que ela está funcional, que vai agir corretamente com um ou poucos usuários.
- Além de garantir a funcionalidade básica, ela serve como baseline para os próximos testes pois podemos usar os seus resultados como parâmetro para os próximos testes. Ex: se a aplicação executa em X milissegundos para 1 usuário podemos usar esse valor para comparar com 100 ou 1000 usuários simultâneos.

### Como testar

Este é o teste mais simples, basta acessar a API ou sistema usando apenas um usuário, ou um número muito pequeno, por alguns segundos e analisar o resultado.

## Load testing

### Objetivos

- Testar a carga esperada do sistema. Por exemplo, se é esperado que a API seja acessada por 1000 usuários esse é o valor que vamos usar nos testes.
- Garantir que a performance mínima é sempre a esperada. Para fins de comparação podemos usar os dados gerados no Smoke Test, algum padrão de mercado ou alguma regulamentação a que o sistema esteja sujeito (como os do Pix impostos pelo BACEN).

### Como testar

É importante lembrar que em todos os testes de carga estamos simulando o comportamento normal de um usuário. Por isso é importante pensarmos que, salvo no teste de Spike que vamos ver mais adiante, a carga aumenta gradualmente e não de uma vez só. E também não desaparece por mágica. Por isso os testes tem uma fase de *“ramp-up”*, onde vamos aumentando gradualmente a carga, e uma fase de *“ramp-down”*, onde a carga vai diminuindo até parar. Com isso conseguimos também avaliar como nosso sistema se comporta em relação a elasticidade (inclusão e remoção de recursos conforme o necessário).

[![LoadTest](/images/posts/LoadTest.png)](/images/posts/LoadTest.png)

## Stress testing

### Objetivos

- Adicionar mais carga do que o normal.
- Testa como o sistema se comporta sob pressão, respondendo a perguntas como “Como o sistema se comporta com 10% a mais de carga? E com 50% a mais?”.

### Como testar

[![StressTest](/images/posts/StressTest.png)](/images/posts/StressTest.png)

## Spike testing

### Objetivos

- Adicionar um pico de carga para observar como o sistema se comporta nestes cenários.
- Responder perguntas como “O que acontece se uma celebridade ou grande evento citar o nosso sistema e repentinamente milhares de usuários acessarem?”

### Como testar

Neste caso o teste vai simular um aumento instantâneo de acessos, que vai diminuir na mesma velocidade.

[![SpikeTest](/images/posts/SpikeTest.png)](/images/posts/SpikeTest.png)

## Breakpoint testing

### Objetivos

- Forçar uma carga no sistema até ele quebrar. Esse é o teste que citei no começo, “enviar tráfego para a aplicação até ela chorar” 🙂
- Identificar qual é o ponto de ruptura do ambiente

### Como testar

[![BreakTest](/images/posts/BreakTest.png)](/images/posts/BreakTest.png)

## Soak testing

Também conhecido como “endurance testing”, “capacity testing”, ou “longevity testing”

### Objetivos

- Testar como o sistema se comporta sob carga constante por um longo período de tempo
- Ajudar a identificar memory leaks ou como o sistema se comporta com a exaustão de algum recurso como memória, disco e banco de dados

### Como testar

[![Soaktest](/images/posts/Soaktest.png)](/images/posts/Soaktest.png)

Estes são os principais testes com os quais podemos validar diferentes aspectos do nosso sistema ou API. Agora que temos uma base de conhecimento, no próximo post vou mostrar exemplos de como desenvolver estes testes usando a ferramenta k6.

# Fontes

[https://k6.io/docs/test-types/load-test-types/](https://k6.io/docs/test-types/load-test-types/)

[https://www.udemy.com/course/k6-load-testing-performance-testing/](https://www.udemy.com/course/k6-load-testing-performance-testing/)