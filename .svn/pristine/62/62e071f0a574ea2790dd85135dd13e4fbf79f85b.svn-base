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
class TeachersController extends AdminController {
	public function index(){
       $keyword  =  I('keyword');

        if(is_numeric($keyword)){
            $map['teacher_phone']= array('like','%'.$keyword.'%');
        }else{
            $map['teacher_name'] = array('like', '%'.(string)$keyword.'%');
        }

      $list = $this->lists('teachers', $map , 'sort asc');
      int_to_string($list);
	    $this->assign('_list',$list);
      $this->display();
	}

	public function add(){
		if(IS_POST){
			$teachers = M('teachers');
			$post = I('post.');
			$post['create_time'] = time();
		   /*手机号和名称不能为空*/
           if($post['teacher_name']==''||$post['teacher_phone']==null){
                $this->error('手机号或名称不能为空');exit();
           } 

			/*验证手机号*/
           if(!preg_match("/^1[34578]\d{9}$/", $post['teacher_phone'])){
            	$this->error('手机号码错误');exit();
            }
           /*手机号重复验证*/
           if($teachers->where('teacher_phone='.$post["teacher_phone"].'')->find()){
                $this->error('手机号重复');exit();
           }  
           /*执行添加操作*/
           if($teachers->add($post)){
              $this->success('添加成功',U('Teachers/index'));
           }else{
              $this->error('添加失败'); 
           }
		}else{
         $this->display();
		}
	}

	public function edit(){
		if(IS_POST){
		  $teachers = M('teachers');
		  $post = I('post.');
		  $post['update_time'] = time();
		  /*手机号和名称不能为空*/
           if($post['teacher_name']==''||$post['teacher_phone']==null){
                $this->error('手机号或名称不能为空');exit();
           }
			/*验证手机号*/
           if(!preg_match("/^1[34578]\d{9}$/", $post['teacher_phone'])){
            	$this->error('手机号码错误');exit();
            }
           /*执行更新*/
           if($teachers->save($post)){
             $this->success('编辑成功',U('Teachers/index'));
           }else{ 
             $this->error('编辑失败');
           }
		}else{
		   $id = I('get.id',0);
		   /*未获取到id退出程序*/
           $id || $this->error('未知错误');
           $teachers = M('teachers');
           $data = $teachers->find($id);
           /*根据id未查找到数据退出程序*/
           if(empty($data)){
              $this->error('没有数据');exit();
           }
           $this->data = $data;	
           $this->display();
		}
	}

	public function del(){
		$teachers = M('teachers');

	   if(IS_POST){

      $ids['teacher_id'] = array('in',implode(',',I('post.ids')));
      if(I('post.ids')){
        $ret = M('class_hour')->where($ids)->select();
        if(!empty($ret)){
          $this->error('所选教师还有课时,不能删除');
        }
      }
      
        /*POST为批量删除*/
        $ids = array_unique((array)I('post.ids'));
        $ids = implode(',', $ids);
        $where['id'] = array('in',$ids);
	       if($teachers->where($where)->delete()){
	          $this->success('删除成功');  
	       }else{
              $this->error('删除失败');
	       }
	   }else{
         /*单个删除,GET获取*/

         if(M('class_hour')->where('teacher_id='.I('get.id'))->find()){
            $this->error('该教师还有课时');die;
         }
          if($teachers->delete(I('get.id'))){
              $this->success('删除成功');
          }else{
              $this->error('删除失败');
          }
	   }
	}
}