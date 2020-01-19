import requests
import urllib
import json

wordarray = ['缘分一道桥','芒种','我和我的祖国','泡沫','出山','一曲相思','我曾','野狼disco']
#songmid
songmids = []
for word in wordarray:
    res1 = requests.get('https://c.y.qq.com/soso/fcgi-bin/client_search_cp?n=1&format=json&w='+word)
    jm1 = json.loads(res1.text.strip('callback()[]'))
    jm1 = jm1['data']['song']['list']
    for j in jm1:
        try:
            songmids.append(j['songmid'])
        except:
            print('wrong')

#m4a url
srcs = []
for songmid in songmids:
    res2 = requests.get('https://u.y.qq.com/cgi-bin/musicu.fcg?format=json&data=%7B%22req_0%22%3A%7B%22module%22%3A%22vkey.GetVkeyServer%22%2C%22method%22%3A%22CgiGetVkey%22%2C%22param%22%3A%7B%22guid%22%3A%222531283263%22%2C%22songmid%22%3A%5B%22'+songmid+'%22%5D%2C%22songtype%22%3A%5B0%5D%2C%22uin%22%3A%220%22%2C%22loginflag%22%3A1%2C%22platform%22%3A%2210%22%7D%7D%2C%22comm%22%3A%7B%22uin%22%3A%220%22%2C%22format%22%3A%22json%22%2C%22ct%22%3A24%2C%22cv%22%3A0%7D%7D')
    jm2 = json.loads(res2.text)
    wifiurl = 'http://dl.stream.qqmusic.qq.com/'+jm2['req_0']['data']["midurlinfo"][0]["wifiurl"]
    srcs.append(wifiurl)

i=0
for word in wordarray:
    print('***** '+word+'.m4a *****'+' Downloading...')
    try:
        print(srcs[i])
        urllib.request.urlretrieve(srcs[i],'E:/music/'+word+'.m4a')
        print('Download Complete!')
    except:
        print('Download wrong~')
    i=i+1;
