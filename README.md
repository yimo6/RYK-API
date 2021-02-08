# RYK-API

一个菜鸡写的获取Liunx服务器运行信息的API

## 目录结构

```
 ├─ api.php     //API调用示例
 ├─ config.php  //配置项
 └─ RYK-API.php //核心文件
```

## 安装

### 1.下载ZIP

### 2.Git

```
git clone https://github.com/yimo6/RYK-API.git
```


## 使用示例

请求地址: http://Test/api.php?token=e10adc3949ba59abbe56e057f20f883e

### (正常)返回结果
```
{
	"code": 200,
	"msg": "Success",
	"data": {
		"cpu_name": "Intel(R) Xeon(R) CPU E5-2695 v2 @ 2.40GHz",  //CPU名称
		"cpu_num": "8",    //CPU数量
		"cpu_used": 4,     //CPU使用率(%)
		"mem": {
			"total": "3770", //总内存
			"used": "2497",  //使用中内存
			"free": "888"    //空闲内存
		},
		"network": {
			"upload": 7497,  //上传传输速度(单位:B)
			"download": 3182 //下载传输速度(单位:B)
		}
	}
}
```
