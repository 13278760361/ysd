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
class ChoiceController extends BaseController {

	 function _initialize(){
        parent::_initialize();
	 	    $this->db = M('Students');
        $this->db_class_hour = M('Class_hour');
        $this->db_choice_class_hour = M('Choice_class_hour');
        $this->db_subjects = M('Subjects');
        $this->db_choice_subjects = M('Choice_subjects');

        //测试开启
        // $this->user_id = 20;


        $choice_s_ids =  implode(',', $this->db_choice_subjects->where(array('user_id'=>$this->user_id))->getField('subject_id',true));   //当前用户选课ID

        $this->choice_s_ids = !empty($choice_s_ids)?$choice_s_ids:0;//如果没选过课程默认为O

       
   
    }
	//选课页面
    public function index(){

        if (IS_POST) {

                $ids = I('post.ids');

                // dump($ids);exit();

                if (empty($ids)) {
                   $this->error('请选择课程');
                }

                
                $idsArr =   explode(",", $ids);

                $this->db_choice_class_hour->startTrans(); //事物开启




                //添加所选课时
                $class_hour_ids = array();
                foreach ($idsArr as $key => $val) {
                    $dataS['user_id'] = $this->user_id;
                    $dataS['subject_id'] = $val;
                    if ( $this->db_choice_subjects->where( $dataS )->find() ) {
                         $this->error('请勿重复提交');
                    }
                    $res = $this->db_choice_subjects->add($dataS);//添加所选课程
                    $class_hour_ids_arr =  $this->db_class_hour->where( array('subject_id'=>$val) )->getField('id',true);
                    foreach ($class_hour_ids_arr as $key_1 => $val_1) {
                         $dataC['user_id'] = $this->user_id;
                         $dataC['class_hour_id'] = $val_1;
                         if ( $this->db_choice_class_hour->where( $dataC )->find() ) {
                             $this->error('请勿重复提交');
                         }
                        $res_2 = $this->db_choice_class_hour->add($dataC);//添加所选课时
                    }
                }
     

                if ($res && $res_2) {
                    $this->db_choice_class_hour->commit(); 
                    $this->success('选课成功',U('Sign/index'));
                }else{
                    $this->db_choice_class_hour->rollback();
                    $this->error('选课失败');
                }

        }else{

            if ( empty($this->user_id) ) {  //检测是否绑定
               redirect( U('Public/login') );
            }
            
            $keyword = I('get.keyword');

            //课程查询二期

            $field ="s.id,s.subject_name,s.subject_score,subject_time";

            // $where="s.id = c.subject_id and s.id NOT IN ({$this->choice_s_ids})  and unix_timestamp(now()) < c.end_time";

               $where="s.id = c.subject_id ";

            if ($keyword) {
                 $where .=" and s.subject_name like '%{$keyword}%'";  
            }  

            $lists =   $this->db_subjects->table('dk_subjects s,dk_class_hour c')->field($field)->where($where)->group('c.subject_id')->limit(0,7)->select();

            foreach ($lists as $key => $val) {
               //该学生课程状态查询
               if ( $this->db_choice_subjects->where( array('subject_id'=>$val['id'],'user_id'=>$this->user_id ) )->find()  ) {
                    if ( $this->db_choice_subjects->where( array('subject_id'=>$val['id'],'user_id'=>$this->user_id,'status'=>1 ) )->find() ) {
                        $lists[$key]['subject_type'] = 2; //已修
                    }else{
                        $lists[$key]['subject_type'] = 1; //已选，维修
                    }
               }else{
                   $lists[$key]['subject_type'] = 0; //未选，未修
               }


                 $class_hour_filed = 'c.start_time,c.end_time,cr.class_room_name,t.teacher_name';   
                 $class_hour_where  = "c.teacher_id = t.id and c.class_room_id = cr.id and c.subject_id ={$val['id']}";
                 $lists[$key]['class_hour'] =   $this->db_class_hour->table("dk_class_hour c,dk_teachers t,dk_class_room cr")->field($class_hour_filed)->where($class_hour_where)->order('c.start_time asc')->select();


                 foreach ($lists[$key]['class_hour']as $key_1 => $val_1) {
                    $lists[$key]['class_hour'][$key_1]['month'] = date('m-d',$val_1['start_time']);
                     $lists[$key]['class_hour'][$key_1]['start_time'] = date('H:i',$val_1['start_time']);
                     $lists[$key]['class_hour'][$key_1]['end_time'] = date('H:i',$val_1['end_time']);

                     $lists[$key]['teacher_name'] = $val_1['teacher_name']; //默认教师不变
                 }
            }

        

      
        $this->assign('lists',$lists);  
        $this->assign('keyword',$keyword);  
        
        
        
        $this->display();
        }
          
         
    }

    public function ajaxClass(){

        $page = I('post.page');
        
        $num = 7; 

        $start = ($page - 1)*$num; 

          $keyword = I('get.keyword');

            //课程查询二期

            $field ="s.id,s.subject_name,s.subject_score,subject_time";

            // $where="s.id = c.subject_id and s.id NOT IN ({$this->choice_s_ids})  and unix_timestamp(now()) < c.end_time";

               $where="s.id = c.subject_id ";

            if ($keyword) {
                 $where .=" and s.subject_name like '%{$keyword}%'";  
            }  

            $lists =   $this->db_subjects->table('dk_subjects s,dk_class_hour c')->field($field)->where($where)->group('c.subject_id')->limit($start,$num)->select();

            foreach ($lists as $key => $val) {
               //该学生课程状态查询
               if ( $this->db_choice_subjects->where( array('subject_id'=>$val['id'],'user_id'=>$this->user_id ) )->find()  ) {
                    if ( $this->db_choice_subjects->where( array('subject_id'=>$val['id'],'user_id'=>$this->user_id,'status'=>1 ) )->find() ) {
                        $lists[$key]['subject_type'] = 2; //已修
                    }else{
                        $lists[$key]['subject_type'] = 1; //已选，维修
                    }
               }else{
                   $lists[$key]['subject_type'] = 0; //未选，未修
               }


                 $class_hour_filed = 'c.start_time,c.end_time,cr.class_room_name,t.teacher_name';   
                 $class_hour_where  = "c.teacher_id = t.id and c.class_room_id = cr.id and c.subject_id ={$val['id']}";
                 $lists[$key]['class_hour'] =   $this->db_class_hour->table("dk_class_hour c,dk_teachers t,dk_class_room cr")->field($class_hour_filed)->where($class_hour_where)->order('c.start_time asc')->select();


                 foreach ($lists[$key]['class_hour']as $key_1 => $val_1) {
                    $lists[$key]['class_hour'][$key_1]['month'] = date('m-d',$val_1['start_time']);
                     $lists[$key]['class_hour'][$key_1]['start_time'] = date('H:i',$val_1['start_time']);
                     $lists[$key]['class_hour'][$key_1]['end_time'] = date('H:i',$val_1['end_time']);

                     $lists[$key]['teacher_name'] = $val_1['teacher_name']; //默认教师不变
                 }
            }
      
       echo json_encode($lists);
    }




}