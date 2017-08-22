import uno
import unohelper
import os

# Abre o OpenOffice.org usando os parametros para que fique ouvindo na porta 2002 por novas
# conex�es. O par�metro accept � usado para que clientes tenham acesso a API do
# OO.org atrav�s da rede, seja interna ou internet.
os.system('soffice "-accept=socket,host=localhost,port=2002;urp;"')

# Retorna o componente context do PyUNO runtime
localContext = uno.getComponentContext()

# Cria o UnoUrlResolver 
resolver = localContext.ServiceManager.createInstanceWithContext(
    'com.sun.star.bridge.UnoUrlResolver', localContext)

# Conecta ao OO.org em execu��o
ctx = resolver.resolve(
    'uno:socket,host=localhost,port=2002;'
    'urp;StarOffice.ComponentContext')
smgr = ctx.ServiceManager

# Retorna o objeto central do desktop
desktop = smgr.createInstanceWithContext(
    'com.sun.star.frame.Desktop', ctx)

# Carrega o documento
cwd = os.getcwd()
path = os.path.join(cwd, 'modelo.sxw')
url = unohelper.systemPathToFileUrl(path)
doc = desktop.loadComponentFromURL(url, '_blank', 0, ())

# Procura e substituicao
# Lista dos dados a alterar
# No arquivo modelo.sxw existe dois campos chamados {{{nome}}} e 
# {{{sobrenome}}}. Estes campos ser�o substitu�dos pelo conte�do das
# tuplas abaixo

L = [('{{{nome}}}', 'Elton', ),
     ('{{{sobrenome}}}', 'Minetto', ),
     ]
# busca e troca
for search, replace in L:
    rd = doc.createReplaceDescriptor()
    rd.setSearchString(search)
    rd.setReplaceString(replace)
    doc.replaceAll(rd)

# Imprime
uno.invoke(doc, "print", ((), ))
# O arquivo n�o pode ser fechado enquando a impress�o n�o finalizar
# Testa para verificar se a impress�o acabou
res = doc.getPrinter()
while res[4].Value == 1:
        res = doc.getPrinter()
        print "Esperando a impress�o"

# Fecha o arquivo sem salvar
try:
    doc.close(True)
except com.sun.star.util.CloseVetoException:
    pass

#Fecha o OO.org
desktop.terminate()
