<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class SignController extends AdminController {
  function _initialize(){
    parent::_initialize();
        $subjects  = M('Subjects')->field('id,subject_name')->select();
        $this->assign('subjects',$subjects);

        $this->user_id  = I('get.user_id');
        $this->class_hour_id = I('get.class_hour_id');
        $this->assign('user_id',$this->user_id);
        $this->assign('class_hour_id',$this->class_hour_id);
   }

	public function index(){

     $keyword = I('keyword');
     $subject_id =I('subject_id');

      $field="s.id,cch.class_hour_id,s.username,c.class_name,y.year_name,su.subject_name,ch.start_time,ch.end_time";
      $where="s.class_id = c.id and c.class_year_id = y.id  and s.id = cch.user_id and cch.class_hour_id = ch.id and ch.subject_id = su.id";

     if ( !empty($keyword) ) {
        $where .=" and s.username like '%{$keyword}%'";
     }

     if ( !empty($subject_id) ) {
        $where .=" and su.id = {$subject_id}";
     }
    // echo $where;exit();
       
    
       
        $count          =  M('Students') ->table('dk_students s,dk_class c,dk_class_years y,dk_choice_class_hour cch,dk_class_hour ch,dk_subjects su')->where($where)->count();
        $page           = new \Think\Page($count,10);
        // $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show           = $page->show();
        
     $list = M('Students')->table('dk_students s,dk_class c,dk_class_years y,dk_choice_class_hour cch,dk_class_hour ch,dk_subjects su')->field($field)->where($where)->group('s.id,ch.id,su.id')->limit($page->firstRow.','.$page->listRows)->select();
    // dump($list);
    // exit();
     foreach ($list as $key => $val) {

        $list[$key]['class_hour_time'] = date('m-d H:i',$val['start_time']).'--'.date('m-d H:i',$val['end_time']);

        unset($list[$key]['start_time']);
        unset($list[$key]['end_time']);

        $list[$key]['s_sign_time'] = M('Sign_history')->where( array('user_id'=>$val['id'],'class_hour_id'=>$val['class_hour_id'],'sign_type'=>1) )->getField('sign_time');
        $list[$key]['s_sign_time'] = !empty($list[$key]['s_sign_time'])?date('m-d H:i',$list[$key]['s_sign_time']):'<font color="red">未打卡</font>';//时间日期格式化

        $list[$key]['e_sign_time'] = M('Sign_history')->where( array('user_id'=>$val['id'],'class_hour_id'=>$val['class_hour_id'],'sign_type'=>2) )->max('sign_time');

         $list[$key]['e_sign_time'] = !empty($list[$key]['e_sign_time'])?date('m-d H:i',$list[$key]['e_sign_time']):'<font color="red">未打卡</font>';//时间日期格式化

         $list[$key]['sign_address'] = M('Sign_history')->where( array('user_id'=>$val['id'],'class_hour_id'=>$val['class_hour_id'],'sign_type'=>2) )->getField('sign_address');

     }

   

      int_to_string($list);
      $this->assign('keyword',$keyword);
      $this->assign('subject_id',$subject_id);
      $this->assign('_list',$list);
      $this->assign('_page',$show);
	    
      $this->display();
	}

  public function add(){
    if (IS_POST) {
       $_data = I('post.');
       // dump($_data);exit();
       if ( empty($_data['user_id']) ) {
          $this->error('用户ID不能为空');
       }else{
          if ( !M('Students')->where( array('id'=>$_data['user_id']) )->find() ) {
            
             $this->error('该用户不存在，请核实用户ID');
          }
       }

       if ( empty($_data['class_hour_id']) ) {
           $this->error('请选择课时');
       }

       if ( empty($_data['sign_time']) ) {
           $this->error('请添加签到时间');
       }

        M()->startTrans();//开启事物
       //查询是否选课程
       if ( !M('Choice_subjects')->where( array('user_id'=>$_data['user_id'],'subject_id'=>$_data['subject_id']) )->find() ) {
           $dataS['user_id'] = $_data['user_id'];
           $dataS['subject_id'] =  $_data['subject_id'];
           $res2 = M('Choice_subjects')->add($dataS);
       }else{
           $res2 = true;
       }
      //查询是否选课时 
       if ( !M('Choice_class_hour')->where( array('user_id'=>$_data['user_id'],'class_hour_id'=>$_data['class_hour_id']) )->find() ) {
           $dataC['user_id'] = $_data['user_id'];
           $dataC['class_hour_id'] = $_data['class_hour_id'];
           $res3 = M('Choice_class_hour')->add($dataC);
       }else{
           $res3 = true; 
       }

       $data['user_id'] = $_data['user_id'];
       $data['class_hour_id'] = $_data['class_hour_id'];
       $class_room_id = M('Class_hour')->where(array('id'=>$_data['class_hour_id']))->getField('class_room_id');//教师ID
       $data['sign_address'] = M('Class_room')->where( array('id'=>$class_room_id) )->getField('class_room_name');
       $data['sign_time'] = strtotime( $_data['sign_time'] );
       $data['sign_type'] = $_data['sign_type'];

       $res = M('Sign_history')->add($data);

       if ( $res2 && $res3 && $res  ) {
         M()->commit();
          $this->success('添加成功',U('Sign/index'));
       }else{
         M()->rollback();
          $this->error('添加失败');
       }


    }else{
      $subjects = M('Subjects')->select();
      $this->assign('subjects',$subjects);
      $this->display();
    }
  }

	public function edit(){
		if(IS_POST){
      $_data = I('post.');
         if ( empty($_data['start_time']) ) {
              $this->error('上课签到时间不能为空');
         }

         if ( empty( $_data['end_time'] ) ) {
              $this->error('下课签到时间不能为空');
         }

         if ( strtotime($_data['start_time'])> strtotime($_data['end_time']) ) {
            $this->error('上课签到时间不能大于下课签到时间');
         }
         
         M()->startTrans();  

        if ( M('Sign_history')->where( array('user_id'=>$_data['user_id'],'class_hour_id'=>$_data['class_hour_id'],'sign_type'=>1) )->find() ) {
           $res =  M('Sign_history')->where( array('user_id'=>$_data['user_id'],'class_hour_id'=>$_data['class_hour_id'],'sign_type'=>1) )->setField('sign_time',strtotime($_data['start_time']));
        }else{
           $data['user_id'] = $_data['user_id'];
           $data['class_hour_id'] = $_data['class_hour_id'];
           $class_room_id = M('Class_hour')->where( array('id'=>$_data['class_hour_id']) )->getField('class_room_id');
           $data['sign_address'] = M('Class_room')->where( array('id'=>$class_room_id) )->getField('class_room_name');  
           $data['sign_time'] = strtotime($_data['start_time']);
           $data['sign_type'] = 1;

           $res =  M('Sign_history')->add($data);
        }
        

         $res2 = M('Sign_history')->where( array('user_id'=>$_data['user_id'],'class_hour_id'=>$_data['class_hour_id'],'sign_type'=>2) )->delete(); //删除所有下课打卡
         
         $data['user_id'] = $_data['user_id'];
         $data['class_hour_id'] = $_data['class_hour_id'];
         $data['sign_type'] = 2;
         $data['sign_time'] = strtotime( $_data['end_time'] ); 

         $res3 = M('Sign_history')->where( array('user_id'=>$_data['user_id'],'class_hour_id'=>$_data['class_hour_id'],'sign_type'=>2) )->add($data); //添加新的修改

         if ($res !== false && $res2 !== false && $res3 !==false ) {
           M()->commit();
           $this->success('编辑成功',U('Sign/index'));
         }else{
           M()->rollback();
           $this->error('编辑失败');
         }

		}else{
             
           $username = M('Students')->where( array('id'=>$this->user_id) )->getField('username');

           $subject_info = M('Subjects')->table('dk_class_hour ch,dk_subjects s')->where("ch.id= {$this->class_hour_id} and ch.subject_id = s.id")->find();
     

           // dump($subject_info);exit();

           $s_sign_time = M('Sign_history')->where( array('user_id'=>$this->user_id,'class_hour_id'=>$this->class_hour_id,'sign_type'=>1) )->getField('sign_time');

           $s_sign_time = !empty($s_sign_time)?$s_sign_time:'';
       
           $e_sign_time =  M('Sign_history')->where( array('user_id'=>$this->user_id,'class_hour_id'=>$this->class_hour_id,'sign_type'=>2) )->max('sign_time');

           $e_sign_time = !empty($e_sign_time)?$e_sign_time:'';

           $this->assign('username',$username);
           $this->assign('subject_info',$subject_info);
           $this->assign('s_sign_time',$s_sign_time);
           $this->assign('e_sign_time',$e_sign_time);
           $this->display();
		}
	}

  

  public function del(){
    // print_r($_GET['id']);die;
     $user_id = I('get.user_id');
     $class_hour_id = I('get.class_hour_id');

     $res = M('Sign_history')->where( array('user_id'=>$user_id,'class_hour_id'=>$class_hour_id) )->delete();

     if ($res) {
         $this->success('删除成功');
     }else{
         $this->success('删除失败');
     }
     
  }

  public function getClassHour(){
       $subject_id  =I('post.subject_id');
       $data = M('Class_hour')->field('id,start_time,end_time')->where( array('subject_id'=>$subject_id) )->select();
       foreach ($data as $key => $val) {
          $data[$key]['class_hour_time'] = date('m-d H:i',$val['start_time']).'--'.date('m-d H:i',$val['end_time']);
          unset($data[$key]['start_time']);
          unset($data[$key]['end_time']);
       }

       echo json_encode($data);
 
  }

  
}
