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
class StudentsController extends AdminController {
	public function index(){

       $keyword  =  I('keyword');

        if(is_numeric($keyword)){
            $map['student_no']= array('like','%'.$keyword.'%');
        }else{
            $map['username'] = array('like', '%'.(string)$keyword.'%');
        }
        
      $list = $this->lists('students', $map , 'id asc');
      foreach ($list as $key => $value) {
         $class_name_arr = M('class')->where(array('id'=>$value['class_id']))->find();
         $class_year = M('class_years')->where(array('id'=>$class_name_arr['class_year_id']))->getField('year_name');
         if($class_year==''||$class_name_arr['class_name']==''){
           $list[$key]['class_name']='暂无数据';
         }else{
          $list[$key]['class_name'] = $class_year.'届'.$class_name_arr['class_name'];
         }
      }
      //print_r($list);die;
      int_to_string($list);
	    $this->assign('_list',$list);
      $this->display();
	}

	public function edit(){
		if(IS_POST){
		  $students = M('students');
		  $post = I('post.');
		  $post['time'] = time();
		  /*手机号和名称不能为空*/
           if($post['username']==''||$post['phone']==''){
                $this->error('手机号或名称不能为空');exit();
           }
			/*验证手机号*/
           if(!preg_match("/^1[34578]\d{9}$/", $post['phone'])){
            	$this->error('手机号码错误');exit();
            }
           /*执行更新*/
           if($students->save($post)){
             $this->success('编辑成功',U('Students/index'));
           }else{ 
             $this->error('编辑失败');
           }
		}else{
		   $id = I('get.id',0);
		   /*未获取到id退出程序*/
           $id || $this->error('未知错误');
           $students = M('students');
           $data = $students->find($id);

           /*根据id未查找到数据退出程序*/
           if(empty($data)){
              $this->error('没有数据');exit();
           }
           $data['year_id'] = M('class')->where(array('id'=>$data['class_id']))->getField('class_year_id');
//print_r($data);die;
           $class = M('class')->select();
           $year = M('class_years')->select();

           $this->class = $class;
           $this->year = $year;
           $this->data = $data;
           $this->display();
		}
	}

  /*ajax修改学生班级*/
  public function ajax_edit(){
   if(isset($_POST['class_id'])&&$_POST['class_id']!=''){
      $id = I('post.year_id');
      $data = M('class')->where(array('class_year_id'=>$id))->select();
      foreach ($data as $k => $v) {
          if($v['id']==$_POST['class_id']){
            $str .= '<option value='.$v['id'].' selected class="year_'.$v['class_year_id'].'">'.$v['class_name'].'届</option>';
            }else{
            $str .= '<option value='.$v['id'].' class="year_'.$v['class_year_id'].'">'.$v['class_name'].'届</option>';
           }
         }
      
      $this->ajaxReturn(array('code'=>1,'str'=>$str));

    }else{
      $id = I('post.year_id');
      $data = M('class')->where(array('class_year_id'=>$id))->select();
      foreach ($data as $k => $v) {
         $str .= '<option value='.$v['id'].' class="year_'.$v['class_year_id'].'">'.$v['class_name'].'届</option>';
      }
      $this->ajaxReturn(array('code'=>1,'str'=>$str));
    }
  }

  public function del(){
    $choice_subjects = M('choice_subjects');//或者是M();
    $choice_class_hour = M('choice_class_hour');
    $sign_history = M('sign_history');
    $students = M('students');
    if(IS_POST){
        $ids['user_id'] = array('in',implode(',',I('post.ids')));
        $ids = array_unique((array)I('post.ids'));
        $ids = implode(',', $ids);
        $where['id'] = array('in',$ids);
        if($choice_subjects->where(array('user_id'=>array('in',$ids)))->select()){
            $ret = $choice_subjects->where(array('user_id'=>array('in',$ids)))->delete();
        }else{
            $ret = true;
        }
        if($choice_class_hour->where(array('user_id'=>array('in',$ids)))->select()){
          $ret2 = $choice_class_hour->where(array('user_id'=>array('in',$ids)))->delete();
        }else{
          $ret2 = true;
        }
        if($sign_history->where(array('user_id'=>array('in',$ids)))->select()){
            $ret3 = $sign_history->where(array('user_id'=>array('in',$ids)))->delete();
        }else{
          $ret3 = true;
        }
        if($students->where(array('id'=>array('in',$ids)))->select()){
            $ret4 = $students->where(array('id'=>array('in',$ids)))->delete();
        }
        if($ret && $ret2 && $ret3 && $ret4) {
            $this->success('删除成功');  
        }else{
            $this->error('删除失败');
        }
    }else{
      $user_id = I('get.id');
      $choice_subjects->startTrans();//在第一个模型里启用就可以了，或者第二个也行

      if ( $choice_subjects->where(array('user_id'=>$user_id))->find() ) {
         $result  = $choice_subjects->where(array('user_id'=>$user_id))->delete();
      }else{
         $result = true;
      }
     
      if ( $choice_class_hour->where(array('user_id'=>$user_id))->find() ) {
         $result2 = $choice_class_hour->where(array('user_id'=>$user_id))->delete();
      }else{
         $result2 = true;
      }

      if ( $sign_history->where(array('user_id'=>$user_id))->find() ) {
        $result3 = $sign_history->where(array('user_id'=>$user_id))->delete();
      }else{
        $result3 = true;
      }
     
      
      $result4 = $students->where(array('id'=>$user_id))->delete();

      if($result && $result2 && $result3 && $result4){
         $choice_subjects->commit();//成功则提交
         $this->success('删除成功');
      }else{
         $choice_subjects->rollback();//不成功，则回滚
         $this->error('删除失败');
      }
    }
  }

  /*学生所选课程*/
  public function choice_subjects(){
    $user_id = I('get.id');
    $keyword  =  I('keyword');

        if($keyword!=''){
             $subjects = M('subjects')->select();
             $ret = strstr($subjects[0]['subject_name'],$keyword);

             foreach ($subjects as $key => $value) {
               $ret = strstr($subjects[$key]['subject_name'],$keyword);
                if($ret!=''){
                  $subject_id.= ','.$value['id'];
                }
             }
            $subject_id = ltrim($subject_id, ",");
            $map['subject_id'] = array('in',$subject_id);
        }
  
    $map['user_id'] = array('eq',$user_id);
    $list = $this->lists('choice_subjects', $map , 'user_id asc');
    foreach ($list as $key => $value) {
       $subject = M('subjects')->where('id='.$value['subject_id'])->find();
       $list[$key]['subject_name'] = $subject['subject_name'];
       $list[$key]['subject_score'] = $subject['subject_score'];
       $list[$key]['subject_time'] = $subject['subject_time'];
   
       $class_hour_id = M('class_hour')->where('subject_id ='.$value['subject_id'])->field('id')->select();

       $class_hour_id = implode(',', array_column($class_hour_id, 'id'));

       $where_sore['user_id'] = array('eq',$value['user_id']);
       $where_sore['class_hour_id'] = array('in',$class_hour_id);
       $list[$key]['run_score'] = M('choice_class_hour')->where($where_sore)->sum('sum_subject_time');
       
       $e = sprintf("%.1f", $list[$key]['run_score']/$list[$key]['subject_time']*100);
       $list[$key]['width_score'] = $e.'%';
       $list[$key]['score'] = $list[$key]['run_score']/$list[$key]['subject_time'];
       unset($score,$subject,$class_hour_id,$where_sore,$e);
    }
    //print_r($list);die;
    int_to_string($list);
    //print_r($list);die;
    $this->assign('_list',$list);
    $this->display();
   //print_r($list);die;
  }

/*审核*/
  public function subject_sh(){

      

  if(IS_POST){

        if($_POST['sh']=='1'){
          $subject = M('subjects')->where(array('id'=>I('post.subject_id')))->find();
             $data['status'] = 1;
             $data['score'] = $subject['subject_score'];
           $where['user_id'] = array('eq',I('post.user_id'));
           $where['subject_id'] = array('eq',I('post.subject_id'));
          if(M('choice_subjects')->where($where)->save($data)){
            $this->success('审核成功',U('Students/choice_subjects',array('id'=>I('post.user_id'))));
          }else{
            $this->error('审核失败',U('Students/choice_subjects',array('id'=>I('post.user_id'))));
          }
        }else{
             $data2['status'] = 0;
             $data2['score'] = 0;
             $where['user_id'] = array('eq',I('post.user_id'));
           $where['subject_id'] = array('eq',I('post.subject_id'));
          if(M('choice_subjects')->where($where)->save($data2)){
               $this->success('审核成功',U('Students/choice_subjects',array('id'=>I('post.user_id'))));
          }else{

             $this->error('审核失败',U('Students/choice_subjects',array('id'=>I('post.user_id'))));
          }
        }
    }else{
      $where['user_id'] = array('eq',I('get.user_id'));
      $where['subject_id'] = array('eq',I('get.subject_id'));
     $subject = M('subjects')->where(array('id'=>I('get.subject_id')))->find();
      $choice_subjects = M('choice_subjects')->where($where)->find();

      $data = M('students')->where(array('id'=>I('get.user_id')))->find();
      $data['subject_name'] = $subject['subject_name'];
      $data['status'] = $choice_subjects['status'];
    //  print_r($data);die;
      $this->assign('data',$data);
      $this->display();
    }    
  }

  public function subject_del(){
          $choice_subjects = M('choice_subjects');

     if(IS_POST){
   /*POST为批量删除*/   
      $ids = I('post.ids');
        $ids = implode(',', $ids);
     $where['subject_id'] = array('in',$ids);
     $where['user_id'] = array('eq',I('get.user_id'));
     if($choice_subjects->where($where)->delete()){
        $this->success('删除成功');
     }else{
        $this->error('删除失败');
     }
    
      
     }else{
         /*单个删除,GET获取*/
          $where['subject_id'] = array('eq',I('get.subject_id'));
          $where['user_id'] = array('eq',I('get.user_id'));

          if($choice_subjects->where($where)->delete()){
              $this->success('删除成功');
          }else{
              $this->error('删除失败');
          }
     }
  }

  public function import(){
    if(IS_POST){
        if (!empty($_FILES)) {
           $exts = substr(strrchr($_FILES['import']['name'], '.'), 1);
           if($exts!='xls'){
             $this->error('请上传以.xls为后缀的Excel文件','',5);exit;
           }
            $upload = new \Think\Upload();// 实例化上传类
            $filepath=C('UPLOAD_PATH').'Excle/'; 
            $upload->exts = array('xlsx','xls');// 设置附件上传类型
            $upload->rootPath  =  $filepath; // 设置附件上传根目录
            $upload->saveName  =     'time';
            $upload->autoSub   =     false;
            if (!$info=$upload->upload()) {
                $this->error($upload->getError());
            }
            foreach ($info as $key => $value) {
                unset($info);
                $info[0]=$value;
                $info[0]['savepath']=$filepath;
            }
            vendor("PHPExcel.PHPExcel");

            $file_name=$info[0]['savepath'].$info[0]['savename'];

            $objReader = \PHPExcel_IOFactory::createReader('Excel5');

            $objPHPExcel = $objReader->load($file_name,$encode='utf-8');

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
           
            $j=0;
            //print_r($where);die;
            for($i=2;$i<=$highestRow;$i++)
            {
                $data['username'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                $data['sex'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                $data['student_no'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                $data['phone'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                $data['sex'] = $data['sex'] == '男' ? 1 : 0 ;
         
            $student_no = M('students')->where(array('student_no'=>$data['student_no']))->find();
                 if(!empty($student_no)){
                    //如果存在相同联系人。判断条件：电话 两项一致，上面注释的代码是用姓名/电话判断
                    $noT .= '学生-------['.$data['username'].']-------学号------['.$student_no['student_no'].']---------学号已存在,请检查。<br/>'; 
                }else{
                    M('students')->add($data);
                    $j++;
                }
            }
            unlink($file_name);
            // User_log('批量导入联系人，数量：'.$j);
            $this->success('导入成功！本次导入学生数量：'.$j.'<br/>'.$noT,U('Students/index'),100);
             // $this->success('导入成功！本次导入学生数量：'.$j);
        }else
        {
            $this->error("请选择上传的文件");
        }
    }else{
      $this->display();
    }
  }

 
    /**
     * 课时单导
     * 导出Excel
     *   A2:A3   B2:B3    C2:C3   D2:D3  |E1:F1  G1:H1  I1:J1   K1:L1|  M1:M2  
     */


     public function exp_students_class_hour(){

      $xlsCell  = array(
          array('username','姓名'),
          array('student_no','学号'),
          array('subject_name','打卡课程'),
          array('teacher_name','上课教师'),
          array('sign_address','打卡地点'),
          array('star_time','上课打卡时间'),
          array('end_time','下课打卡时间'),
          array('sum_subject_time','上了几分钟'),
        );

     $sql = 'select  s.username ,z.sign_address, kk.sum_subject_time,z.class_hour_id,z.user_id,z.sign_type,s.student_no, t.teacher_name ,z.sign_time, su.subject_name from dk_students as s, dk_teachers as t , dk_class_hour as ch ,dk_choice_class_hour as kk,dk_sign_history as z ,dk_subjects as su where z.user_id=s.id and kk.user_id = s.id and z.class_hour_id=ch.id and ch.teacher_id = t.id and ch.subject_id = su.id and z.sign_type=1 and z.class_hour_id='.I('id').'';
 
     $data2 = M()->query($sql);

      foreach($data2 as $k => $v){
         $data[$k] = $v;
         unset($data[$k]['sign_time']);
         $data[$k]['star_time'] = $v['sign_time'] == '' ? '未打卡' : date("Y-m-d H:i:s",$v['sign_time']);
         $end_time = M('sign_history')->where(array('class_hour_id'=>$v['class_hour_id'],'user_id'=>$v['user_id'],'sign_type'=>2,))->field("max(sign_time)")->find();
         $data[$k]['end_time'] = $end_time['max(sign_time)'] ==''? '未打卡' : date("Y-m-d H:i:s",$end_time['max(sign_time)']);
        $data[$k]['sum_subject_time'] = $data[$k]['sum_subject_time']==0 ? '' : ($data[$k]['sum_subject_time']*60).'分钟';
        unset($data[$k]['class_hour_id'],$data[$k]['user_id'],$data[$k]['sign_type']);
      }
        $xlsName  = '科目:'.$data[0]['subject_name'].'—教师:'.$data[0]['teacher_name'].'—教室:'.$data[0]['sign_address'].'';
        $xlsData = $data;   
        $this->exportExcel2($xlsName,$xlsCell,$xlsData);
     }


         /**
     * 课时单导
     * 导出Excel
     *   A2:A3   B2:B3    C2:C3   D2:D3  |E1:F1  G1:H1  I1:J1   K1:L1|  M1:M2  
     */
    public function exportExcel2($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);  //几列
        $dataNum = count($expTableData); //几行

        vendor("PHPExcel.PHPExcel");
            
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

/*若要标题这里打开 ↓ */
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);
        for($i=0;$i<$cellNum;$i++){                                /*若要标题 ↓ +1*/
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){                              /*若要标题 ↓ +1*/
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}
