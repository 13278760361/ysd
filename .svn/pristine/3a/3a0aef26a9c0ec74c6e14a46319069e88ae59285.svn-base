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
class SubjectsController extends AdminController {
	public function index(){
       $keyword  =  I('keyword');
        if(I('get.keyword')){
            $map['subject_name']= array('like','%'.$keyword.'%');
        }
      $list = $this->lists('Subjects', $map , 'id asc');
      int_to_string($list);
      $this->assign('_list',$list);
      $this->display();
	}
/*添加*/
	public function add(){
       if(IS_POST){
         if(I('post.subject_name')==''){
           $this->error('课程名不能为空,请从新输入。');exit;
         }
         if(!is_numeric(I('post.subject_score'))||!is_numeric(I('post.subject_time'))){
           $this->error('课程学分或课程学时必须填写数字,请从新输入。');exit;
         }
         if(M('subjects')->add(I('post.'))){
           $this->success('添加课程成功',U('Subjects/index'));
         }else{
           $this->error('添加失败,请重试！');
         }
       }else{
        $this->display();
       }
	}

	public function edit(){
       if(IS_POST){
           if(I('post.subject_name')==''){
             $this->error('课程名不能为空,请从新输入。');exit;
           }
           if(!is_numeric(I('post.subject_score'))||!is_numeric(I('post.subject_time'))){
             $this->error('课程学分或课程学时必须填写数字,请从新输入。');exit;
           }
           $data = I('post.');
           if(M('subjects')->save($data)){
             $this->success('编辑成功',U('Subjects/index'));exit;
           }else{
             $this->error('编辑失败,请重试');
           }
       }else{
         if(I('id')==''){
           $this->error('缺失参数id');exit;
         }
         $data = M('subjects')->find(I('id'));
         $this->assign('data',$data);
         $this->display();
       }
	}

  public function del(){
   $Subjects = M('Subjects');
     if(IS_POST){
        /*POST为批量删除*/
        $ids = array_unique((array)I('post.ids'));
        $ids = implode(',', $ids);
        $where['id'] = array('in',$ids);
        $where2['subject_id'] = array('in',$ids);
        $ret = M('class_hour')->where($where2)->select();
       if(!empty($ret)){
         $this->error('所选课程下存在课时,请清除课时后再删除该课程','',10);exit;
       }else{
         if($Subjects->where($where)->delete()){
            $this->success('删除成功');  
         }else{
              $this->error('删除失败');
         }
       }
     }else{
         /*单个删除,GET获取*/
         $ret = M('class_hour')->where(array('subject_id'=>I('get.id')))->find();
         if(!empty($ret)){
           $this->error('该课程下存在课时,请清除课时后再删除该课程','',10);exit;
         }else{
           if($Subjects->delete(I('get.id'))){
              $this->success('删除成功');
           }else{
              $this->error('删除失败');
           }
         }
     }
  }
}