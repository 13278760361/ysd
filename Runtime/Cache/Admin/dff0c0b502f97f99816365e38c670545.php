<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|云师大考勤管理后台</title>
    <link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/static/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/static/layer/system.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <!-- <span class="logo"></span> -->
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav" >
            <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
                <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
                <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
                <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                    <a class="item" href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
    <div class="main-title">
        <h2>添加课程</h2>
    </div>
    <form action="<?php echo U('ClassHour/adds');?>" method="post" class="form-horizontal">
    <input type="hidden" name='subject_id' value="<?php echo ($_GET['subject_id']); ?>"/>
        <div class="form-item">
           <label class="item-label">课程名:<?php echo ($subject_name); ?></label>
        </div> 

        <div class="form-item e">
            <label class="item-label">授课老师</label>
            <select name="teacher_id">
            <option value="">请选择</option>
            <?php if(is_array($teachers)): $i = 0; $__LIST__ = $teachers;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['teacher_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>    
            </select> 
        </div> 
        <div class="form-item">
            <label class="item-label">上课教室</label>
            <select name="class_room_id">
            <option value="">请选择</option>
            <?php if(is_array($class_room)): $i = 0; $__LIST__ = $class_room;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['class_room_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>    
            </select> 
        </div>

       <div class="form-item">
            <label class="item-label">开始时间</label>
        <input type="text" name="start_time" class="text time" placeholder="请选择时间" />
       </div>
       <div class="form-item">
            <label class="item-label">结束时间</label>
        <input type="text" name="end_time" class="text time" placeholder="请选择时间" />
       </div>

    <!-- <div class="form-item">
            <label class="item-label">排序</label>
        <input type="text" name="sort" class="text input-small" value="<?php echo ($data['sort']); ?>" />
       </div>-->
       <label class="item-label"><a href="javascript:;" nctype="add_sv">点击添加课时</a><span style="color:red; font-size:11px">(没有课可以不用填)</span></label>
       <!--按天数添加课时
       <div class="r-am">
            <div class = "row">
                <div class="form-item col-md-6">
                    <label class="item-label">上午开始时间</label>
                    <input type="text" name="time_s[am][1][start]" class="text pma" placeholder="请选择开始时间" />
                </div>
                <div class="form-item col-md-6">
                    <label class="item-label">上午结束时间</label>
                    <input type="text" name="time_s[am][1][end]" class="text pma" placeholder="请选择结束时间" />
                </div>
            </div>
            <div class="row">
                <div class="form-item col-md-6">
                    <label class="item-label">下午开始时间</label>
                    <input type="text" name="time_s[pm][1][start]" class="text pma" placeholder="请选择开始时间" />
                </div>
                <div class="form-item col-md-6">
                    <label class="item-label">下午结束时间</label>
                    <input type="text" name="time_s[pm][1][end]" class="text pma" placeholder="请选择结束时间" />
                </div>
            </div>
        </div>-->
        <!--按周添加课时-->
        <div class="r-am">
            <div class = "row">
                <div class="form-item col-md-6">
                    <label class="item-label">上午开始时间</label>
                    <input type="text" name="time_s[am][1][start]" class="text pma" placeholder="请选择周一上午上课时间" />
                    <input type="text" name="time_s[am][1][up_time]" class="text" placeholder="请填写上课提前时间默认30分钟" />
                </div>
                <div class="form-item col-md-6">
                    <label class="item-label">上午结束时间</label>
                    <input type="text" name="time_s[am][1][end]" class="text pma" placeholder="请选择周一上午下课课时间" />
                    <input type="text" name="time_s[am][1][down_time]" class="text" placeholder="请填写下课延长时间默认30分钟" />
                </div>
            </div>
            <div class="row">
                <div class="form-item col-md-6">
                    <label class="item-label">下午开始时间</label>
                    <input type="text" name="time_s[pm][1][start]" class="text pma" placeholder="请选择周一下午上课时间" />
                    <input type="text" name="time_s[pm][1][up_time]" class="text" placeholder="请填写上课提前时间如30" />
                </div>
                <div class="form-item col-md-6">
                    <label class="item-label">下午结束时间</label>
                    <input type="text" name="time_s[pm][1][end]" class="text pma" placeholder="请选择周一下午下课课时间" />
                    <input type="text" name="time_s[pm][1][down_time]" class="text" placeholder="请填写下课延长时间默认30分钟" />
                </div>
            </div>
        </div>
        <div class = "row">
            <div class="form-item">
                <input type="text" hidden="hidden" name="datas"/>
                <textarea hidden="hidden" name="datas"></textarea>
                <button class="btn submit-btn ajax-post" id="submit" type="button" target-form="form-horizontal">确 定</button>
                
                <button class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
            </div>
        </div>
    </form>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用青才管理平台</div>
                <div class="fr">V<?php echo (ONETHINK_VERSION); ?></div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "", //当前网站地址
            "APP"    : "/admin.php?s=", //当前项目地址
            "PUBLIC" : "/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/Public/static/think.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
<link href="/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
<?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
<link href="/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">
        //导航高亮
        
        highlight_subnav('<?php echo U('Subjects/index');?>');
        //获取相同html内容
        var html = $('.e').html();
        
$(function(){
    $('.date').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
    $('.time').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        startView:2,
        autoclose:true,
       // daysOfWeekDisabled:[0,6]
    });
   newTime();
}) ;
// 添加课时 
    var i = 2;  // 自增，防止冲突
    var j = 0; //添加课时标识
    var sweet = '';
    $('a[nctype="add_sv"]').click(function(){
        //alert($('input[name="start_time"]').val());
       // alert(new Date($('input[name="start_time"]').val()).getDay());
       switch(i){
        case 2:
            sweet = '二';
        break;
        case 3:
            sweet = '三';
        break;
        case 4:
            sweet = '四';
        break;
        case 5:
            sweet = '五';
        break;
        case 6:
            sweet = '六';
        break;
        case 7:
            sweet = '日';
        break;
       }
        var strtime = $('input[name="start_time"]').val();
        var endtime = $('input[name="end_time"]').val();
        var st = replcTime(strtime);
        var et = replcTime(endtime);
        var ss = (et-st)/3600000/24;
        var _tr = $('.r-am');

          var r = true;
        if(i>5){
        
            if (confirm("周末是否有课")){
              r = true;
            }else{
              r = false;
            }
        }
        if(ss >= 1 && i<=7 && r){
        $('<div class="new"><div class = "row">                <div class="form-item col-md-6">                    <label class="item-label">上午开始时间</label>                    <input type="text" name="time_s[am]['+i+'][start]" class="text pma" placeholder="请选择周'+sweet+'上午上课时间" /> <input type="text" name="time_s[am]['+i+'][up_time]" class="text" placeholder="请填写上课提前时间默认30分钟" />               </div>                <div class="form-item col-md-6">                    <label class="item-label">上午结束时间</label>                    <input type="text" name="time_s[am]['+i+'][end]" class="text pma" placeholder="请选择周'+sweet+'上午下课时间" /> <input type="text" name="time_s[am]['+i+'][down_time]" class="text" placeholder="请填写下课延长时间默认30分钟" />               </div>            </div>            <div class="row">                <div class="form-item col-md-6">                    <label class="item-label">下午开始时间</label>                    <input type="text" name="time_s[pm]['+i+'][start]" class="text pma" placeholder="请选择周'+sweet+'下午上课时间" /> <input type="text" name="time_s[pm]['+i+'][up_time]" class="text" placeholder="请填写上课提前时间默认30分钟" />               </div>                <div class="form-item col-md-6">                    <label class="item-label">下午结束时间</label>                    <input type="text" name="time_s[pm]['+i+'][end]" class="text pma" placeholder="请选择周'+sweet+'下午下课时间" /> <input type="text" name="time_s[pm]['+i+'][down_time]" class="text" placeholder="请填写下课延长时间默认30分钟" />               </div>            </div><span class="nscs"><a href="javascript:void(0);" class="btn-orange"><i class="icon-minus-sign"></i><p>移除</p></a></span></div>').appendTo(_tr);   //上午
        $('.nscs').find('a').click(function(){
            $(this).parents('div:first').remove();
            i=2;  //初始化
            j--;
            if(j==-1){
                j=0;
            }
        });
       // alert(j);
         i++;
         j++;
     }else{
        alert('添加课时失败，请检查你的开始时间与结束时间！');
        return false;

     }
        newTime('+'+j);
    });
    function replcTime(val){
        var date = new Date(val); //传入一个时间格式，如果不传入就是获取现在的时间了，这样做不兼容火狐。
        // 可以这样做
        var date = new Date(val.replace('/-/g', '/'));
        return Date.parse(date);
    }
    function newTime(ret){
         //重新初始化datetimepicker
        $('.pma').datetimepicker({
            startDate:ret+'d',
            format: 'hh:ii',
            language:"zh-CN",
            minView:0,
            startView:1,
            autoclose:true,
            showMeridian:1,
            //todayBtn:true
          //  daysOfWeekDisabled:[0,6]
        });
    }
    $("#submit").click(function(){
        var len = $('.col-md-6');
       // var ipt = ;
        var data = "";
        for(var i=1;i<=len.length/4;i++){
            data+="{am_start:'"+$('.col-md-6').find("input[name='time_s[am]["+i+"][start]']").val()+"',am_end:'"+$('.col-md-6').find("input[name='time_s[am]["+i+"][end]']").val()+"',pm_start:'"+$('.col-md-6').find("input[name='time_s[pm]["+i+"][start]']").val()+"',pm_end:'"+$('.col-md-6').find("input[name='time_s[pm]["+i+"][end]']").val()+"',up_a_time:'"+$('.col-md-6').find("input[name='time_s[am]["+i+"][up_time]']").val()+"',down_a_time:'"+$('.col-md-6').find("input[name='time_s[am]["+i+"][down_time]']").val()+"',up_p_time:'"+$('.col-md-6').find("input[name='time_s[pm]["+i+"][up_time]']").val()+"',down_p_time:'"+$('.col-md-6').find("input[name='time_s[pm]["+i+"][down_time]']").val()+"'},";
        }
        
       data = data.substring(0,data.length-1);
       datas="["+data+"]";
       $("textarea[name='datas']").text(datas);
       $(this).attr('type','submit');
    });
    </script>

</body>
</html>