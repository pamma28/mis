<!DOCTYPE html>
<html>
<head>
<style type="text/css">
 html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}
 body{margin:0}
 article,aside,details,figcaption,figure,footer,header,hgroup,main,menu,nav,section,summary{display:block}
 audio,canvas,progress,video{display:inline-block;vertical-align:baseline}
 audio:not([controls]){display:none;height:0}[hidden],
 template{display:none}
 a{background-color:transparent}
 a:active,a:hover{outline:0}
 button,input,optgroup,select,textarea{color:inherit;font:inherit;margin:0}
 button{overflow:visible}
 button,select{text-transform:none}
 button[disabled],html input[disabled]{cursor:default}
 button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}
 table{border-collapse:collapse;border-spacing:0}
 td,th{padding:0}*{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}*:before,*:after{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}
 html{font-size:10px;-webkit-tap-highlight-color:rgba(0,0,0,0)}
 body{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:13px;line-height:1.42857143;color:#333;background-color:#fff}
 input,button,select,textarea{font-family:inherit;font-size:inherit;line-height:inherit}
 a{color:#337ab7;text-decoration:none}
 a:hover,a:focus{color:#23527c;text-decoration:underline}
 a:focus{outline:5px auto -webkit-focus-ring-color;outline-offset:-2px}
 figure{margin:0}
 img{vertical-align:middle}
.img-responsive,.thumbnail>img,.thumbnail a>img{display:block;max-width:100%;height:auto}
.img-rounded{border-radius:6px}
.img-thumbnail{padding:4px;line-height:1.42857143;background-color:#fff;border:1px solid #ddd;border-radius:4px;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;transition:all .2s ease-in-out;display:inline-block;max-width:100%;height:auto}
.img-circle{border-radius:50%}hr{margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee}
.sr-only{position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0, 0, 0, 0);border:0}
.sr-only-focusable:active,.sr-only-focusable:focus{position:static;width:auto;height:auto;margin:0;overflow:visible;clip:auto}[role="button"]{cursor:pointer}h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6{font-family:inherit;font-weight:500;line-height:1.1;color:inherit}h1 small,h2 small,h3 small,h4 small,h5 small,h6 small,.h1 small,.h2 small,.h3 small,.h4 small,.h5 small,.h6 small,h1 .small,h2 .small,h3 .small,h4 .small,h5 .small,h6 .small,.h1 .small,.h2 .small,.h3 .small,.h4 .small,.h5 .small,.h6 .small{font-weight:normal;line-height:1;color:#777}h1,.h1,h2,.h2,h3,.h3{margin-top:20px;margin-bottom:10px}h1 small,.h1 small,h2 small,.h2 small,h3 small,.h3 small,h1 .small,.h1 .small,h2 .small,.h2 .small,h3 .small,.h3 .small{font-size:65%}h4,.h4,h5,.h5,h6,.h6{margin-top:10px;margin-bottom:10px}h4 small,.h4 small,h5 small,.h5 small,h6 small,.h6 small,h4 .small,.h4 .small,h5 .small,.h5 .small,h6 .small,.h6 .small{font-size:75%}h1,.h1{font-size:36px}h2,.h2{font-size:30px}h3,.h3{font-size:24px}h4,.h4{font-size:18px}h5,.h5{font-size:14px}h6,.h6{font-size:12px}p{margin:0 0 10px}
.lead{margin-bottom:20px;font-size:16px;font-weight:300;line-height:1.4}@media (min-width:768px){.lead{font-size:21px}}small,.small{font-size:85%}mark,.mark{background-color:#fcf8e3;padding:.2em}
.text-left{text-align:left}
.text-right{text-align:right}
.text-center{text-align:center}
.text-justify{text-align:justify}
.text-nowrap{white-space:nowrap}
.text-primary{color:#337ab7}a.text-primary:hover,a.text-primary:focus{color:#286090}
.text-success{color:#3c763d}a.text-success:hover,a.text-success:focus{color:#2b542c}
.text-info{color:#31708f}a.text-info:hover,a.text-info:focus{color:#245269}
.text-warning{color:#8a6d3b}a.text-warning:hover,a.text-warning:focus{color:#66512c}
.text-danger{color:#a94442}a.text-danger:hover,a.text-danger:focus{color:#843534}
.bg-primary{color:#fff;background-color:#337ab7}a.bg-primary:hover,a.bg-primary:focus{background-color:#286090}
.bg-success{background-color:#dff0d8}a.bg-success:hover,a.bg-success:focus{background-color:#c1e2b3}
.bg-info{background-color:#d9edf7}a.bg-info:hover,a.bg-info:focus{background-color:#afd9ee}
.bg-warning{background-color:#fcf8e3}a.bg-warning:hover,a.bg-warning:focus{background-color:#f7ecb5}
.bg-danger{background-color:#f2dede}a.bg-danger:hover,a.bg-danger:focus{background-color:#e4b9b9}
.row{margin-left:-15px;margin-right:-15px}
.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12{position:relative;min-height:1px;padding-left:15px;padding-right:15px}
.col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12{float:left}
.col-xs-12{width:100%}
.col-xs-11{width:91.66666667%}
.col-xs-10{width:83.33333333%}
.col-xs-9{width:75%}
.col-xs-8{width:66.66666667%}
.col-xs-7{width:58.33333333%}
.col-xs-6{width:50%}
.col-xs-5{width:41.66666667%}
.col-xs-4{width:33.33333333%}
.col-xs-3{width:25%}
.col-xs-2{width:16.66666667%}
.col-xs-1{width:8.33333333%}
.col-xs-pull-12{right:100%}
.col-xs-pull-11{right:91.66666667%}
.col-xs-pull-10{right:83.33333333%}
.col-xs-pull-9{right:75%}
.col-xs-pull-8{right:66.66666667%}
.col-xs-pull-7{right:58.33333333%}
.col-xs-pull-6{right:50%}
.col-xs-pull-5{right:41.66666667%}
.col-xs-pull-4{right:33.33333333%}
.col-xs-pull-3{right:25%}
.col-xs-pull-2{right:16.66666667%}
.col-xs-pull-1{right:8.33333333%}
.col-xs-pull-0{right:auto}
.col-xs-push-12{left:100%}
.col-xs-push-11{left:91.66666667%}
.col-xs-push-10{left:83.33333333%}
.col-xs-push-9{left:75%}
.col-xs-push-8{left:66.66666667%}
.col-xs-push-7{left:58.33333333%}
.col-xs-push-6{left:50%}
.col-xs-push-5{left:41.66666667%}
.col-xs-push-4{left:33.33333333%}
.col-xs-push-3{left:25%}
.col-xs-push-2{left:16.66666667%}
.col-xs-push-1{left:8.33333333%}
.col-xs-push-0{left:auto}
.col-xs-offset-12{margin-left:100%}
.col-xs-offset-11{margin-left:91.66666667%}
.col-xs-offset-10{margin-left:83.33333333%}
.col-xs-offset-9{margin-left:75%}
.col-xs-offset-8{margin-left:66.66666667%}
.col-xs-offset-7{margin-left:58.33333333%}
.col-xs-offset-6{margin-left:50%}
.col-xs-offset-5{margin-left:41.66666667%}
.col-xs-offset-4{margin-left:33.33333333%}
.col-xs-offset-3{margin-left:25%}
.col-xs-offset-2{margin-left:16.66666667%}
.col-xs-offset-1{margin-left:8.33333333%}
.col-xs-offset-0{margin-left:0}@media (min-width:768px){.col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12{float:left}
.col-sm-12{width:100%}
.col-sm-11{width:91.66666667%}
.col-sm-10{width:83.33333333%}
.col-sm-9{width:75%}
.col-sm-8{width:66.66666667%}
.col-sm-7{width:58.33333333%}
.col-sm-6{width:50%}
.col-sm-5{width:41.66666667%}
.col-sm-4{width:33.33333333%}
.col-sm-3{width:25%}
.col-sm-2{width:16.66666667%}
.col-sm-1{width:8.33333333%}
.col-sm-pull-12{right:100%}
.col-sm-pull-11{right:91.66666667%}
.col-sm-pull-10{right:83.33333333%}
.col-sm-pull-9{right:75%}
.col-sm-pull-8{right:66.66666667%}
.col-sm-pull-7{right:58.33333333%}
.col-sm-pull-6{right:50%}
.col-sm-pull-5{right:41.66666667%}
.col-sm-pull-4{right:33.33333333%}
.col-sm-pull-3{right:25%}
.col-sm-pull-2{right:16.66666667%}
.col-sm-pull-1{right:8.33333333%}
.col-sm-pull-0{right:auto}
.col-sm-push-12{left:100%}
.col-sm-push-11{left:91.66666667%}
.col-sm-push-10{left:83.33333333%}
.col-sm-push-9{left:75%}
.col-sm-push-8{left:66.66666667%}
.col-sm-push-7{left:58.33333333%}
.col-sm-push-6{left:50%}
.col-sm-push-5{left:41.66666667%}
.col-sm-push-4{left:33.33333333%}
.col-sm-push-3{left:25%}
.col-sm-push-2{left:16.66666667%}
.col-sm-push-1{left:8.33333333%}
.col-sm-push-0{left:auto}
.col-sm-offset-12{margin-left:100%}
.col-sm-offset-11{margin-left:91.66666667%}
.col-sm-offset-10{margin-left:83.33333333%}
.col-sm-offset-9{margin-left:75%}
.col-sm-offset-8{margin-left:66.66666667%}
.col-sm-offset-7{margin-left:58.33333333%}
.col-sm-offset-6{margin-left:50%}
.col-sm-offset-5{margin-left:41.66666667%}
.col-sm-offset-4{margin-left:33.33333333%}
.col-sm-offset-3{margin-left:25%}
.col-sm-offset-2{margin-left:16.66666667%}
.col-sm-offset-1{margin-left:8.33333333%}
.col-sm-offset-0{margin-left:0}}@media (min-width:992px){.col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12{float:left}
.col-md-12{width:100%}
.col-md-11{width:91.66666667%}
.col-md-10{width:83.33333333%}
.col-md-9{width:75%}
.col-md-8{width:66.66666667%}
.col-md-7{width:58.33333333%}
.col-md-6{width:50%}
.col-md-5{width:41.66666667%}
.col-md-4{width:33.33333333%}
.col-md-3{width:25%}
.col-md-2{width:16.66666667%}
.col-md-1{width:8.33333333%}
.col-md-pull-12{right:100%}
.col-md-pull-11{right:91.66666667%}
.col-md-pull-10{right:83.33333333%}
.col-md-pull-9{right:75%}
.col-md-pull-8{right:66.66666667%}
.col-md-pull-7{right:58.33333333%}
.col-md-pull-6{right:50%}
.col-md-pull-5{right:41.66666667%}
.col-md-pull-4{right:33.33333333%}
.col-md-pull-3{right:25%}
.col-md-pull-2{right:16.66666667%}
.col-md-pull-1{right:8.33333333%}
.col-md-pull-0{right:auto}
.col-md-push-12{left:100%}
.col-md-push-11{left:91.66666667%}
.col-md-push-10{left:83.33333333%}
.col-md-push-9{left:75%}
.col-md-push-8{left:66.66666667%}
.col-md-push-7{left:58.33333333%}
.col-md-push-6{left:50%}
.col-md-push-5{left:41.66666667%}
.col-md-push-4{left:33.33333333%}
.col-md-push-3{left:25%}
.col-md-push-2{left:16.66666667%}
.col-md-push-1{left:8.33333333%}
.col-md-push-0{left:auto}
.col-md-offset-12{margin-left:100%}
.col-md-offset-11{margin-left:91.66666667%}
.col-md-offset-10{margin-left:83.33333333%}
.col-md-offset-9{margin-left:75%}
.col-md-offset-8{margin-left:66.66666667%}
.col-md-offset-7{margin-left:58.33333333%}
.col-md-offset-6{margin-left:50%}
.col-md-offset-5{margin-left:41.66666667%}
.col-md-offset-4{margin-left:33.33333333%}
.col-md-offset-3{margin-left:25%}
.col-md-offset-2{margin-left:16.66666667%}
.col-md-offset-1{margin-left:8.33333333%}
.col-md-offset-0{margin-left:0}}@media (min-width:1200px){.col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12{float:left}
.col-lg-12{width:100%}
.col-lg-11{width:91.66666667%}
.col-lg-10{width:83.33333333%}
.col-lg-9{width:75%}
.col-lg-8{width:66.66666667%}
.col-lg-7{width:58.33333333%}
.col-lg-6{width:50%}
.col-lg-5{width:41.66666667%}
.col-lg-4{width:33.33333333%}
.col-lg-3{width:25%}
.col-lg-2{width:16.66666667%}
.col-lg-1{width:8.33333333%}
.col-lg-pull-12{right:100%}
.col-lg-pull-11{right:91.66666667%}
.col-lg-pull-10{right:83.33333333%}
.col-lg-pull-9{right:75%}
.col-lg-pull-8{right:66.66666667%}
.col-lg-pull-7{right:58.33333333%}
.col-lg-pull-6{right:50%}
.col-lg-pull-5{right:41.66666667%}
.col-lg-pull-4{right:33.33333333%}
.col-lg-pull-3{right:25%}
.col-lg-pull-2{right:16.66666667%}
.col-lg-pull-1{right:8.33333333%}
.col-lg-pull-0{right:auto}
.col-lg-push-12{left:100%}
.col-lg-push-11{left:91.66666667%}
.col-lg-push-10{left:83.33333333%}
.col-lg-push-9{left:75%}
.col-lg-push-8{left:66.66666667%}
.col-lg-push-7{left:58.33333333%}
.col-lg-push-6{left:50%}
.col-lg-push-5{left:41.66666667%}
.col-lg-push-4{left:33.33333333%}
.col-lg-push-3{left:25%}
.col-lg-push-2{left:16.66666667%}
.col-lg-push-1{left:8.33333333%}
.col-lg-push-0{left:auto}
.col-lg-offset-12{margin-left:100%}
.col-lg-offset-11{margin-left:91.66666667%}
.col-lg-offset-10{margin-left:83.33333333%}
.col-lg-offset-9{margin-left:75%}
.col-lg-offset-8{margin-left:66.66666667%}
.col-lg-offset-7{margin-left:58.33333333%}
.col-lg-offset-6{margin-left:50%}
.col-lg-offset-5{margin-left:41.66666667%}
.col-lg-offset-4{margin-left:33.33333333%}
.col-lg-offset-3{margin-left:25%}
.col-lg-offset-2{margin-left:16.66666667%}
.col-lg-offset-1{margin-left:8.33333333%}
.col-lg-offset-0{margin-left:0}}
.btn{display:inline-block;margin-bottom:0;font-weight:normal;text-align:center;vertical-align:middle;-ms-touch-action:manipulation;touch-action:manipulation;cursor:pointer;background-image:none;border:1px solid transparent;white-space:nowrap;padding:6px 12px;font-size:14px;line-height:1.42857143;border-radius:4px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}
.btn:focus,.btn:active:focus,.btn.active:focus,.btn.focus,.btn:active.focus,.btn.active.focus{outline:5px auto -webkit-focus-ring-color;outline-offset:-2px}
.btn:hover,.btn:focus,.btn.focus{color:#333;text-decoration:none}
.btn:active,.btn.active{outline:0;background-image:none;-webkit-box-shadow:inset 0 3px 5px rgba(0,0,0,0.125);box-shadow:inset 0 3px 5px rgba(0,0,0,0.125)}
.btn.disabled,.btn[disabled],fieldset[disabled] .btn{cursor:not-allowed;opacity:.65;filter:alpha(opacity=65);-webkit-box-shadow:none;box-shadow:none}a.btn.disabled,fieldset[disabled] a.btn{pointer-events:none}
.btn-default{color:#333;background-color:#fff;border-color:#ccc}
.btn-default:focus,.btn-default.focus{color:#333;background-color:#e6e6e6;border-color:#8c8c8c}
.btn-default:hover{color:#333;background-color:#e6e6e6;border-color:#adadad}
.btn-default:active,.btn-default.active,.open>.dropdown-toggle.btn-default{color:#333;background-color:#e6e6e6;border-color:#adadad}
.btn-default:active:hover,.btn-default.active:hover,.open>.dropdown-toggle.btn-default:hover,.btn-default:active:focus,.btn-default.active:focus,.open>.dropdown-toggle.btn-default:focus,.btn-default:active.focus,.btn-default.active.focus,.open>.dropdown-toggle.btn-default.focus{color:#333;background-color:#d4d4d4;border-color:#8c8c8c}
.btn-default:active,.btn-default.active,.open>.dropdown-toggle.btn-default{background-image:none}
.btn-default.disabled:hover,.btn-default[disabled]:hover,fieldset[disabled] .btn-default:hover,.btn-default.disabled:focus,.btn-default[disabled]:focus,fieldset[disabled] .btn-default:focus,.btn-default.disabled.focus,.btn-default[disabled].focus,fieldset[disabled] .btn-default.focus{background-color:#fff;border-color:#ccc}
.btn-default .badge{color:#fff;background-color:#333}
.btn-primary{color:#fff;background-color:#337ab7;border-color:#2e6da4}
.btn-primary:focus,.btn-primary.focus{color:#fff;background-color:#286090;border-color:#122b40}
.btn-primary:hover{color:#fff;background-color:#286090;border-color:#204d74}
.btn-primary:active,.btn-primary.active,.open>.dropdown-toggle.btn-primary{color:#fff;background-color:#286090;border-color:#204d74}
.btn-primary:active:hover,.btn-primary.active:hover,.open>.dropdown-toggle.btn-primary:hover,.btn-primary:active:focus,.btn-primary.active:focus,.open>.dropdown-toggle.btn-primary:focus,.btn-primary:active.focus,.btn-primary.active.focus,.open>.dropdown-toggle.btn-primary.focus{color:#fff;background-color:#204d74;border-color:#122b40}
.btn-primary:active,.btn-primary.active,.open>.dropdown-toggle.btn-primary{background-image:none}
.btn-primary.disabled:hover,.btn-primary[disabled]:hover,fieldset[disabled] .btn-primary:hover,.btn-primary.disabled:focus,.btn-primary[disabled]:focus,fieldset[disabled] .btn-primary:focus,.btn-primary.disabled.focus,.btn-primary[disabled].focus,fieldset[disabled] .btn-primary.focus{background-color:#337ab7;border-color:#2e6da4}
.btn-primary .badge{color:#337ab7;background-color:#fff}
.btn-success{color:#fff;background-color:#5cb85c;border-color:#4cae4c}
.btn-success:focus,.btn-success.focus{color:#fff;background-color:#449d44;border-color:#255625}
.btn-success:hover{color:#fff;background-color:#449d44;border-color:#398439}
.btn-success:active,.btn-success.active,.open>.dropdown-toggle.btn-success{color:#fff;background-color:#449d44;border-color:#398439}
.btn-success:active:hover,.btn-success.active:hover,.open>.dropdown-toggle.btn-success:hover,.btn-success:active:focus,.btn-success.active:focus,.open>.dropdown-toggle.btn-success:focus,.btn-success:active.focus,.btn-success.active.focus,.open>.dropdown-toggle.btn-success.focus{color:#fff;background-color:#398439;border-color:#255625}
.btn-success:active,.btn-success.active,.open>.dropdown-toggle.btn-success{background-image:none}
.btn-success.disabled:hover,.btn-success[disabled]:hover,fieldset[disabled] .btn-success:hover,.btn-success.disabled:focus,.btn-success[disabled]:focus,fieldset[disabled] .btn-success:focus,.btn-success.disabled.focus,.btn-success[disabled].focus,fieldset[disabled] .btn-success.focus{background-color:#5cb85c;border-color:#4cae4c}
.btn-success .badge{color:#5cb85c;background-color:#fff}
.btn-info{color:#fff;background-color:#5bc0de;border-color:#46b8da}
.btn-info:focus,.btn-info.focus{color:#fff;background-color:#31b0d5;border-color:#1b6d85}
.btn-info:hover{color:#fff;background-color:#31b0d5;border-color:#269abc}
.btn-info:active,.btn-info.active,.open>.dropdown-toggle.btn-info{color:#fff;background-color:#31b0d5;border-color:#269abc}
.btn-info:active:hover,.btn-info.active:hover,.open>.dropdown-toggle.btn-info:hover,.btn-info:active:focus,.btn-info.active:focus,.open>.dropdown-toggle.btn-info:focus,.btn-info:active.focus,.btn-info.active.focus,.open>.dropdown-toggle.btn-info.focus{color:#fff;background-color:#269abc;border-color:#1b6d85}
.btn-info:active,.btn-info.active,.open>.dropdown-toggle.btn-info{background-image:none}
.btn-info.disabled:hover,.btn-info[disabled]:hover,fieldset[disabled] .btn-info:hover,.btn-info.disabled:focus,.btn-info[disabled]:focus,fieldset[disabled] .btn-info:focus,.btn-info.disabled.focus,.btn-info[disabled].focus,fieldset[disabled] .btn-info.focus{background-color:#5bc0de;border-color:#46b8da}
.btn-info .badge{color:#5bc0de;background-color:#fff}
.btn-warning{color:#fff;background-color:#f0ad4e;border-color:#eea236}
.btn-warning:focus,.btn-warning.focus{color:#fff;background-color:#ec971f;border-color:#985f0d}
.btn-warning:hover{color:#fff;background-color:#ec971f;border-color:#d58512}
.btn-warning:active,.btn-warning.active,.open>.dropdown-toggle.btn-warning{color:#fff;background-color:#ec971f;border-color:#d58512}
.btn-warning:active:hover,.btn-warning.active:hover,.open>.dropdown-toggle.btn-warning:hover,.btn-warning:active:focus,.btn-warning.active:focus,.open>.dropdown-toggle.btn-warning:focus,.btn-warning:active.focus,.btn-warning.active.focus,.open>.dropdown-toggle.btn-warning.focus{color:#fff;background-color:#d58512;border-color:#985f0d}
.btn-warning:active,.btn-warning.active,.open>.dropdown-toggle.btn-warning{background-image:none}
.btn-warning.disabled:hover,.btn-warning[disabled]:hover,fieldset[disabled] .btn-warning:hover,.btn-warning.disabled:focus,.btn-warning[disabled]:focus,fieldset[disabled] .btn-warning:focus,.btn-warning.disabled.focus,.btn-warning[disabled].focus,fieldset[disabled] .btn-warning.focus{background-color:#f0ad4e;border-color:#eea236}
.btn-warning .badge{color:#f0ad4e;background-color:#fff}
.btn-danger{color:#fff;background-color:#d9534f;border-color:#d43f3a}
.btn-danger:focus,.btn-danger.focus{color:#fff;background-color:#c9302c;border-color:#761c19}
.btn-danger:hover{color:#fff;background-color:#c9302c;border-color:#ac2925}
.btn-danger:active,.btn-danger.active,.open>.dropdown-toggle.btn-danger{color:#fff;background-color:#c9302c;border-color:#ac2925}
.btn-danger:active:hover,.btn-danger.active:hover,.open>.dropdown-toggle.btn-danger:hover,.btn-danger:active:focus,.btn-danger.active:focus,.open>.dropdown-toggle.btn-danger:focus,.btn-danger:active.focus,.btn-danger.active.focus,.open>.dropdown-toggle.btn-danger.focus{color:#fff;background-color:#ac2925;border-color:#761c19}
.btn-danger:active,.btn-danger.active,.open>.dropdown-toggle.btn-danger{background-image:none}
.btn-danger.disabled:hover,.btn-danger[disabled]:hover,fieldset[disabled] .btn-danger:hover,.btn-danger.disabled:focus,.btn-danger[disabled]:focus,fieldset[disabled] .btn-danger:focus,.btn-danger.disabled.focus,.btn-danger[disabled].focus,fieldset[disabled] .btn-danger.focus{background-color:#d9534f;border-color:#d43f3a}
.btn-danger .badge{color:#d9534f;background-color:#fff}
.btn-link{color:#337ab7;font-weight:normal;border-radius:0}
.btn-link,.btn-link:active,.btn-link.active,.btn-link[disabled],fieldset[disabled] .btn-link{background-color:transparent;-webkit-box-shadow:none;box-shadow:none}
.btn-link,.btn-link:hover,.btn-link:focus,.btn-link:active{border-color:transparent}
.btn-link:hover,.btn-link:focus{color:#23527c;text-decoration:underline;background-color:transparent}
.btn-link[disabled]:hover,fieldset[disabled] .btn-link:hover,.btn-link[disabled]:focus,fieldset[disabled] .btn-link:focus{color:#777;text-decoration:none}
.btn-lg{padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px}
.btn-sm{padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}
.btn-xs{padding:1px 5px;font-size:12px;line-height:1.5;border-radius:3px}
.btn-block{display:block;width:100%}
.btn-block+.btn-block{margin-top:5px}input[type="submit"].btn-block,input[type="reset"].btn-block,input[type="button"].btn-block{width:100%}
.thumbnail{display:block;padding:4px;margin-bottom:20px;line-height:1.42857143;background-color:#fff;border:1px solid #ddd;border-radius:4px;-webkit-transition:border .2s ease-in-out;-o-transition:border .2s ease-in-out;transition:border .2s ease-in-out}
.thumbnail>img,.thumbnail a>img{margin-left:auto;margin-right:auto}a.thumbnail:hover,a.thumbnail:focus,a.thumbnail.active{border-color:#337ab7}
.thumbnail .caption{padding:9px;color:#333}
.clearfix:before,.clearfix:after,.dl-horizontal dd:before,.dl-horizontal dd:after,.container:before,.container:after,.container-fluid:before,.container-fluid:after,.row:before,.row:after{content:" ";display:table}
.clearfix:after,.dl-horizontal dd:after,.container:after,.container-fluid:after,.row:after{clear:both}
.center-block{display:block;margin-left:auto;margin-right:auto}
.pull-right{float:right }
.pull-left{float:left }
.hide{display:none }
.show{display:block }
.invisible{visibility:hidden}
.text-hide{font:0/0 a;color:transparent;text-shadow:none;background-color:transparent;border:0}
.hidden{display:none }
.affix{position:fixed}
.bg-default{background: #fff;}
</style>
</head>
<body class="col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 bg-info">
  <div class="row bg-default">
    <div class="col-md-4 col-sm-4">
      <h1 class="text-center"><img src="<?=base_url('upload/system/logo.png');?>" class="img-rounded" alt="Logo SEF" height="80px"></h1>
    </div>
    <div class="col-md-8 col-sm-8 text-center ">
      <h3 class="text-primary">MEMBERSHIP SYSTEM</h3>
      <p><b>Membership of Regular Class Student English Forum Universitas Jenderal Soedirman</b></p>
    </div>
  </div>
  <div class="row bg-default">
    <div class="col-md-12 col-sm-12">
    <hr class="clearfix"/>
      <p>Hello {honor}{name},</p>
      <p>You have registering this email to our system. You cannot login to the system until the confirmation of email success. Please confirm your registration by clicking button below:</p>
      <h2 class="text-center"><a href="{url_confirm_email}" class="btn btn-primary">Confirm Registration</a></h2>
      <p>Thank you for your attention.</p>
      <p><i>PS: if this was not you, please click this  <a href="{url_revoke_email}" class="text-danger bg-info">link</a> to delete your email from our system.</i></p>
      <hr class="clearfix">
    </div>
  </div>
  <div class="row bg-danger">
    <div class="col-md-12 col-sm-12"><small><i>This is auto generated email, please do not reply this email.</i></small>
    </div>
  </div>
  <div class="row bg-default">
    <hr class="clearfix">
  </div>
  <div class="row bg-success">
    <div class="col-md-6 col-sm-6">
      <h3>Contact Us:</h3>
      <p><img src="<?=base_url('upload/system/phone.png');?>" class="img-thumbnail" width="35" alt="phone"> <b class="text-primary">089128378127</b></p>
      <p><img src="<?=base_url('upload/system/whatsapp.png');?>" class="img-thumbnail" width="35" alt="whatsapp"> <b class="text-primary">09712361265</b></p>
    </div>
    <div class="col-md-6 col-sm-6">
      <h3>Our Social Media:</h3>
      <p><img src="<?=base_url('upload/system/twitter.png');?>" class="img-thumbnail" width="35" alt="twitter"> <b class="text-primary">@sefunsoed</b></p>
      <p><img src="<?=base_url('upload/system/facebook.png');?>" class="img-thumbnail" width="35" alt="facebook"> <b class="text-primary">Student English Forum</b></p>
      <p><img src="<?=base_url('upload/system/instagram.png');?>" class="img-thumbnail" width="35" alt="instagram"> <b class="text-primary">Student English Forum</b></p>
    </div>
  </div>
  <div class="row bg-default">
    <div class="col-md-12 col-sm-12 text-center bg-primary">
      <a href="#"><h5 class="bg-primary"> Visit our site </h5></a> 
      Copyright 2018
      <hr class="clearfix" />
    </div>
  </div>
</body>
</html>
