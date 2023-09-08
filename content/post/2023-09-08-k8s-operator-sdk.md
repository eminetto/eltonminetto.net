---
title: "Criando Kubernetes Operators com o operator-sdk"
date: 2023-09-08T08:30:43-03:00
draft: false
---
Se você desenvolve APIs ou microsserviços, especialmente em ambientes de médio a grande porte, provavelmente você está usando Kubernetes.

[Kubernetes](https://kubernetes.io/) é um projeto criado pelo Google em meados de 2015 e que rapidamente se tornou o padrão para gerenciar a execução de containers. Você pode hospedar e gerenciar ele em suas máquinas ou usar alguma solução gerenciada por algum dos grandes players de cloud como [AWS](https://aws.amazon.com/pt/eks/), [Google](https://cloud.google.com/kubernetes-engine) e [DigitalOcean](https://docs.digitalocean.com/products/kubernetes/). Se você quiser se aprofundar mais sobre Kubernetes, ou k8s para deixar mais curto, eu recomendo o [livro](https://www.casadocodigo.com.br/products/livro-kubernetes) e o [curso](https://www.youtube.com/playlist?list=PLnOICPAPShyTwuLit7vP6In9kETQ0BSnQ) do grande Lucas Santos. 

Neste post eu quero falar sobre outra funcionalidade importante que é a possibilidade de estendê-lo para criar novas capacidades. Vamos começar pelos conceitos importantes para o entendimento deste artigo. 

## Resources e Controllers

Um dos conceitos mais básicos é que o k8s gerencia recursos. Segundo a [documentação oficial](https://kubernetes.io/pt-br/docs/home/), 

> Um recurso é um endpoint na API do Kubernetes que armazena uma coleção de objetos de API de um determinado tipo; por exemplo, o recurso built-in pods contém uma coleção de objetos Pod.

Ele faz a gestão destes recursos usando outro conceito importante, os *controllers*. Quando usamos algum recurso do k8s precisamos definir, em um arquivo *yaml* qual é o estado desejado por nós. Por exemplo: 

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: nginx-deployment
spec:
  selector:
    matchLabels:
      app: nginx
  replicas: 2 # diz ao deployment para executar 2 pods que correspondam ao modelo
  template:
    metadata:
      labels:
        app: nginx
    spec:
      containers:
      - name: nginx
        image: nginx:1.14.2
        ports:
        - containerPort: 80
``` 

As informações dentro da chave *spec* correspondem ao estado desejado do recurso.

O que o k8s faz é garantir que o estado atual do objeto contido no cluster seja igual ao estado desejado que foi declarado, neste caso: dois containers do Nginx, versão 1.14.2 sendo executados na porta 80. Ele faz isso usando o que é chamado de *control loop*:

[![operator-reconciliation-kube-only](/images/posts/operator-reconciliation-kube-only.png)](/images/posts/operator-reconciliation-kube-only.png)

Ele verifica se o estado atual do recurso difere do estado desejado, e caso positivo executa a função *Reconcile* do *controller* vinculado ao objeto. Desta forma, podemos definir um *controller* assim:

> Um controller rastreia pelo menos um tipo de recurso do Kubernetes. Esses objetos têm um campo spec que representa o estado desejado. O(s) controlador(es) desse recurso são responsáveis por fazer com que o estado atual se aproxime daquele estado desejado.

O k8s possui uma série de recursos embutidos como *Pod*, *Deployment*, *Service* e *controllers* que rastreiam o ciclo de vida de cada um deles. Mas além deles podemos criar nossos próprios recursos, através dos *Custom Resource Definitions (CRD)*. A junção de um *CRD* e um *controller* é o que chamamos de um [*operator*](https://kubernetes.io/docs/concepts/extend-kubernetes/operator/), e é o que vamos explorar neste texto.

## operator-sdk

Para ilustrar o que podemos fazer com um *operator* vou criar uma prova de conceito usando o *operator-sdk*. Segundo o [site oficial](https://sdk.operatorframework.io/):

> O Operator SDK facilita a criação de aplicativos nativos do Kubernetes, um processo que pode exigir conhecimento operacional profundo e específico do aplicativo.
> Este projeto é um componente do [Operator Framework](https://github.com/operator-framework), um kit de ferramentas de código aberto para gerenciar aplicativos nativos do Kubernetes, chamados Operadores, de forma eficaz, automatizada e escalável.

É possível criarmos um *operator* usando [Go](https://sdk.operatorframework.io/docs/building-operators/golang/quickstart/), [Ansible](https://sdk.operatorframework.io/docs/building-operators/ansible/quickstart/) ou [Helm](https://sdk.operatorframework.io/docs/building-operators/helm/quickstart/). Neste artigo vou usar Go.

O primeiro passo é instalar o *CLI* do sdk na máquina. Eu usei o *brew* mas na [documentação](https://sdk.operatorframework.io/docs/installation/) é possível ver as outras opções.

```bash
brew install operator-sdk
```

O próximo passo é usar o *CLI* para gerar o esqueleto do projeto, usando os comandos:

```bash
operator-sdk init --domain minetto.dev --repo github.com/eminetto/k8s-operator-talk
operator-sdk create api --version v1alpha1 --kind Application --resource --controller
```

O primeiro comando inicializa o projeto indicando o domínio, informação que é usada pelo k8s para identificar o recurso, e o nome do repositório, que é usado para o nome do pacote do Go. O segundo comando indica a criação de um novo recurso chamado *Application*, na versão *alpha1* e que vamos precisar também do esqueleto de um *controller*.

Antes de entrarmos no código, é importante entendermos o objetivo da prova de conceito. Na sua forma nativa, colocar uma aplicação em execução no k8s exige que a pessoa desenvolvedora entenda uma série de conceitos como *Deployment*, *Pod*, *Service*, etc. O meu objetivo aqui é reduzir esta carga cognitiva para apenas dois conceitos: um [*namespace*](https://kubernetes.io/docs/concepts/overview/working-with-objects/namespaces/), onde a aplicação vai residir dentro do cluster, e um *Application*, que vai definir o estado desejado de uma aplicação. Por exemplo, o time precisa criar apenas o seguinte *yaml*:

```yaml
apiVersion: v1
kind: Namespace
metadata:
  name: application-sample
---
apiVersion: minetto.dev/v1alpha1
kind: Application
metadata:
  name: application-sample
  namespace: application-sample
spec:
  image: nginx:latest
  replicas: 2
  port: 80
```

Aplicá-lo ao cluster usando o comando:

```bash
kubectl apply -f application.yaml
```

E o restante é criado pelo nosso *controller*. 

O primeiro passo é configurar nosso recurso para que ele possua os campos relacionados a `spec`. Para isso é preciso alterar o aquivo *api/v1alpha/application_types.go* e adicionar os campos na struct:

```go
type ApplicationSpec struct {
	Image    string `json:"image,omitempty"`
	Replicas int32  `json:"replicas,omitempty"`
	Port     int32  `json:"port,omitempty"`
}
```

Mais tarde vamos usar esta informação para gerar os arquivos necessários para a instalação do *CRD* no nosso cluster. E também vamos usar esta *struct* para criarmos os recursos necessários.

O próximo passo é criarmos a lógica do nosso *controller*. O *operator-sdk* criou o arquivo *controllers/application_controller.go* e a assinatura da função *‌Reconcile*.  É esta função que é chamada pelo *control loop* cada vez que o k8s detecta alguma diferença entre o estado atual do objeto e o estado desejado. O vínculo entre o recurso *Application* e o nosso *controller* está definido no arquivo *main.go* que foi gerado pelo sdk, e não precisamos nos preocupar com ele agora. Essa é uma das vantagens do *operator-sdk* pois nos permite manter o foco na lógica do *controller* e abstrai todos os detalhes massantes necessários para que ele funcione.

O código da função *Reconcile*, e as auxiliares, encontra-se a seguir. Tentei documentar os trechos mais importantes:

```go
func (r *ApplicationReconciler) Reconcile(ctx context.Context, req ctrl.Request) (ctrl.Result, error) {
	l := log.FromContext(ctx)
	var app minettodevv1alpha1.Application
	//recupera os detalhes do objeto sendo gerenciado
	if err := r.Get(ctx, req.NamespacedName, &app); err != nil {
		if apierrors.IsNotFound(err) {
			return ctrl.Result{}, nil
		}
		l.Error(err, "unable to fetch Application")
		return ctrl.Result{}, err
	}
	/*o finalizer é importante pois indica ao k8s que 
	precisamos ter controle sobre a exclusão do objeto
	pois como nós vamos criar outros recursos é 
	importante que eles sejam excluídos junto
	sem o finalizer não dá tempo para que o 
	garbage collector do k8s faça a exclusão
	e corremos o risco de termos recursos sem utilidade
	no cluster*/
	if !controllerutil.ContainsFinalizer(&app, finalizer) {
		l.Info("Adding Finalizer")
		controllerutil.AddFinalizer(&app, finalizer)
		return ctrl.Result{}, r.Update(ctx, &app)
	}

	if !app.DeletionTimestamp.IsZero() {
		l.Info("Application is being deleted")
		return r.reconcileDelete(ctx, &app)
	}
	l.Info("Application is being created")
	return r.reconcileCreate(ctx, &app)
}

func (r *ApplicationReconciler) reconcileCreate(ctx context.Context, app *minettodevv1alpha1.Application) (ctrl.Result, error) {
	l := log.FromContext(ctx)
	l.Info("Creating deployment")
	err := r.createOrUpdateDeployment(ctx, app)
	if err != nil {
		return ctrl.Result{}, err
	}
	l.Info("Creating service")
	err = r.createService(ctx, app)
	if err != nil {
		return ctrl.Result{}, err
	}
	return ctrl.Result{}, nil
}

func (r *ApplicationReconciler) createOrUpdateDeployment(ctx context.Context, app *minettodevv1alpha1.Application) error {
	var depl appsv1.Deployment
	deplName := types.NamespacedName{Name: app.ObjectMeta.Name + "-deployment", Namespace: app.ObjectMeta.Name}
	if err := r.Get(ctx, deplName, &depl); err != nil {
		if !apierrors.IsNotFound(err) {
			return fmt.Errorf("unable to fetch Deployment: %v", err)
		}
		/*se não existe um Deployment vamos criá-lo
		Um trecho importante na definição é o OwnerReferences
		pois ele indica ao k8s que este recurso está sendo criado
		por um Application. 
		É assim que o k8s sabe que ao removermos um Application
		ele deve remover também todos os recursos que ele criou
		Outro ponto importante é que aqui usamos os dados 
		da nossa Application para criar o Deployment, 
		como a informação da imagem, porta e replicas
		*/
		if apierrors.IsNotFound(err) {
			depl = appsv1.Deployment{
				ObjectMeta: metav1.ObjectMeta{
					Name:        app.ObjectMeta.Name + "-deployment",
					Namespace:   app.ObjectMeta.Name,
					Labels:      map[string]string{"label": app.ObjectMeta.Name, "app": app.ObjectMeta.Name},
					Annotations: map[string]string{"imageregistry": "https://hub.docker.com/"},
					OwnerReferences: []metav1.OwnerReference{
						{
							APIVersion: app.APIVersion,
							Kind:       app.Kind,
							Name:       app.Name,
							UID:        app.UID,
						},
					},
				},
				Spec: appsv1.DeploymentSpec{
					Replicas: &app.Spec.Replicas,
					Selector: &metav1.LabelSelector{
						MatchLabels: map[string]string{"label": app.ObjectMeta.Name},
					},
					Template: v1.PodTemplateSpec{
						ObjectMeta: metav1.ObjectMeta{
							Labels: map[string]string{"label": app.ObjectMeta.Name, "app": app.ObjectMeta.Name},
						},
						Spec: v1.PodSpec{
							Containers: []v1.Container{
								{
									Name:  app.ObjectMeta.Name + "-container",
									Image: app.Spec.Image,
									Ports: []v1.ContainerPort{
										{
											ContainerPort: app.Spec.Port,
										},
									},
								},
							},
						},
					},
				},
			}
			err = r.Create(ctx, &depl)
			if err != nil {
				return fmt.Errorf("unable to create Deployment: %v", err)
			}
			return nil
		}
	}
	/*o controller precisa também gerenciar a alteração
	pois se o dev alterar alguma informação de uma 
	Application já existente isso deve ter impacto nos
	demais recursos*/
	depl.Spec.Replicas = &app.Spec.Replicas
	depl.Spec.Template.Spec.Containers[0].Image = app.Spec.Image
	depl.Spec.Template.Spec.Containers[0].Ports[0].ContainerPort = app.Spec.Port
	err := r.Update(ctx, &depl)
	if err != nil {
		return fmt.Errorf("unable to update Deployment: %v", err)
	}
	return nil
}

func (r *ApplicationReconciler) createService(ctx context.Context, app *minettodevv1alpha1.Application) error {
	srv := v1.Service{
		ObjectMeta: metav1.ObjectMeta{
			Name:      app.ObjectMeta.Name + "-service",
			Namespace: app.ObjectMeta.Name,
			Labels:    map[string]string{"app": app.ObjectMeta.Name},
			OwnerReferences: []metav1.OwnerReference{
				{
					APIVersion: app.APIVersion,
					Kind:       app.Kind,
					Name:       app.Name,
					UID:        app.UID,
				},
			},
		},
		Spec: v1.ServiceSpec{
			Type:                  v1.ServiceTypeNodePort,
			ExternalTrafficPolicy: v1.ServiceExternalTrafficPolicyTypeLocal,
			Selector:              map[string]string{"app": app.ObjectMeta.Name},
			Ports: []v1.ServicePort{
				{
					Name:       "http",
					Port:       app.Spec.Port,
					Protocol:   v1.ProtocolTCP,
					TargetPort: intstr.FromInt(int(app.Spec.Port)),
				},
			},
		},
		Status: v1.ServiceStatus{},
	}
	_, err := controllerutil.CreateOrUpdate(ctx, r.Client, &srv, func() error {
		return nil
	})
	if err != nil {
		return fmt.Errorf("unable to create Service: %v", err)
	}
	return nil
}

func (r *ApplicationReconciler) reconcileDelete(ctx context.Context, app *minettodevv1alpha1.Application) (ctrl.Result, error) {
	l := log.FromContext(ctx)

	l.Info("removing application")

	controllerutil.RemoveFinalizer(app, finalizer)
	err := r.Update(ctx, app)
	if err != nil {
		return ctrl.Result{}, fmt.Errorf("Error removing finalizer %v", err)
	}
	return ctrl.Result{}, nil
}
```

Para fazermos o deploy do nosso recurso customizado e do seu *controller* o SDK fornece comandos no seu *makefile*:

```bash
make manifests
make docker-build docker-push IMG=registry.hub.docker.com/eminetto/k8s-operator-talk:latest
make deploy IMG=registry.hub.docker.com/eminetto/k8s-operator-talk:latest
```

O primeiro comando gera todos os rquivos necessários para a criação do *CRD*. O segundo faz a geração de um *container docker* e o *push* para o repositório indicado. E o último comando faz a instalação do *container* gerado no cluster. Dica: é possível automatizar a geração e instalação do *controller* em seu ambiente de desenvolvimento usando o *Tilt*. No [repositório](https://github.com/eminetto/k8s-operator-talk) onde estão estes códigos existe um *Tiltfile* que faz todo este trabalho. E para conhecer mais sobre o *Tilt* confira o [post](https://eltonminetto.dev/post/2022-08-31-improve-local-development-tilt/) que fiz sobre a ferramenta.  

Agora basta aplicar ao cluster o *yaml* com a definição do *Application* e o *controller* vai gerar o *Deployment* e o *Service* necessários para que a aplicação esteja em execução. Podemos conferir que os recursos foram criados com os comandos a seguir.

```bash
kubectl -n application-sample get applications
NAME                 AGE
application-sample   18s
```

```bash
kubectl -n application-sample get deployments
NAME                            READY   UP-TO-DATE   AVAILABLE   AGE
application-sample-deployment   2/2     2            2           41s
```

```bash
kubectl -n application-sample get pods
NAME                                             READY   STATUS    RESTARTS   AGE
application-sample-deployment-65b96554f8-8vv64   1/1     Running   0          56s
application-sample-deployment-65b96554f8-v54gp   1/1     Running   0          56s
```

```bash
kubectl -n application-sample get services
NAME                         TYPE       CLUSTER-IP     EXTERNAL-IP   PORT(S)        AGE
application-sample-service   NodePort   10.43.63.164   <none>        80:32591/TCP   66s
```

Este post acabou ficando bem extenso, então tem outros assuntos que vou deixar para um próximo texto, como a parte de testes. Mas espero que eu tenha conseguido despertar o interesse neste assunto. É algo que eu estou bem empolgado e acredito que tem um potencial incrível para ajudar na criação de automações que facilitam muito a vida dos times de desenvolvimento e operações.

