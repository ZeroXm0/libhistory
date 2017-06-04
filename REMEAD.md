
```
URL：http://test.zeroxm0.cn/lib/index.php
```
访问方式：POST

##### 参数
参数名     | 类型    |  描述  |示例
---|---|---|---
|username|String|用户名|2016101010|
|psw|String|密码|101010|
|fromdate|date|起始日期|2017-01-01|
|todate|date|终止日期|2017-05-05|


#### JSON示例
```

{
    "code": 200,
    "message": "succeed",
    "data": [
        {
            "order": "1",
            "book": "愿人生从容",
            "action": "还书",
            "date": "2017-05-29"
        },
        {
            "order": "2",
            "book": "哈哈哈，你是谁？",
            "action": "借书",
            "date": "2017-02-08"
        }
    ]
}

```

##### 返回值说明
|返回值    |说明         |
|:-------------:|:-------------|
|100	   |用户名或密码错误	 |
|101	   |参数缺失	 |
|199        |未知错误|
|200       |succeed	 |