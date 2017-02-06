<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
// use OT\DataDictionary;
use Think\Controller;
use Wechat\TPWechat;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class CreateBController extends BaseController {

	  public function _initialize(){
  
        $this->options = array('appid'=>C('appid'),'appsecret'=>C('appsecret'),'token'=>C('token'),'encodingaeskey'=>C('encodingaeskey'));
        $this->wechatObj = new TPWechat($this->options);
    
  
   
    }
	//微信菜单生成
    public function index(){



   $data = array (
            'button' => array (
              0 => array (
                'name' => '课程签到',
                'sub_button' => array (
                    0 => array (
                      'type' => 'view',
                      'name' => '签到信息',
                      'url' => 'http://ysdmba.easyitcn.cn/index.php?s=/Home/Sign/index.html',
                    ),
                    1 => array (
                      'type' => 'view',
                      'name' => '课程信息',
                      'url' => 'http://ysdmba.easyitcn.cn/index.php?s=/Home/Choice/index.html',
                    ),
                ),
              ),
              1 => array (
                'type' => 'view',
                'name' => 'MBA',
                'url'  => 'http://ysdmba.easyitcn.cn/index.php?s=/Home/Img/index.html',             
              ),
              2 => array (
                'type' => 'view',
                'name' => '个人中心',
                'url' => 'http://ysdmba.easyitcn.cn/index.php?s=/Home/Public/login.html'
              ),
            ),
        );


  $this->wechatObj->createMenu($data);
  
      
    }
          
         
  

   




}