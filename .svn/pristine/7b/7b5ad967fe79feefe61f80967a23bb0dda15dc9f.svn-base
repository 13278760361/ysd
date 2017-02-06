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
class IndexController extends BaseController {

	 function _initialize(){
        parent::_initialize();
	 	    $this->db = M('Students');
        $this->db_sign = M('Sign_history');
        $this->db_class_hour = M('Class_hour');
        $this->db_choice_class_hour = M('Choice_class_hour');
        $this->db_choice_subjects = M('Choice_subjects');
        // $this->options = array('appid'=>C('appid'),'appsecret'=>C('appsecret'),'token'=>C('token'),'encodingaeskey'=>C('encodingaeskey'));
        // $this->wechatObj = new TPWechat($this->options);
   
    }
	//系统首页
    public function index(){


       
         $classR = I('get.classR'); //获取教室信息

         $ticket = I('get.ticket');//获取 ticket 信息

      

         $info =  $this->wechatObj->getShakeInfoShakeAroundUser($ticket); //获取摇一摇周边信息
         if ( empty($info) ) {
              $this->error('请重新摇一摇 -0-');
         }
         
         // $openid ='oseUAsxPfDpHXhHIN6XOSkB_lOWs';
         
         $openid = $info['data']['openid'];

         $now_time = time(); //当前时间

         $this->db->startTrans();//开启事物

         if (  $this->db->where(array('openid'=>$openid))->find()  ) { //如果有数据 执行打卡操作
              //查询改学生上课课程
          

              // FROM_UNIXTIME(1234567890, '%Y-%m-%d %H:%i:%s') //时间戳格式化

              // DATE_ADD(OrderDate,INTERVAL 2 DAY) //时间日期格式 添加时间

              // DATE_SUB(OrderDate,INTERVAL 2 DAY) //时间日期格式 减时间

              // UNIX_TIMESTAMP('2015-04-29') //日期格式转时间戳

                // UNIX_TIMESTAMP(DATE_SUB(FROM_UNIXTIME(c,start_time, '%Y-%m-%d %H:%i:%s'),INTERVAL 10 MINUTE) )
                // UNIX_TIMESTAMP(DATE_SUB(FROM_UNIXTIME(c,start_time, '%Y-%m-%d %H:%i:%s'),INTERVAL 10 MINUTE) )
                // UNIX_TIMESTAMP(DATE_SUB(FROM_UNIXTIME(c,start_time, '%Y-%m-%d %H:%i:%s') as time ,INTERVAL 10 MINUTE) as ttime) as tttime
              $field = "c.id as class_hour_id,c.subject_id,c.teacher_id,st.id as user_id,cr.class_room_name,
                c.start_time, 
                c.end_time ";
              $where ="st.openid ='{$openid}' and cr.class_room_name ='{$classR}' and  
               ROUND( UNIX_TIMESTAMP(DATE_SUB(FROM_UNIXTIME(start_time, '%Y-%m-%d %H:%i:%s'),INTERVAL 30 MINUTE)))  <={$now_time} and  
               ROUND( UNIX_TIMESTAMP(DATE_ADD(FROM_UNIXTIME(end_time, '%Y-%m-%d %H:%i:%s'),INTERVAL 30 MINUTE)))  >= {$now_time} and st.id = cc.user_id and cc.class_hour_id = c.id and c.class_room_id = cr.id";
            
              $dk = $this->db->table('dk_students st,dk_choice_class_hour cc,dk_class_hour c,dk_class_room cr')->field($field)->where($where)->find();
             
              
           
              

              if ($dk) { //如果该学生有课程
                    //打卡操作
                    if ( $first_sign =   $this->db_sign->where(array('user_id'=>$dk['user_id'],'class_hour_id'=>$dk['class_hour_id'],'sign_type'=>1))->find() ) { //查看是否有过打卡记录
                        $data['user_id'] = $dk['user_id'];
                        $data['class_hour_id'] = $dk['class_hour_id'];
                        $data['sign_address'] = $dk['class_room_name'];
                        $data['sign_time'] = $now_time;
                        $data['sign_type'] = 2;   //下课打卡
                         
                     

                        $res = $this->db_sign->add($data);

                        if ($now_time >= $dk['end_time']) { //如果下课打卡时间大于结束时间
                           $now_time = $dk['end_time'];
                        }
                        
                        $sum_subject_time = round(($now_time-$first_sign['sign_time'])%86400/3600,1); //该科目所学学时计算
                    
                        if ( $now_time <= $first_sign['sign_time'] ) {//上课之前就打了下课卡
                            $res_2 = true; 
                         }else{
                            $res_2 = $this->db_choice_class_hour->where( array('user_id'=>$dk['user_id'],'class_hour_id'=>$dk['class_hour_id']) )->setField('sum_subject_time',$sum_subject_time);//更新学时   
                         }
                         
                           
                      
                                                    

                        if ($res && $res_2!==false ) {
                            $this->db->commit();
                            $url = U('Public/signBackSuccess',array('subject_id'=>$dk['subject_id'],'teacher_id'=>$dk['teacher_id'],'classR'=>$classR ));
                            redirect($url);
                        }else{
                            $this->db->rollback();
                            $this->error('打卡失败');
                        }
                       
                    }else{
                         $data['user_id'] = $dk['user_id'];
                        $data['class_hour_id'] = $dk['class_hour_id'];
                        $data['sign_address'] = $dk['class_room_name'];

                        if ($now_time <= $dk['start_time']) { //如果打卡在上课开始之前 打卡时间==上课开始时间
                           $data['sign_time'] = $dk['start_time'];
                        }else{
                           $data['sign_time'] = $now_time;
                        }
                       
                        $data['sign_type'] = 1;   //上课打卡 
                        $res = $this->db_sign->add($data);

                        // echo $this->db_sign->getLastSql();exit();
                        if ($res) {
                              $this->db->commit();
                              $url = U('Public/signSuccess',array('subject_id'=>$dk['subject_id'],'teacher_id'=>$dk['teacher_id'],'classR'=>$classR ));
                            redirect($url);
                        }else{
                            $this->db->rollback();
                            $this->error('打卡失败');
                        }     
                    }
                   
              }else{ //很奇葩的需求 没选课还能打卡 

                $field = "c.id as class_hour_id ,cr.class_room_name,c.subject_id,c.teacher_id,
                  c.start_time, 
                  c.end_time ";
                $where ="cr.class_room_name ='{$classR}' and  
                 ROUND( UNIX_TIMESTAMP(DATE_SUB(FROM_UNIXTIME(start_time, '%Y-%m-%d %H:%i:%s'),INTERVAL 30 MINUTE)))  <={$now_time} and  
                   c.end_time  >= {$now_time}  and c.class_room_id = cr.id";
              
                $find_dk = $this->db_class_hour->table('dk_class_hour c,dk_class_room cr')->field($field)->where($where)->find();
                  
                  if ($find_dk) { //如果找到该课程 添加选课 打卡
                       //添加选课时
                        $data['user_id'] =$this->user_id;
                        $data['class_hour_id'] = $find_dk['class_hour_id'];

                        $res = $this->db_choice_class_hour->add($data); 
                        //添加打卡记录 
                          $dataS['user_id'] = $this->user_id;
                          $dataS['class_hour_id'] = $find_dk['class_hour_id'];
                          $dataS['sign_address'] = $find_dk['class_room_name'];

                          if ($now_time <= $find_dk['start_time']) { //如果打卡在上课开始之前 打卡时间==上课开始时间
                             $dataS['sign_time'] = $find_dk['start_time'];
                          }else{
                             $dataS['sign_time'] = $now_time;
                          }
                         
                          $dataS['sign_type'] = 1;   //上课打卡 
                          $res_2 = $this->db_sign->add($dataS);


                         //添加选课程
                          $dataC['user_id'] = $this->user_id;
                          $dataC['subject_id'] = $this->db_class_hour->where( array('id'=>$find_dk['class_hour_id']) )->getField('subject_id');//找到课程ID

                          if ( $this->db_choice_subjects->where( array('user_id'=>$this->user_id,'subject_id'=>$dataC['subject_id']) )->find() ) { //如果选课已经添加
                             $res_3 = true;
                          }else{
                             $res_3 = $this->db_choice_subjects->add($dataC);
                          }
                         
                         if ($res && $res_2 && $res_3) {
                            $this->db->commit();
                             $url = U('Public/signSuccess',array('subject_id'=>$find_dk['subject_id'],'teacher_id'=>$find_dk['teacher_id'],'classR'=>$classR,'start_time'=>$find_dk['start_time'],'end_time'=>$find_dk['end_time'] ));
                            redirect($url);
                         }else{
                            $this->db->rollback();
                            $this->error('打卡失败');
                         }

                      
                  }else{ //没有课程
                     
                      redirect(U('Sign/index'));
                  }


                  // $this->error('上课时间未到，请查看课程时间',U('Sign/index'));
              }



              

         }else{ //没有数据 认证 
            
              
              redirect(U('Public/login'));
	

         }




                 
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