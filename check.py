import time
import MySQLdb
import subprocess as subprocess

SQLparams = {
    'user': 'root', 
    'password': 'a92015a92015',
    'host': 'localhost'
}


conn=MySQLdb.connect(**SQLparams)
cursor=conn.cursor()
SQL="CREATE DATABASE IF NOT EXISTS switch DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_unicode_ci" 
cursor.execute(SQL)
conn.commit()
cursor.close()
conn.close()

conn=MySQLdb.connect(**SQLparams, db="webcam", charset="utf8")
cursor=conn.cursor()
SQL="CREATE TABLE IF NOT EXISTS switch( switch int(1))"
cursor.execute(SQL)
conn.commit()

subprocess.Popen(['python', 'camera _ _.py'])

check_T = '(1,)'
while True:
    f = open('pid.txt','r')
    pid = f.read
    SQL="SELECT * FROM switch"
    cursor.execute(SQL)
    check = str(cursor.fetchone())
    if check == check_T :
        SQL="UPDATE switch SET switch='0' WHERE switch='1' "
        cursor.execute(SQL)
        conn.commit()
        subprocess.Popen('taskkill /F /PID {0}'.format(pid), shell=True)
        subprocess.Popen(['python', 'camera _ _.py'])
        f = open('check.txt','w')
        f.write("0")
    time.sleep(30)