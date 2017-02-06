<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller; 
use Wechat\TPWechat;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class NoticeController extends AdminController {
  function _initialize(){
    parent::_initialize();

        $this->options = array('appid'=>C('appid'),'appsecret'=>C('appsecret'),'token'=>C('token'),'encodingaeskey'=>C('encodingaeskey'));
        $this->wechatObj = new TPWechat($this->options);

        $this->db_n = M('Notice');
        $this->db_t = M('Wx_tpl');
        $this->db_s = M('Students');
        $this->db_c = M('Class');
        $this->db_cy = M('Class_years'); 

        $tpls = $this->db_t ->field("id,title")->select();
        $this->assign('tpls',$tpls);
   }

	public function index(){

     $keyword = I('keyword');
     $subject_id =I('subject_id');

      $field="n.id,n.title,n.time,n.sender,s.username,c.class_name,y.year_name";
      $where="n.user_id = s.id and s.class_id = c.id and c.class_year_id =y.id";

       
        $count          =  $this->db_n ->table('dk_students s,dk_class c,dk_class_years y,dk_notice n')->where($where)->count();
        $page           = new \Think\Page($count,10);
        // $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $show           = $page->show();
        
     $list = $this->db_s->table('dk_students s,dk_class c,dk_class_years y,dk_notice n')->field($field)->where($where)->limit($page->firstRow.','.$page->listRows)->order('time DESC')->select();
    // dump($list);
    // exit();
     foreach ($list as $key => $val) {
         $list[$key]['time'] = date('Y-m-d H:i:s',$val['time']);
     }
    

   

      int_to_string($list);
      // $this->assign('keyword',$keyword);
      // $this->assign('subject_id',$subject_id);
      $this->assign('_page',$show);
	    $this->assign('_list',$list);
      $this->display();
	}

   public function refreshTmp(){ //刷新微信模板 
    
      $tmplates = $this->wechatObj->getAllPrivateTemplate();
      $this->db_t->where('1')->delete(); 
      foreach ($tmplates['template_list'] as $key => $value) {
        $res =    $this->db_t->add($value);
      }
      if ($res) {
         redirect(U('add'));
      }else{
        $this->error('刷新失败');
      }

    }

  public function add(){
    if (IS_POST) {
      $id = I('post.id');
      $tmplate = $this->db_t ->where(array('id'=>$id))->find();
      if ($tmplate) {
        $datafrom = self::MaketmpFrom($tmplate['content']);
            $commefrom['url'] = array(
                'title' => '自定义链接',
                'key' => array('key'=>'url', 'value'=>'','placeholder'=>'填写一个想要链接的地址！')
            );
            $commefrom['template_id'] = array(
                'title' => '模版ID',
                'key' => array('key'=>'template_id', 'value'=>$tmplate['template_id'],'placeholder'=>'')
            );
            

            $this->assign('curr_template', $tmplate['id']);
            $this->assign('commefrom', $commefrom);
            $this->assign('datafrom', $datafrom);
      }else{
        $this->error('获取模版出错');
      }
      $this->display();

    }else{
      
      $this->display();
    }
  }

  public function info(){
    $id= I('get.id');
    $field="s.username,n.title,n.sender,content,n.time";
    $where="s.id = n.user_id and n.id ={$id}";
    $info = $this->db_n->table("dk_students s,dk_notice n")->field($field)->where($where)->find();
    $info['content'] = objarray_to_array(json_decode($info['content']));
    
    $template_id = $this->db_n ->where( array('id'=>$id) )->getField('template_id');
    $content= $this->db_t->where( array('template_id'=>$template_id) )->getField('content');
    $datafrom = self::MaketmpFrom($content); //模版
    
    
    $arr = array();
    foreach ($info['content'] as $key => $val) {//获取value
       $arr[] = $val['value'];
    }

    foreach ($datafrom as $key => $val) {//重新拼接数组
      
        foreach ($arr as $key1 => $val1) {
 
           $datafrom[$key]['key']['value']= $arr[$key];
       }
      
    }
   
    $this->assign('info',$info);
    $this->assign('datafrom',$datafrom);
    $this->display();

  }



  public function MaketmpFrom($content)
    {
        $content = nl2br($content);
        $ms = explode('<br />',  $content);
        foreach ($ms as $k => $value)
        {
            preg_match("/.*\{+/",$value, $title);
            preg_match("/\w+[^\.]/",$value, $key);
            $_title = preg_replace("/(：\{+)|(\{+)/", "", $title[0]);
            if ($_title)
            {
                $dd[$k]['title'] = $_title;
                $dd[$k]['key'] = array('key'=>$key[0], 'value'=>'','placeholder'=>'填写'.$_title);
            }else
            {
              $dd[$k]['title']=$k==0?'模版标题':'备注';
              $dd[$k]['key']=$k==0?array('key'=>$key[0], 'value'=>'','placeholder'=>'填写模板消息标题！'):array('key'=>$key[0], 'value'=>'','placeholder'=>'填写模板消息摘要！');
            }
        }
        return $dd;
    }

  public function sendMsg(){
      
      $all_id = I('post.all_id');
      $title = I('post.title');
      $sender =I('post.sender');
      if ( empty($title) ) {
          $this->error('标题不能为空');
      }

      if ( empty($sender) ) {
         $this->error('发送者不能为空');
      }

      if ( empty($all_id) ) {
          $this->error('请选择接受者');
      }


    
      $all_arr = explode(',',$all_id);

      $user_id = array();
   
      foreach ($all_arr as $key => $val) { //排除班级ID
           if ((int)$all_arr[$key] == false) {
               continue;
           }
           $user_id[] = $val;        
      } 

     foreach ($user_id as $key => $val) { //循环发送微信模版

                  $send = self::makTmpdata();

                  if ( empty($send['template_id']) ) {
                     $this->error('请选择模板消息，填写后再发送');
                  }

                  $send['touser'] = $this->db_s->where( array('id'=>$val) )->getField('openid');//获取OpenId
                
                  $res =  $this->wechatObj->sendTemplateMessage($send); //消息推送

                  //入库操作 

                  $data['title'] = I('post.title');
                  $data['sender'] = I('post.sender');
                  $data['user_id'] = $val;
                  $data['time'] = time();
                  $data['content'] = json_encode($send['data']);
                  $data['template_id'] = $send['template_id'];

                  $res2 =$this->db_n->add($data);  

                 
     }
       
     if ($res && $res2) {
          $this->success('成功发送消息',U('Notice/index'));
       }else{
          $this->error('发送消息失败，请稍后再试');
       } 
    
   
     

     
  }  

   public function makTmpdata(){
        $data = I('post.');
        
        $template_id = $data['commefrom']['template_id'];
      
        $senddata = array(
            'touser' => '',
            'template_id' => $template_id,
            'url' => $data['commefrom']['url'],
            'data' => array()
        );

        foreach ($data['body'] as $key => $value) {
           if ( $value['key'] == 'first' || $value['key'] == 'remark' ) { //标题 备注标识红色
              $senddata['data'][$value['key']] = array('value' => $value['value'], 'color' => '#FF0000');
            }else{
              $senddata['data'][$value['key']] = array('value' => $value['value'], 'color' => '#173177');
            }
           
        }

        return $senddata;
    }

	
 public function getData(){
        if(IS_POST){
          $field="c.id,c.class_name,cy.year_name";
          $where="c.class_year_id = cy.id";
          $class_list = $this->db_c ->table("dk_class c,dk_class_years cy")->field($field)->where($where)->select();//班级列表

      
        foreach ($class_list as $key => $val) {
            $class_list[$key]['id'] = "班级".$val['id'];
            $class_list[$key]['name'] = $val['year_name'].'-'.$val['class_name'];
            $class_list[$key]['pId'] = 0;
            unset(  $class_list[$key][ 'class_name'] );
            unset(  $class_list[$key][ 'year_name' ] );
        }
       
       
        $user_list = $this->db_s ->table("dk_students s,dk_class c")->field("s.id,s.username,s.class_id")->where("s.class_id = c.id")->select(); //用户班级列表

        foreach ($user_list as $key => $val) {
            $user_list[$key]['name'] = $val['username'];
            $user_list[$key]['pId'] = "班级".$val['class_id'];
            unset( $user_list[$key]['username']  );
             unset( $user_list[$key]['class_id']  );
        }

        $all_list = array_merge($class_list,$user_list);//合并数组

        // dump($all_list);exit();
      


        echo (json_encode($all_list));

        
           // $add = $this->db_auth_rule->field('id ,pid as pId,title as name')->select();
           //  //组装
           //      foreach ($add as $key=>$val) {
           //         $add[$key]['pId'] = $val['pid'];
        
           //      }

       
           
           // echo (json_encode($add));
         }else{
            $this->error('非法请求');
         }  
    }
  

 

  

  
}
