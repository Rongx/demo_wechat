<style type="text/css">

     .loading-mask {
         display          :none;
         position         :absolute;
         top              :0;
         left             :0;
         z-index          :9999;
         width            :100%;
         height           :100%;
         background-color :rgba(0, 0, 0, 0.8);
     }
       @keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
     .loading {
         display: none;
         position: absolute;
         top: 0;
         left: 0;
         z-index: 10000;
         width: 36px;
         height: 36px;
         top: 440px; left: 941.5px; display: none;
     }
     .loading .inner {
         width: 30px;
         height: 30px;
         border-radius: 30px;
         border-width: 3px;
         border-style: solid;
         border-color: rgba(0,183,229,0.95) rgba(0,183,229,0.9) rgba(0,183,229,0.3) rgba(0,183,229,0.25);
         -webkit-animation: spin 1s infinite linear;
         animation: spin 1s infinite linear;
     }
     .loading .loading-text {
         position: absolute;
         top: 50%;
         left: 50%;
         z-index: 10001;
         margin: -14px 0 0 -14px;
         display: inline-block;
         width: 28px;
         height: 28px;
         line-height: 28px;
         text-align: center;
         font-size: 14px;
         font-weight: bold;
         color: #FFF;
         text-shadow: 0 1px 1px rgba(0,0,0,0.15);
     }

     .loading .loading-tips {
         position: absolute;
         top: 42px;
         left: 50%;
         z-index: 10;
         margin-left: -60px;
         width: 120px;
         height: 100%;
         text-align: center;
         font-size: 16px;
         color: #FFF;
     }

</style>



<!-- js-loading start -->
<div id="js-loading-mask" class="loading-mask"></div>
<div class="loading" id="js-loading" style="display: none; top: 320px; left: 933.5px;">
    <div class="inner"></div>
    <span class="loading-text">le</span>
    <p class="loading-tips">正在加载ing...</p>
</div>
<!-- js-loading end -->
