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
use Wechat\TPWechat;
use Think\Controller;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class BaseController extends Controller {
          
      public $wx_info=array();

	  public function _initialize(){
	
        $this->options = array('appid'=>C('appid'),'appsecret'=>C('appsecret'),'token'=>C('token'),'encodingaeskey'=>C('encodingaeskey'));
        $this->wechatObj = new TPWechat($this->options);
        $this->wx_info = $this->get_info();

        $this->user_id = M('Students')->where(array('openid'=>cookie('myopenid')))->getField('id');
  
   
    }
	

     //获取用户信息
    protected function get_info()
    {
    	$access=cookie('access_token');
    	$refresh=cookie('refresh_token');
    	$openid=cookie('myopenid');
         
    	if($access)
    	{  
           
    		return $this->wechatObj->getOauthUserinfo($access,$openid);

    	}else if($refresh)
    	{
           
    		$token=$this->wechatObj->getOauthRefreshToken($refresh);
    		if($token)
    		{
    			cookie('access_token',$token['access_token'],C('ACCESS_TOKEN_TIME'));
        		cookie('refresh_token',$token['access_token'],C('REFRESH_TOKEN_TIME'));
        		cookie('myopenid',$token['openid'],C('REFRESH_TOKEN_TIME'));
        		return $this->wechatObj->getOauthUserinfo($token['access_token'],$token['openid']);
    		}else
    		{
    			cookie('access_token',null);
        		cookie('refresh_token',null);
            	$this->get_oauth(1);
    		}
    	}else
    	{
          
    		$code=isset($_GET['code'])?$_GET['code']:'';
    		if($code)
    		{
    			$token=$this->wechatObj->getOauthAccessToken();
                $info=$this->wechatObj->getOauthUserinfo($token['access_token'],$token['openid']);
                if(!$info){$this->error('授权失败！！请重新授权');}
                cookie('access_token',$token['access_token'],C('ACCESS_TOKEN_TIME'));
                cookie('refresh_token',$token['access_token'],C('REFRESH_TOKEN_TIME'));
                cookie('myopenid',$token['openid'],C('REFRESH_TOKEN_TIME'));
                return $info;
    		}else
    		{
    			$this->get_oauth(1);
    		}
    	}
    }

    //获取授权--0:基本信息授权,1:所有信息授权
    protected function get_oauth($all=0)
    {
    	$url=$this->get_url();
    	$type=$all==1?'snsapi_userinfo':'snsapi_base';
    	$wurl=$this->wechatObj->getOauthRedirect($url,$type,$type);
        redirect($wurl);
    }

      //获取当前地址
    public function get_url()
    {
    	//协议
        $protocol=isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443'?'https://':'http://';
        //入口
        $main=$_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME'];
        //参数
        $parm=isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';
        //地址
        $url=isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:$main.(isset($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:$parm);
        //返回地址
        return $protocol.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'').$url;
    }

}