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
class ClassRoomController extends AdminController {
	public function index(){
       $keyword  =  I('keyword');

        if(I('get.keyword')){
            $map['class_room_name']= array('like','%'.$keyword.'%');
        }

      $list = $this->lists('ClassRoom', $map , 'sort asc');
      int_to_string($list);
	    $this->assign('_list',$list);
      $this->display();
	}
/*添加*/
	public function add(){
		if(IS_POST){
      $ClassRoom = M('ClassRoom');
			$post = I('post.');
			$post['create_time'] = time();
		   /*教室名称和名称不能为空*/
           if($post['class_room_name']==''||$post['class_room_name']==null){
                $this->error('教室名称不能为空');exit();
           } 
           /*教室名称重复验证*/
           $map['class_room_name'] = array('eq',$post["class_room_name"]);
           if($ClassRoom->where($map)->find()){
                $this->error('教室名称重复');exit();
           }  
           /*执行添加操作*/
           if($ClassRoom->add($post)){
              $this->success('添加成功',U('ClassRoom/index'));
           }else{
              $this->error('添加失败'); 
           }
		}else{
         $this->display();
		}
	}
/*编辑*/
	public function edit(){
     $ClassRoom = M('class_room');
		if(IS_POST){
		  $post = I('post.');
		  $post['update_time'] = time();
		  /*教室名称不能为空*/
           if($post['class_room_name']==''||$post['class_room_name']==null){
                $this->error('教室名称不能为空');exit();
           }
           /*执行更新*/
           if($ClassRoom->save($post)){
             $this->success('编辑成功',U('ClassRoom/index'));
           }else{ 
             $this->error('编辑失败');
           }
		}else{
		   $id = I('get.id',0);
		   /*未获取到id退出程序*/
           $id || $this->error('未知错误');
           $data = $ClassRoom->find($id);
           /*根据id未查找到数据退出程序*/
           if(empty($data)){
              $this->error('没有数据');exit();
           }
           $this->data = $data;	
           $this->display();
		}
	}
/*删除*/
	public function del(){
		$ClassRoom = M('ClassRoom');
	   if(IS_POST){
        /*POST为批量删除*/
        $ids = array_unique((array)I('post.ids'));
        $ids = implode(',', $ids);
        $where['id'] = array('in',$ids);
	       if($ClassRoom->where($where)->delete()){
	          $this->success('删除成功');  
	       }else{
              $this->error('删除失败');
	       }
	   }else{
         /*单个删除,GET获取*/
          if($ClassRoom->delete(I('get.id'))){
              $this->success('删除成功');
          }else{
              $this->error('删除失败');
          }
	   }
	}
}