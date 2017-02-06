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
class DaoController extends Controller {

   function _initialize(){
         // parent::_initialize();
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

	 
    public function dao(){
       
        $data =  $this->db_choice_class_hour->group('user_id,class_hour_id')->select();

        foreach ($data as $key => $val) {
               $data['subject_id'] = $this->db_class_hour->where(array('id'=>$val['class_hour_id']))->getField('subject_id');
               $data['user_id']= $val['user_id'];
               if ( $this->db_choice_subjects->where(array('user_id'=>$data['user_id'],'subject_id'=>$data['subject_id']))->find() ) {
                 continue;
               }
               $res = $this->db_choice_subjects->add($data);
        }

        if ($res) {
           echo '成功';
        }else{
          echo '失败';
        }


    }

  

}