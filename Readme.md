Bash painter
===================================

部署
-----------------------------------

这是一个纯静态的页面，将代码部署在web服务器上即可访问

使用方法
-----------------------------------

在页面中输入jpg或png格式的图片，
<br>选择每行字符的个数限制
<br>点击生成按钮，将输出一个字符串。
<br>在bash中输入指令<br>
    echo -e "[页面返回的字符串]"
<br>就可以看见字符画了
<br>Ps.
<br>可以在.bashrc文件末尾输出该字符串当启动欢迎图，或为脚本的说明，帮助信息添加彩图。
![example-doge](https://raw.githubusercontent.com/liuyuekeng/staticFilesForReadme/master/bashpainter/example-doge.jpg)
![example-doge](https://raw.githubusercontent.com/liuyuekeng/staticFilesForReadme/master/bashpainter/example-kin.jpg)

常见问题
-----------------------------------

### 屏幕宽度不够导致错行
在生成时适当减小每行字符的数目，适应自己的屏幕。

### 字符宽度不一致
宽字符导致图像变形，如图<br>
![example-doge](https://raw.githubusercontent.com/liuyuekeng/staticFilesForReadme/master/bashpainter/error-example1.jpg)<br>
以Xshell为例，在设置中把“不确定字符作宽字符处理”选项去掉<br>
![example-doge](https://raw.githubusercontent.com/liuyuekeng/staticFilesForReadme/master/bashpainter/error-example2.jpg)<br>
