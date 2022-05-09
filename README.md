# My Music Player  
<p align="center"><img src="public/images/music.png" alt="logo"/></p>
<br>
<p align="center">
<a target="_blank" href="https://github.com/UtopiaXC/My-Music-Player/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-green" alt="license-mit"/></a>
<a target="_blank" href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-8.83.11-%23ff5252" alt="Laravel"/></a>
<a target="_blank" href="https://vuejs.org/"><img src="https://img.shields.io/badge/Vue-2.x-%2343a047" alt="Vue"/></a>
<a target="_blank" href="https://www.utopiaxc.cn/"><img src="https://img.shields.io/badge/Author-UtopiaXC-%23ae52d4" alt="Author"/></a>
<a target="_blank" href="https://github.com/UtopiaXC/My-Music-Player"><img src="https://img.shields.io/badge/GitHub-My%20Music%20Player-%236d6d6d" alt="GitHub"/></a>
<a target="_blank" href="https://git.utopiaxc.cn/UtopiaXC/my-music-player"><img src="https://img.shields.io/badge/GitLab-My%20Music%20Player-%23ff8a65" alt="GitLab"/></a>
</p>
<br>
这是一个本地化部署的基于Laravel的调用网易云API的网页音乐播放器。  
This is a localized deployment of Laravel-based web music player calling NetEase Cloud Music API  

## 开源使用  
本软件采用开源前端仓库[mini-player](https://github.com/muhammed/mini-player)与开源后端框架[Laravel](https://github.com/laravel/laravel)构建  
本软件为开源软件，采用MIT协议  
[GitHub](https://github.com/UtopiaXC/My-Music-Player) | [GitLab](https://git.utopiaxc.cn/UtopiaXC/my-music-player)

## 简介
这是一个用于博客网站本地化部署的音乐播放器，采用Laravel调用网易云音乐接口进行歌单播放。  

## 演示地址
[UtopiaXC's Music Player](https://music.utopiaxc.cn/) 

## 私有化部署
### 下载  
请直接使用Release下载。如果希望体验开发中的内容可以直接使用git clone。  
首先，创建Nginx或Apache网站根目录，然后进入。进入后执行下方指令中的一条即可，两者唯一的区别是下载源不同。  
```bash
cd /xxx/xxx # 将/xxx/xxx替换为你自己的网站根目录

# 如果你的服务器不在中国大陆，请使用本条
wget https://github.com/UtopiaXC/My-Music-Player/archive/refs/tags/1.0.0.tar.gz && tar -zxvf 1.0.0.tar.gz && cp -r My-Music-Player-1.0.0/. ./ && rm -rf 1.0.0.tar.gz My-Music-Player-1.0.0/ && chmod 755 ./

# 如果你的服务器在中国大陆，请使用本条
wget https://git.utopiaxc.cn/UtopiaXC/my-music-player/-/archive/1.0.0/my-music-player-1.0.0.tar.gz && tar -zxvf my-music-player-1.0.0.tar.gz && cp -r my-music-player-1.0.0/.  ./ && rm -rf my-music-player-1.0.0.tar.gz my-music-player-1.0.0/ && chmod 755 ./
```

### Nginx设置
请将Nginx的运行目录设置为public  

### Composer依赖
你需要自行安装Composer依赖，首先确认您是否已经安装Composer  
```bash
composer -V
```
如果显示出您的Composer版本则意味着您有着Composer，如果显示命令不存在，您需要先安装Composer  
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

然后，使用Composer安装php依赖包  
```bash
composer install
```

如果，您的服务器在国内导致下载一直被卡住，请替换源为国内源，以下命令任选一个运行即可  
```bash
# 全局换为阿里云源
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
# 全局换为腾讯云源
composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
# 全局换为中国全量镜像源
composer config -g repo.packagist composer https://packagist.phpcomposer.com

# 仅本工程换为阿里云源
composer config repo.packagist composer https://mirrors.aliyun.com/composer/
```

### 配置文件
您需要先生成一份自己的配置文件  
```bash
cp .env.example .env
```

### APP KEY
你需要生成一个APP KEY，你可以使用php脚手架进行生成，当然，你也可以通过网页进行生成。  
如果您希望使用脚手架生成，你需要保证php已经作为系统变量可以执行，然后执行以下命令  
```bash
php artisan key:generate
```
或者，对.env配置文件进行修改，暂时将APP_DEBUG更改为true，保存后访问您的域名，然后按照提示点击生成APP KEY，生成后将.env文件的APP_DEBUG改回false保证安全。  

### 配置文件
最后，你需要对配置文件进行修改。具体修改内容请参考注释。
```bash
vim .env
```
