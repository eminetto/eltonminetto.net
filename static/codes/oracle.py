#!/usr/bin/python
import cx_Oracle

uid = "user"    # usu�rio
pwd = "senha"   # senha
db = "db_conf"  # string de conex�o do Oracle, configurado no cliente Oracle, arquivo tnsnames.ora

connection = cx_Oracle.connect(uid+"/"+pwd+"@"+db) #cria a conex�o
cursor = connection.cursor() # cria um cursor

cursor.execute("SELECT * from tab") # consulta sql
result = cursor.fetchone()  # busca o resultado da consulta
if result == None:
        print "Nenhum Resultado"
        exit
else:
        while result:
                print result[0]
                result = cursor.fetchone()
cursor.close()
connection.close()

