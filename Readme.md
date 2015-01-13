Bash painter
===================================

部署
-----------------------------------

这是一个纯静态的页面，将代码部署在web服务器上即可访问(比如github)，你可以访问我的<br>
[liuyuekeng.github.io/bashpainter/](http://liuyuekeng.github.io/bashpainter/)

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

实现原理
-----------------------------------

### 流程

获取图片信息=>图像处理=>颜色处理=>生成ANSI控制符=>输出

### 图片上传

获取用户图片，使用标签

    <input type="file" >
出于安全的考虑，我们并不能拿到用户选择的本地文件完整路径。
[http://stackoverflow.com/questions/15201071/how-to-get-full-path-of-selected-file-on-change-of-input-type-file-using-jav](How to get full path of selected file on change of <input type=‘file’> using javascript, jquery-ajax?)
而实际上，我们并不需要得到完整路径，也可以使用用户选择的文件。
通过URL.createObjectURL方法可以让浏览器为我们生成一个虚拟路径，可以像普通路径一样引用。
[https://developer.mozilla.org/zh-CN/docs/Web/API/URL.createObjectURL](API文档)
[http://caniuse.com/#search=createObjectURL](兼容性)

### 图像处理

利用canva进行图像处理的工作，主要有缩放，取得图像像素信息两个操作。
前者是在drawImage的时候指定宽高，后者是使用getImageData方法。
getImageData取得的对象包含width,height,data三个属性，其中data是一个数组，包含像素的颜色信息，形如

    [R,G,B,A,R,G,B,A,R,G,B,A...]

### 颜色处理

使用ANSI控制符控制输出颜色有一个限制，只能支持216色的模式，也就是所谓的安全色。
RGB每一个原色只支持6阶。6*6*6 = 216
简单来说就是建立一个0~255 =》 0~6 的映射。得到的rgb三个数值结果我们将它视为6进制的三位，转换成10进制

    num = r * 36 + g * 6 + b

由于前面有16个特殊颜色位，所以最终要的颜色编码要加上16的偏移量，这个值一会儿就回用到。

### ANSI控制字符

[http://en.wikipedia.org/wiki/ANSI_escape_code](ANSI_escape_code)
在维基百科查到有这么多得控制符，这里我们只会用到两个
![escapecode](https://raw.githubusercontent.com/liuyuekeng/staticFilesForReadme/master/bashpainter/escapecode.png)<br>
前景色和背景色的设置
设定背景色之后打一个空格，会有一个长条形的方块，然后我们打出这个字符▄，他占据了空格位置一半的高度，所以我们在每一个空格的位置可以分成上下两块，分别用背景色和字符色进行填充，构成基本的色块单位。

  echo -e "\033[38;5;1m\033[48;5;2m▄\033[0m"

![show-escapecode](https://raw.githubusercontent.com/liuyuekeng/staticFilesForReadme/master/bashpainter/show-escapecode.png)<br>
这段代码\033是esc对应地编码，随后设置了背景绿色，字符红色，打出一个方块，最后返回默认设置
