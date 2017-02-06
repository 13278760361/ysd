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
 * 后台频道控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class ClassController extends AdminController {
/*年份列表*/
     public function class_years(){
     	 $keyword  =  I('keyword');

        if(I('get.keyword')){
            $map['year_name']= array('like','%'.$keyword.'%');
        }
     	 $list = $this->lists('class_years', $map , 'id asc');
     	 int_to_string($list);
     	 $this->assign('_list',$list);
         $this->display();
     }
/*添加年份*/
     public function class_year_add(){
	     	if(IS_POST){
	     	 if(M('class_years')->where('year_name='.I('year'))->find()){
                $this->error('该年份已存在');exit;
	     	 }	
	     		$data['year_name'] = I('year');
	          if(M('class_years')->add($data)){
                $this->success('添加成功',U('Class/class_years'));
	          }else{
                $this->error('添加失败');
	          }
	     	}else{
              $this->display();  
	     	}    	
     }
/*删除年份*/
     public function class_year_del(){
     	$years = M('class_years');
	   if(IS_POST){
        /*POST为批量删除*/
        $ids = array_unique((array)I('post.ids'));
        $ids = implode(',', $ids);
        $class_where['class_year_id'] = array('in',$ids);
        if(M('class')->where($class_where)->find()){
          $this->error('该年份下有班级,不能删除');die;
        }
        $where['id'] = array('in',$ids);
	       if($years->where($where)->delete()){
	          $this->success('删除成功');  
	       }else{
              $this->error('删除失败');
	       }
	   }else{
         /*单个删除,GET获取*/
          $class_where['class_year_id'] = array('eq',I('get.id'));
        if(M('class')->where($class_where)->find()){
          $this->error('该年份下有班级,不能删除');die;
        }

          if($years->delete(I('get.id'))){
              $this->success('删除成功');
          }else{
              $this->error('删除失败');
          }
	   }
     }
/*班级列表*/
     public function class_index(){
     	$keyword  =  I('keyword');

        if(I('get.keyword')){
            $map['class_name']= array('like','%'.$keyword.'%');
        }
         $map['class_year_id'] = array('eq',I('get.id'));
     	 $list = $this->lists('class', $map , 'id asc');
     	 foreach ($list as $key => $value) {
     	 	 $year_name  = M('class_years')->where(array('id'=>$value['class_year_id']))->getField('year_name');
     	 	  $list[$key]['class_year_name'] = $year_name;
     	 	  unset($year_name);
     	 }
     	 int_to_string($list);
     	 $this->assign('_list',$list);
       $this->assign('year_list',M('class_years')->select());
       $this->display();
     }
/*添加班级*/
     public function class_add(){
         if(IS_POST){

        if(I('post.class_name')==''){
           $this->error('请填写班级名称');die;
        }
        if(I('post.class_year_id')==''){
           $this->error('请选择班级年份');die;
        }
        $ret = M('class')->where(array('class_name'=>I('post.class_name'),'class_year_id'=>I('post.class_year_id')))->find();
        if($ret){
          $this->error('班级名称已存在');die;
        }
		        $data = I('post.');
		        if(M('class')->add($data)){
		          $this->success('添加成功',U('Class/class_index',array('id'=>I('post.class_year_id'))));
		        }else{
		          $this->error('添加失败');
		        }
 
         }else{
           $this->assign('years',M('class_years')->select());	
           $this->display();
         }
     }

     /*班级删除*/
     public function class_del(){
        $years = M('class_years');
           if(IS_POST){
              /*POST为批量删除*/
              $ids = array_unique((array)I('post.ids'));
              $ids = implode(',', $ids);
              $class_where['class_id'] = array('in',$ids);

              if(M('students')->where($class_where)->find()){
                $this->error('该班级下有学生,不能删除');die;
              }
              $where['id'] = array('in',$ids);
               if(M('class')->where($where)->delete()){
                  $this->success('删除成功');  
               }else{
                    $this->error('删除失败');
               }
           }else{
               /*单个删除,GET获取*/
               //print_r($_GET['id']);die;
                $class_where['class_id'] = array('eq',I('get.id'));
              
              if(M('students')->where($class_where)->find()){
                $this->error('该班级下有学生,不能删除');die;
              }

                if(M('class')->delete(I('get.id'))){
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
           }
     }

     /*修改班级*/
     public function class_edit(){

     if(IS_POST){ 	
     	 if(I('post.class_name')==''){
           $this->error('请填写班级名称');die;
        }
        if(I('post.class_year_id')==''){
           $this->error('请选择班级年份');die;
        }
        // $ret = M('class')->where(array('class_name'=>I('post.class_name')))->find();
        // if($ret){
        //   $this->error('班级名称已存在');die;
        // }
		        $data = I('post.');
		        if(M('class')->save($data)){
		          $this->success('添加成功',U('Class/class_index',array('id'=>I('post.class_year_id'))));
		        }else{
		          $this->error('添加失败');
		        }
 
         }else{
           $this->assign('data',M('class')->where('id='.I('get.id'))->find());	
           $this->assign('years',M('class_years')->select());	
           $this->display('class_add');
         }
     }

     public function student_list(){
        $keyword  =  I('keyword');

        if(is_numeric($keyword)){
            $map['student_no']= array('like','%'.$keyword.'%');
        }else{
            $map['username'] = array('like', '%'.(string)$keyword.'%');
        }
          $map['class_id'] = array('eq',I('get.id'));
          $list = $this->lists('students', $map , 'id asc');
          int_to_string($list);
          $this->assign('_list',$list);
          $this->display();
     }
}