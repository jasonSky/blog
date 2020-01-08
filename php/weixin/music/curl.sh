#!/bin/bash

xx=`cat txt`
IFS=$'\n\n'
for i in $xx
do
songmid=`echo $i |awk -F" " '{print $2}'`
echo $songmid
#urlstr="https://u.y.qq.com/cgi-bin/musicu.fcg?format=json&data={\"req_0\":{\"module\":\"vkey.GetVkeyServer\",\"method\":\"CgiGetVkey\",\"param\":{\"guid\":\"2531283263\",\"songmid\":[\"${songmid}\"],\"songtype\":[0],\"uin\":\"0\",\"loginflag\":1,\"platform\":\"20\"}},\"comm\":{\"uin\":\"0\",\"format\":\"json\",\"ct\":24,\"cv\":0}}"
urlstr='https://u.y.qq.com/cgi-bin/musicu.fcg?format=json&data=%7B%22req_0%22%3A%7B%22module%22%3A%22vkey.GetVkeyServer%22%2C%22method%22%3A%22CgiGetVkey%22%2C%22param%22%3A%7B%22guid%22%3A%222531283263%22%2C%22songmid%22%3A%5B%22'$songmid'%22%5D%2C%22songtype%22%3A%5B0%5D%2C%22uin%22%3A%220%22%2C%22loginflag%22%3A1%2C%22platform%22%3A%2210%22%7D%7D%2C%22comm%22%3A%7B%22uin%22%3A%220%22%2C%22format%22%3A%22json%22%2C%22ct%22%3A24%2C%22cv%22%3A0%7D%7D'
result=`curl --request GET --url $urlstr`
echo $result>${songmid}.txt
done
