# 商品 API

## 概况

随便说两句

## 用户登录相关

key | value
------|------------
负责人 | 黄新泽
Email | rd02@yuanfeng021.com
Mobile | fafaff


### 登录
#### 请求URL
api.php?ctl=User&met=login&typ=json

#### 请求参数
键值 | 类型 | 描述
------|------|--------------
user_account | string | 用户名/email/手机

#### 返回数据
```json
{
    user_id:用户id
    k:认证信息，每次发送请求必须带的参数
}
```
### 注册验证码
#### 请求URL
api.php?ctl=User&met=regCode&typ=json

#### 请求参数
键值 | 类型 | 描述
------|------|--------------
mobile | string | 手机

#### 返回数据
```json
{
    user_code:验证码 //测试使用,正式会通过手机短信接收
}
```

