<!DOCTYPE>
<html>
    <head></head>
    <body>
        每行字符数:<input type="text" id="colSize" value="120"><br>
        <input type="file" id="upLoadImg"><br>
        <input type="button" id="build" value="生成">
        <canvas id="myCanvas" width="10px" height="10px"></canvas><br>
        <textarea id="output" rows="30" cols="80"></textarea>
        <script>
;(function(){
    //公用参数
    var param = {
        width: '',      //缩放后绘制用的宽
        height: '',     //缩放后绘制用的高
        colSize: 120,   //默认每行字符数
        scale: 0,       //缩放比例
        ctx: {},        //canvas的context对象
        ret: [],        //bash中背景色编码
        str: "",        //最终生成的字符串
        filter: 231,    //排除底色，这里滤掉的是白色
        img: "",        //图片路径
        output: document.getElementById("output"),          //最终输出字符串的dome节点
        inputElement: document.getElementById("upLoadImg"),     //上传图片节点
        colSizeItem: document.getElementById("colSize"),        //设置宽度节点
        button: document.getElementById("build"),               //生成按钮
    }

    function paintInit () {
        //初始化canvas
        var canvas = document.getElementById('myCanvas');
        if (canvas.getContext) {
            param.ctx = canvas.getContext('2d');
        }

        //绘制图片
        var img = new Image();
        img.onload = function() {
            param.scale = param.colSize / img.width;
            console.log(param.scale);
            param.width = parseInt(img.width * param.scale);
            param.height = parseInt(img.height * param.scale);
            if ( param.height % 2 !== 0 ) {
                param.height += 1;
            }
            canvas.width = param.width;
            canvas.height = param.height;
            param.ctx.drawImage(img, 0, 0, param.width, param.height);
            //处理数据
            dealImg();
        }
        img.src = param.img;
    }

    //处理图片像素，用rgb值转成216颜色的编号（用于bash显示背景色）
    function dealImg () {
        var imageData = param.ctx.getImageData(0, 0, param.width, param.height);
        var d = imageData.data;
        var len = d.length - 4 * (param.width + 1);
        var lastNum = "";
        var _lastNum = "";
        param.ret = [];
        param.str = "\\033[0m";
        var count = 0;
        var r,g,b,i,_r,_g,_b,_i;
        for (var i = 0; i < len; i += 4) {
            //背景色
            r = rgbTo216(d[i]);
            d[i] = r * 51;
            g = rgbTo216(d[i + 1]);
            d[i + 1] = g * 51;
            b = rgbTo216(d[i + 2]);
            d[i +2] = b * 51;

            //前16个编码是标准色范围，跳过

            //前景色
            _i = i + (param.width * 4);
            _r = rgbTo216( d[_i] );
            d[_i] = _r * 51;
            _g = rgbTo216( d[_i + 1] );
            d[_i + 1] = _g * 51;
            _b = rgbTo216( d[_i + 2] );
            d[_i + 2] = _b * 51

            var num = r * 36 + g * 6 + b + 16;
            //param.ret.push(num);
            var _num = _r * 36 + _g * 6 + _b + 16;

            if(param.filter && num === param.filter){
                num = 0;
            }
            if(param.filter && _num === param.filter){
                _num = 0;
            }
            //判断是否跟前一个颜色值一致
            if(num === lastNum && _num === _lastNum){
                param.str += " ";
            } else if (num === _num) {
                param.str += ("\\033[48;5;" + num + "m ");
            } else {
                param.str += ("\\033[48;5;" + num + "m\\033[38;5;" + _num + "m▄");
            }

            //判断是否换行
            if ( (i / 4 + 1) % (param.width) === 0) {
                param.str += "\\033[0m\\n\\033[48;5;" + num + "m\\033[38;5;" + _num + "m";
                i += (param.width * 4);
            }
            count ++;

            lastNum = num;
            _lastNum = _num;
        }
        param.str += "\\033[0m"
        param.output.value = param.str;
        param.ctx.putImageData(imageData, 0, 0);
    }

    //取近似值，256阶=>6阶
    //@param    int     0~255
    function rgbTo216 (val) {
        var remainder = val % 51;
        var quotient = parseInt(val / 51);
        if ( remainder < 26 ) {
            return quotient;
        } else {
            return quotient + 1;
        }
    }

    param.button.onclick = function (e) {
        if (param.img && param.colSize) {
            paintInit();
        } else {
            alert("请先选择参数\nPlease fill params first");
        }
    }
    param.inputElement.onchange = function (e) {
        var fileType = e.target.files[0].type
        if (fileType === "image/jpeg" || fileType === "image/png" ) {
            param.img = URL.createObjectURL(e.target.files[0]);
        } else {
            alert("只支持jpg或png\nsupport jpg&png only");
        }
    }
    param.colSizeItem.onchange = function (e) {
        var val = e.target;
        param.colSize = val.value;
    }

})();

        </script>
    </body>
</html>
