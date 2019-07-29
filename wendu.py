#!/usr/bin/python
import RPi.GPIO as GPIO
import time
import pymysql


temp_arr=[]
humid_arr=[]

def read_air():
     #BCM编号方式的17对应树莓派的pin11
     channel = 改成对应自己的
     data = []
     j = 0

     #I/O口使用BCM编号方式
     GPIO.setmode(GPIO.BCM)

     time.sleep(1)

     #设置数据线为输出
     GPIO.setup(channel, GPIO.OUT)

     GPIO.output(channel, GPIO.LOW)
     time.sleep(0.02)
     GPIO.output(channel, GPIO.HIGH)

     #设置数据线为输入
     GPIO.setup(channel, GPIO.IN)

     while GPIO.input(channel) == GPIO.LOW:
          continue

     while GPIO.input(channel) == GPIO.HIGH:
          continue

     while j < 40:
          k = 0
          while GPIO.input(channel) == GPIO.LOW:
              continue

          while GPIO.input(channel) == GPIO.HIGH:
              k += 1
              if k > 100:
                  break

          if k < 8:
              data.append(0)
          else:
              data.append(1)

          j += 1

     print ("Sensor is working.")
     print (data)

     #读取数值
     humidity_bit = data[0:8]
     humidity_point_bit = data[8:16]
     temperature_bit = data[16:24]
     temperature_point_bit = data[24:32]
     check_bit = data[32:40]

     humidity = 0
     humidity_point = 0
     temperature = 0
     temperature_point = 0
     check = 0

     #转换数值
     for i in range(8):
          humidity += humidity_bit[i] * 2 ** (7 - i)
          humidity_point += humidity_point_bit[i] * 2 ** (7 - i)
          temperature += temperature_bit[i] * 2 ** (7 - i)
          temperature_point += temperature_point_bit[i] * 2 ** (7 - i)
          check += check_bit[i] * 2 ** (7 - i)

     tmp = humidity + humidity_point + temperature + temperature_point

     #数据校验
     if check == tmp:
          print ("温度: ", temperature, ",湿度 : " , humidity)
     else:
          print ("Sorry! There is Something Wrong!!! - LanHao Tech.")
          print( "Temperature : ", temperature, ", Humidity : " , humidity, " check : ", check, " tmp : ", tmp)
     temp_arr.append(temperature)
     humid_arr.append(humidity)
     GPIO.cleanup()



     
#数据转换成JSON格式

def upload(temperature,humidity):
     db = pymysql.connect("数据库地址","数据库用户名","数据库密码","数据库名")
     cursor = db.cursor()
     ts=str(time.time())
     timm=ts.split(".")
     sql = "insert into 表名(temperature,humidity,time)\
            VALUES ( %s, %s, '%s')" % \
            (temperature, humidity, timm[0])

     # 使用 execute()  方法执行 SQL 查询 
     cursor.execute(sql)
     db.commit()
     db.close()

# Program to find most frequent 
# element in a list 
def most_frequent(arr):
     counter=0
     num=arr[0]
     for i in arr:
          curr_fre=arr.count(i)
          if(curr_fre>counter):
               counter=curr_fre
               num=i
     return num

if __name__ == "__main__":
     for i in range(0,10):
          read_air()
     upload(most_frequent(temp_arr),most_frequent(humid_arr))
     print ("温度 "+str(most_frequent(temp_arr))+" 湿度 "+str(most_frequent(humid_arr)))
