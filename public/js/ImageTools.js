(function (window, document, undefined) {
  var ImageTools = function (dom, options) {
    if (!(this instanceof ImageTools)) return new ImageTools(dom, options);
    this.dom = dom;
    this.options = this.setOptions(options, {
      zoomVal: 0.5,
      left: 0,
      top: 0,
      currentX: 0,
      currentY: 0,
      flag: false,
    });
  };

  ImageTools.prototype = {
    setOptions: function (options, defaults) {
      if (defaults === undefined) {
        defaults = this.options;
      }

      for (var k in options) {
        defaults[k] = options[k];
      }
      return defaults;
    },
    //获取相关css属性
    getCss: function (o, key) {
      return o.currentStyle
        ? o.currentStyle[key]
        : document.defaultView.getComputedStyle(o, false)[key];
    },
    bbimg: function (o) {
      let params = this.options;
      $(this.dom).on("mousewheel", (event) => {
        var o = document.querySelector(this.dom);
        params.zoomVal += event.originalEvent.wheelDelta / 1200;
        if (params.zoomVal >= 0.2) {
          o.style.transform = "scale(" + params.zoomVal + ")";
        } else {
          params.zoomVal = 0.2;
          o.style.transform = "scale(" + params.zoomVal + ")";
          return false;
        }
      });
      //   var o = o.getElementsByTagName("img")[0];
      //   this.options.zoomVal += event.wheelDelta / 1200;
      //   if (this.options.zoomVal >= 0.2) {
      //     o.style.transform = "scale(" + this.options.zoomVal + ")";
      //   } else {
      // 	this.options.zoomVal = 0.2;
      //     o.style.transform = "scale(" + this.options.zoomVal + ")";
      //     return false;
      //   }
    },
    //拖拽的实现
    startDrag: function (bar, target, callback) {
      let params = this.options;
      let self = this;
      if (this.getCss(target, "left") !== "auto") {
        params.left = this.getCss(target, "left");
      }
      if (this.getCss(target, "top") !== "auto") {
        params.top = this.getCss(target, "top");
      }
      //o是移动对象
      bar.oncontextmenu = function (event) {
        return false;
      }
      bar.onmousedown = function (event) {
        event.stopPropagation();
        var code = event.which;
        if(code != 3){
          return
        }
       
        params.flag = true;
        if (!event) {
          event = window.event;
          //防止IE文字选中
          bar.onselectstart = function () {
            return false;
          };
        }
        var e = event;
        params.currentX = e.clientX;
        params.currentY = e.clientY;
      };
      document.onmouseup = function () {
        params.flag = false;
        if (self.getCss(target, "left") !== "auto") {
          params.left = self.getCss(target, "left");
        }
        if (self.getCss(target, "top") !== "auto") {
          params.top = self.getCss(target, "top");
        }
      };
      document.onmousemove = function (event) {
        var e = event ? event : window.event;
        if (params.flag) {
          var nowX = e.clientX,
            nowY = e.clientY;
          var disX = nowX - params.currentX,
            disY = nowY - params.currentY;
          target.style.left = parseInt(params.left) + disX + "px";
          target.style.top = parseInt(params.top) + disY + "px";
          if (typeof callback == "function") {
            callback(
              (parseInt(params.left) || 0) + disX,
              (parseInt(params.top) || 0) + disY
            );
          }
          if (event.preventDefault) {
            event.preventDefault();
          }
          return false;
        }
      };
    },
  };

  ImageTools.init = function (element,options) {
    var imageTools = new ImageTools(element,options);
    imageTools.bbimg();
    $(element).on("mousemove", (e) => {
      imageTools.options.offsetX = e.offsetX;
      imageTools.options.offsetY = e.offsetY;
    });
    imageTools.startDrag(
      document.querySelector(element),
      document.querySelector(element)
    );
    return imageTools;
  };

  window.ImageTools = ImageTools;
})(window, document);
