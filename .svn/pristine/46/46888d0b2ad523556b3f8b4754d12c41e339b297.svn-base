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
class SignController extends BaseController {

	 function _initialize(){
         parent::_initialize();
	       $this->db = M('Students');
         $this->db_class_hour =M('Class_hour');
         $this->db_choice_class_hour = M('Choice_class_hour');
         $this->db_subjects = M('Subjects');
         $this->db_choice_subjects = M('Choice_subjects');
        // $this->options = array('appid'=>C('appid'),'appsecret'=>C('appsecret'),'token'=>C('token'),'encodingaeskey'=>C('encodingaeskey'));
        // $this->wechatObj = new TPWechat($this->options);

        //测试开启
        // $this->user_id = 92;
    }
	//签到历史
    public function index(){


            if ( empty($this->user_id) ) {  //检测是否绑定
               redirect( U('Public/login') );
            }

          // $user_info = $this->db->where( array('id'=>$this->user_id) )->find();//用户信息

    
          $user_info =  $this->db->table('dk_students s ,dk_class c,dk_class_years cy')->field("s.headimgurl,s.username,s.student_no,s.phone,s.sex,cy.year_name,c.class_name")->where("s.class_id = c.id and c.class_year_id = cy.id and s.id ={$this->user_id}")->find();


          $field =" sum(cc.sum_subject_time) as subject_time_sum, s.id as subject_id, s.subject_name,s.subject_score,s.subject_time,t.teacher_name";
          $where ="cc.user_id ={$this->user_id} and cc.class_hour_id =c.id and  c.subject_id = s.id and c.teacher_id = t.id ";



          $class_hour_info  =  $this->db_choice_class_hour->table('dk_class_hour c, dk_choice_class_hour cc,dk_subjects s ,dk_teachers t')->field($field)->where($where)->order('c.start_time asc')->group('c.subject_id')->order('subject_id asc')->select(); //选择课时信息

          // dump($class_hour_info);exit();

         
           //所选科目总学分
       

           $subject_score_sum  = $this->db_subjects->where("id in( select c.subject_id from dk_class_hour as c ,dk_choice_class_hour as cc where cc.user_id ={$this->user_id} and cc.class_hour_id =c.id  )")->sum('subject_score');

           $subject_score_sum = !empty($subject_score_sum)?$subject_score_sum:0; //没分就计算为0分

           //计算该学生总共学分(通过了才会给学分)
           $user_score = $this->db_choice_subjects->where( array('user_id'=>$this->user_id,'status'=>1) )->sum('score'); 
           
           $user_score = !empty($user_score)?$user_score:0; //没分就计算为0分



     

     
           $this->assign('user_info',$user_info);
           $this->assign('class_hour_info',$class_hour_info );
           $this->assign('subject_score_sum',$subject_score_sum);
           $this->assign('user_score',$user_score);
           $this->display();
          
    }

    

  

}