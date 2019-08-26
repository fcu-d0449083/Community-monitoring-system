import csv
import MySQLdb

SQLparams = {
    'user': 'root', 
    'password': 'a92015a92015',
    'host': 'localhost'
}


def downloadcsvfun():
	conn=MySQLdb.connect(**SQLparams, db="webcam", charset="utf8")
	cursor=conn.cursor()
	SQL="SELECT * FROM snapshot" 
	cursor.execute(SQL)
	result=cursor.fetchall()
	
	#print(result)

	c = csv.writer(open('snapshot.csv', 'w',newline=''))
	for x in result:
		c.writerow(x)

	
	SQL="SELECT * FROM nameonly" 
	cursor.execute(SQL)
	result=cursor.fetchall()

#print(result)

	c = csv.writer(open('nameonly.csv', 'w',newline=''))
	for x in result:
		c.writerow(x)

	cursor.close()
	conn.close()