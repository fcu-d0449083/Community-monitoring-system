import cv2
import smtplib
import face_recognition
import datetime
import csv
import MySQLdb
import os
import downloadcsv

'''操作說明

需要pip install opencv相關套件
pip install opencv-contrib-python
pip install cmake
pip install dlib or wheel
#use python 3.6
#https://pypi.org/simple/dlib/

pip install face_recognition

'''


SQLparams = {
    'user': 'root', 
    'password': 'a92015a92015',
    'host': 'localhost'
}

pid = str(os.getpid())
f = open('pid.txt','w')
f.write(pid)

downloadcsv.downloadcsvfun()

conn=MySQLdb.connect(**SQLparams)
cursor=conn.cursor()
SQL="CREATE DATABASE IF NOT EXISTS Timedb DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_unicode_ci" 
cursor.execute(SQL)
conn.commit()
cursor.close()
conn.close()

conn=MySQLdb.connect(**SQLparams, db="Timedb", charset="utf8")
cursor=conn.cursor()
SQL="CREATE TABLE IF NOT EXISTS time( user_name VARCHAR(20),\
year INT(4), month TINYINT(2),day TINYINT(2), \
hour TINYINT(2), start_minute TINYINT(2),start_second TINYINT(2), \
end_minute TINYINT(2),end_second TINYINT(2))"
cursor.execute(SQL)
conn.commit() 



#face detect
facePath = "haarcascade_frontalface_default.xml"
faceCascade = cv2.CascadeClassifier(facePath)

with open('snapshot.csv', 'r') as csvFile:
    reader = csv.reader(csvFile)
    imageStorePlace = list(reader)
csvFile.close()

#print(imageStorePlace)
#print(type(imageStorePlace))
#del imageStorePlace[0]

with open('nameonly.csv', 'r') as csvFile:
    reader = csv.reader(csvFile)
    face_name = list(reader)
csvFile.close()

#print(face_name)
#del face_name[0]

count = 0
#face recognition
for i in imageStorePlace:

	sto = str(imageStorePlace[count])
	sto = sto.replace("'","",2)
	sto = sto.replace("`","",2)
	sto = sto.replace("[","")
	sto = sto.replace("]","")
	sto = sto.replace("\/","\\")
#    print(sto)
#    print(type(sto))
    
	image = face_recognition.load_image_file(sto)
	encodings = face_recognition.face_encodings(image)
	if len(encodings) > 0:
		
		count = count+1
	else:
		print("No faces found in the image!")
		del face_name[count]
		del imageStorePlace[count]
	del image	

face_encoding_list = []
count = 0
print(len(imageStorePlace))
for i in imageStorePlace:
    print(count)
    
    sto = str(imageStorePlace[count])
    sto = sto.replace("'","",2)
    sto = sto.replace("`","",2)
    sto = sto.replace("[","")
    sto = sto.replace("]","")
    sto = sto.replace("\/","\\")
#    print(sto)
#    print(type(sto))
    
    image = face_recognition.load_image_file(sto)
    #image = face_recognition.load_image_file("t3.jpg")
    encodings = face_recognition.face_encodings(image,num_jitters=3)
    if len(encodings) > 0:
        face_encoding_list.append(face_recognition.face_encodings(image)[0])
        count = count+1
    else:
        print("No faces found in the image!")
        del face_name[count]
        del imageStorePlace[count]
    
    del image

####################

print(len(face_name), len(imageStorePlace), len(face_encoding_list))
print(face_name)
print(imageStorePlace,)

def sava_image():
    pass

# get camera 0 or 1 
#需要確認羅技攝影機的編號
cap = cv2.VideoCapture(0)
process_this_frame = True
face_locations = []
face_encodings = []
face_names = []
x = datetime.datetime.now()
date = ["",x.strftime("%Y"),x.strftime("%m"),x.strftime("%d"),x.strftime("%H"),x.strftime("%M"),x.strftime("%S"),"",""]
name = "Unknown"

endtime_now = int(x.strftime("%S"))
endtime_new = int(x.strftime("%S"))
reflashCheck = False
changeCheck = False

while(True):
    # get picture from camera
    ret, frame = cap.read()
    x = datetime.datetime.now()
    endtime_now = int(x.strftime("%S"))
    
    
    #設定目標人臉 越小越快執行 但人臉在影像中必須越大
    small_frame = cv2.resize(frame, (0, 0), fx=0.5, fy=0.5)
    small_frame = small_frame[:, :, ::-1]

    if process_this_frame:
        face_locations = face_recognition.face_locations(small_frame)
        face_encodings = face_recognition.face_encodings(small_frame, face_locations)
        face_names = []
        for face_encoding in face_encodings:
            matches = face_recognition.compare_faces(face_encoding_list, face_encoding,tolerance=0.4)
            name = "Unknown"
            
            if True in matches:
                first_match_index = matches.index(True)
                name = str(face_name[first_match_index])
                cv2.imwrite("C:\\AppServ\www\demo\show\showimage.jpg", frame)
                f = open('C:\\AppServ\www\demo\show\monitor.txt','w')
                f.write(name)
                #print (datetime.datetime.now(), "yes")
                x = datetime.datetime.now()
                
                if name != date[0] :
                    changeCheck = True
                
                SQL="INSERT INTO time(user_name,year,month,day,hour,start_minute,start_second,end_minute,end_second) \
                VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s)"
                date = [name,x.strftime("%Y"),x.strftime("%m"),x.strftime("%d"),x.strftime("%H"),"","",x.strftime("%M"),x.strftime("%S")]
                if reflashCheck == False:
                    endtime_old = x.strftime("%S")
                    endtime_old_min = x.strftime("%M")
                endtime_new = int(x.strftime("%S"))
                reflashCheck = True
                #cursor.execute(SQL, date)
                #del date
                #conn.commit()
            else:
#                print(name)
                cv2.imwrite("C:\\AppServ\www\demo\show\showimage.jpg", frame)
                f = open('C:\\AppServ\www\demo\show\monitor.txt','w')
                f.write(name)
#                print (datetime.datetime.now(), "no")
                x = datetime.datetime.now()
                SQL="INSERT INTO time(user_name,year,month,day,hour,start_minute,start_second,end_minute,end_second) \
                VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s)"
                date = [name,x.strftime("%Y"),x.strftime("%m"),x.strftime("%d"),x.strftime("%H"),"","",x.strftime("%M"),x.strftime("%S")]
                if reflashCheck == False:
                    endtime_old = x.strftime("%S")
                    endtime_old_min = x.strftime("%M")
                endtime_new = int(x.strftime("%S"))
                reflashCheck = True
                
                #cursor.execute(SQL, date)
                #del date
                #conn.commit() 
            face_names.append(name)
            
        
        if date[0] == name and changeCheck == False:
            endtime_now = int(x.strftime("%S"))
            if endtime_new > endtime_now:
                endtime_now = endtime_now+60
            ss = endtime_now - endtime_new
            #print(ss,endtime_now,endtime_old)
            if ss > 3 and reflashCheck == True:
                date[5] = endtime_old_min
                date[6] = endtime_old
                print(date)
                print("T")
                reflashCheck = False
                changeCheck = False
                cursor.execute(SQL, date)
            #del date
                conn.commit()
        elif changeCheck == True and reflashCheck == True:
            date[5] = endtime_old_min
            date[6] = endtime_old
            print(date)
            print("C")
            reflashCheck = False
            changeCheck = False
            cursor.execute(SQL, date)
            conn.commit()
				
	#偵測到人臉時顯示(框出人臉)
    #process_this_frame = not process_this_frame

    for (top, right, bottom, left), name in zip(face_locations, face_names):
        top *= 2
        right *= 2
        bottom *= 2
        left *= 2
        cv2.rectangle(frame, (left, top), (right, bottom), (255, 255, 0), 2)
        cv2.rectangle(frame, (left, bottom - 10), (right, bottom), (255, 255, 0), cv2.FILLED)
        font = cv2.FONT_HERSHEY_DUPLEX
        cv2.putText(frame, name, (left + 6, bottom - 6), font, 0.6, (255, 255, 255), 1)

    #gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    #faces = faceCascade.detectMultiScale(gray, 1.3, 5)
    

    #for (x,y,w,h) in faces:
     #   cv2.rectangle(frame,(x,y),(x+w,y+h),(255,0,0),2)
        
    endtime_now = int(x.strftime("%S"))
    # show image
    cv2.imshow('frame', frame)
  
    f = open('check.txt','r')
    check = str(f.read())
    checker = "0"

    # detect key press
    keypress = cv2.waitKey(1) & 0xFF
    if  keypress == ord('q'):
        print('quit')

        break
    elif check == "0":
        f = open('check.txt','w')
        f.write("1")
        break
    
    elif keypress == ord('c'):
        #點擊C可以將當前影像儲存到當前執行檔的資料夾
        cv2.imwrite("tmp.jpg", frame)
        print('capture')

   
    elif keypress<255:
        print('not def')

# relase camera
cap.release()

# close windows
cv2.destroyAllWindows()