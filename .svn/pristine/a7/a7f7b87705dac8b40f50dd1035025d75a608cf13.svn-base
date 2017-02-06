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
class PublicController extends BaseController {

	 function _initialize(){
        parent::_initialize();
	 	     $this->db = M('Students');
         $this->db_subjects =M('Subjects');
         $this->db_teachers = M('Teachers');
        // $this->options = array('appid'=>C('appid'),'appsecret'=>C('appsecret'),'token'=>C('token'),'encodingaeskey'=>C('encodingaeskey'));
        // $this->wechatObj = new TPWechat($this->options);
   
    }
	//系统首页
    public function login(){
         if (IS_POST) {

              $_data = I('post.');
              $data['username'] = $_data['username'];
              $data['phone'] = $_data['phone'];
              $data['student_no'] = $_data['student_no'];
              $data['openid']  = $this->wx_info['openid'];
              $data['wx_nickname'] =  deal_emoji($this->wx_info['nickname'],0);
              $data['sex'] = $this->wx_info['sex'];
              $data['province'] = $this->wx_info['province'];
              $data['city'] = $this->wx_info['city'];
              $data['country'] = $this->wx_info['country'];
              $data['headimgurl'] = $this->wx_info['headimgurl'];
              $data['time'] = time();//注册时间
              $data['class_id'] = $_data['class_id'];

              if ( empty($data['username']) ) {
                   $this->error('用户名不能为空');
              }

              if ( empty($data['phone']) ) {
                   $this->error('电话号码不能为空');
              }else{
                   $isMob="/^1[3-5,7,8]{1}[0-9]{9}$/";
                   if(!preg_match($isMob,$data['phone'])){
                      $this->error('电话号码格式不正确，请重新输入'); 
                    }else{
                       if ( $this->db->where(array('phone'=>$data['phone']))->find() ) {
                          $this->error('电话号码已经存在！');
                       }
                    }
              }

             

              if ( empty($data['student_no']) ) {
                $this->error('学号不能为空');
              }else{
                if ( $this->db->where(array('student_no'=>$data['student_no']))->find() ) {
                   $this->error('学号已经存在');
                }
              }

              if ( empty($data['class_id']) ) {
                 $this->error('请选择班级');
              }

              $res = $this->db->add($data);

              if ($res) {
                  $this->success('注册成功',U('Center/index'));
              }else{
                  $this->error('注册失败');
              }

         }else{
            // dump($this->wx_info);

             if ( $this->db->where( array('openid'=>$this->wx_info['openid']) )->find() ) {
                  redirect( U('Center/index') );
              }
            $data = M('Class_years')->select();
            $arr = array();
            foreach ($data as $key => $val) {         
                 
                 $arr[$val['year_name']]=   M('Class')->where("class_year_id = {$val['id']}")->getField('id,class_name');
            }

            
            $this->assign("classData",urlencode(json_encode($arr)));
            $this->display();
         }
          
    }

    public function signSuccess(){
        $subject_id = I('get.subject_id');
        $teacher_id = I('get.teacher_id');

        $subject_name = $this->db_subjects->where(array('id'=>$subject_id))->getField('subject_name');
        $teacher_name = $this->db_teachers->where(array('id'=>$teacher_id))->getField('teacher_name');

        $class_name = I('get.classR');

        //提醒时间 
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');

        $start = date('H:i',$start_time);
        $end = date('H:i',strtotime('+30 minute',$end_time));

        $prompt = "请于今日".$start."--".$end."完成打卡，否则不予记录该课时学时!";

        

        $this->assign('subject_name',$subject_name);
        $this->assign('teacher_name',$teacher_name);
        $this->assign('class_name',$class_name);
        $this->assign('prompt',$prompt); 
        $this->display();

    }


     public function signBackSuccess(){
        $subject_id = I('get.subject_id');
        $teacher_id = I('get.teacher_id');

        $subject_name = $this->db_subjects->where(array('id'=>$subject_id))->getField('subject_name');
        $teacher_name = $this->db_teachers->where(array('id'=>$teacher_id))->getField('teacher_name');

        $class_name = I('get.classR');

        $this->assign('subject_name',$subject_name);
        $this->assign('teacher_name',$teacher_name);
        $this->assign('class_name',$class_name);

        $this->display();

    }

    //  //获取用户信息
    // protected function get_info()
    // {
    // 	$access=cookie('access_token');
    // 	$refresh=cookie('refresh_token');
    // 	$openid=cookie('myopenid');
    // 	if($access)
    // 	{
    // 		return $this->wechatObj->getOauthUserinfo($access,$openid);
    // 	}else if($refresh)
    // 	{
    // 		$token=$this->wechatObj->getOauthRefreshToken($refresh);
    // 		if($token)
    // 		{
    // 			cookie('access_token',$token['access_token'],C('ACCESS_TOKEN_TIME'));
    //     		cookie('refresh_token',$token['access_token'],C('REFRESH_TOKEN_TIME'));
    //     		cookie('myopenid',$token['openid'],C('REFRESH_TOKEN_TIME'));
    //     		return $this->wechatObj->getOauthUserinfo($token['access_token'],$token['openid']);
    // 		}else
    // 		{
    // 			cookie('access_token',null);
    //     		cookie('refresh_token',null);
    //         	$this->get_oauth(1);
    // 		}
    // 	}else
    // 	{
    // 		$code=isset($_GET['code'])?$_GET['code']:'';
    // 		if($code)
    // 		{
    // 			$token=$this->wechatObj->getOauthAccessToken();
    //             $info=$this->wechatObj->getOauthUserinfo($token['access_token'],$token['openid']);
    //             if(!$info){$this->error('授权失败！！请重新授权');}
    //             cookie('access_token',$token['access_token'],C('ACCESS_TOKEN_TIME'));
    //             cookie('refresh_token',$token['access_token'],C('REFRESH_TOKEN_TIME'));
    //             cookie('myopenid',$token['openid'],C('REFRESH_TOKEN_TIME'));
    //             return $info;
    // 		}else
    // 		{
    // 			$this->get_oauth(1);
    // 		}
    // 	}
    // }

    // //获取授权--0:基本信息授权,1:所有信息授权
    // protected function get_oauth($all=0)
    // {
    // 	$url=$this->get_url();
    // 	$type=$all==1?'snsapi_userinfo':'snsapi_base';
    // 	$wurl=$this->wechatObj->getOauthRedirect($url,$type,$type);
    //     redirect($wurl);
    // }

    //   //获取当前地址
    // public function get_url()
    // {
    // 	//协议
    //     $protocol=isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443'?'https://':'http://';
    //     //入口
    //     $main=$_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME'];
    //     //参数
    //     $parm=isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';
    //     //地址
    //     $url=isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:$main.(isset($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:$parm);
    //     //返回地址
    //     return $protocol.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'').$url;
    // }

}