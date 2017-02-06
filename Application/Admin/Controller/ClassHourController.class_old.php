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
class ClassHourController extends AdminController {
  public function index(){
      $subject_id = I('get.subject_id');
      $map['subject_id'] = array('eq',$subject_id);
      $list = $this->lists('class_hour', $map , 'id asc');
      foreach ($list as $key => $value) {
        $list[$key]['subject_name'] = M('subjects')->where(array('id'=>$value['subject_id']))->getField('subject_name');
        $list[$key]['teacher_name'] = M('teachers')->where(array('id'=>$value['teacher_id']))->getField('teacher_name');
        $list[$key]['class_room_name'] = M('class_room')->where(array('id'=>$value['class_room_id']))->getField('class_room_name');
      }
      int_to_string($list);
      $this->assign('_list',$list);
      $this->display();
  }

  public function add(){
    if(IS_POST){
       if(I('post.teacher_id')==''){($this->error('请选择教师'));exit;}
       if(I('post.class_room_id')==''){($this->error('请选择教室'));exit;}
       if(I('post.start_time')==''){($this->error('请选择上课时间'));exit;}
       if(I('post.end_time')==''){($this->error('请选择下课时间'));exit;}
       if(strtotime(I('start_time'))>strtotime(I('end_time'))){
        $this->error('下课时间应大于上课时间');exit;
       }
       if(date('Y-m-d',strtotime(I('start_time')))!=date('Y-m-d',strtotime(I('end_time')))){
        $this->error('上课时间与下课时间,应在同一天','',5);exit;
       }

       $data = I('post.');
       $data['create_time'] = time();
       $data['start_time'] = strtotime(I('start_time'));
       $data['end_time'] = strtotime(I('end_time'));
       $data['date'] =  date('Y-m-d',$data['start_time']);

      if(M('class_hour')->add($data)){
        $this->success('添加成功',U('ClassHour/index',array('subject_id'=>$data['subject_id'])));
      }else{
        $this->error('添加失败');
      }  
    }else{
     $class_room = M('class_room')->field('id,class_room_name')->select(); 
     $teachers = M('teachers')->field('id,teacher_name')->select();
     $this->assign('subject_name',M('subjects')->where(array('id'=>I('get.subject_id')))->getField('subject_name'));     
     $this->assign('class_room',$class_room);
     $this->assign('teachers',$teachers);
     $this->display(); 
    }
  }

  public function edit(){
    if(IS_POST){
       if(I('post.teacher_id')==''){($this->error('请选择教师'));exit;}
       if(I('post.class_room_id')==''){($this->error('请选择教室'));exit;}
       if(I('post.start_time')==''){($this->error('请选择上课时间'));exit;}
       if(I('post.end_time')==''){($this->error('请选择下课时间'));exit;}
       if(date('Y-m-d',strtotime(I('start_time')))!=date('Y-m-d',strtotime(I('end_time')))){
        $this->error('上课时间与下课时间,应在同一天');exit;
       }
       $data = I('post.');
       $data['update_time'] = time();
       $data['start_time'] = strtotime(I('start_time'));
       $data['end_time'] = strtotime(I('end_time'));
       $data['date'] =  date('Y-m-d',$data['start_time']);

      if(M('class_hour')->save($data)){
        $this->success('编辑成功',U('ClassHour/index',array('subject_id'=>$data['subject_id'])));
      }else{
        $this->error('编辑失败');
      }
    }else{
      $data = M('class_hour')->find(I('get.id'));
      $class_room = M('class_room')->field('id,class_room_name')->select(); 
      $teachers = M('teachers')->field('id,teacher_name')->select();
      $this->assign('subject_name',M('subjects')->where(array('id'=>I('get.subject_id')))->getField('subject_name'));
      $this->assign('data',$data);
      $this->assign('class_room',$class_room);
      $this->assign('teachers',$teachers);
      $this->display();
    }
  }

  public function del(){
       $ClassHour = M('class_hour');
     if(IS_POST){
        /*POST为批量删除*/
        $ids = array_unique((array)I('post.ids'));
        $ids = implode(',', $ids);
        $where['id'] = array('in',$ids);
           if($ClassHour->where($where)->delete()){
                $this->success('删除成功');  
           }else{
                $this->error('删除失败');
           } 
     }else{
            /*单个删除,GET获取*/
            if($ClassHour->delete(I('get.id'))){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
         
     }
  }


  /**
     * 按课程导出
     * 导出Excel
     *   A2:A3   B2:B3    C2:C3   D2:D3  |E1:F1  G1:H1  I1:J1   K1:L1|  M1:M2  
     */


  public function exp_students_subject(){

   $list = M('class_hour')->where(array('subject_id'=>I('get.subject_id')))->order('id asc')->select();
   
   foreach ($list as $key => $value) {
      if(!strstr($date,$value['date'])){
          $date .= $value['date'].',';
      } 
   }

   $date = substr($date,0,strlen($date)-1); 
   $date = array_flip(explode(',', $date));

   foreach ($list as $k => $v) {
     foreach ($date as $kk => $vv) {
       if($kk==$v['date']){
          $class_hour_tody[$kk][$v['id']] = $v;
       }
     }
   }

$num = array('E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

 //print_r($class_hour_tody);die;

// print_r(count($class_hour_tody['2016-08-18']));die;
//            'E2'=>array('E2:F2','2016-8-18'),
//            'G2'=>array('G2:H2','2016-8-19'),

/*合并格式组装 -------star*/
$i = '';

foreach ($class_hour_tody as $key => $value) {
   if($i!=''){
      $xls_title_num[$num[$i+1].'2'] = array($num[$i+1].'2:'.$num[count($class_hour_tody[$key])+$i].'2',$key);
   }else{
      $xls_title_num['E2'] = array('E2:'.$num[count($class_hour_tody[$key])-1].'2',$key);
   }
      $i +=  $i == '' ? count($class_hour_tody[$key])-1 : count($class_hour_tody[$key]);
}
  $z=-1;

foreach ($list as $k => $v) {
    $z += 1;
    $data_title[$num[$z].'3'] = array('',date('H:i',$v['start_time']).'-'.date('H:i',$v['end_time']));
}


     $xls_title = array(
          'A2'=> array('A2:A3','序号'),
          'B2'=> array('B2:B3','学号'),
          'C2'=> array('C2:C3','班级'),
          'D2'=> array('D2:D3','姓名'),
         ); 
   
   $xls_title = array_merge_recursive($xls_title,$xls_title_num,$data_title);
   
   
  // print_r($xls_title);die;
/*合并格式组装 -------end*/
 
  foreach ($list as $key => $value) {
     $data_list_title[$key] = $value['date'].'-'.$value['id'];
     $sign_data[$key][0] = $value['date'].'-'.$value['id'];
     $sign_data[$key][1] = '';
  }
  // print_r(array_flip($data_list_title));die;
      $xlsCell  = array(
          array('xh',''),
          array('student_no',''),
          array('class_id',''),
          array('username',''),
        );
      $xlsCell = array_merge_recursive($xlsCell,$sign_data);

      //print_r(array_flip($data_list_title));die;
      //print_r($xlsCell);die;
/*
 *
 */

$sql = 'select std.class_id , std.student_no , std.username ,sh.user_id ,

        GROUP_CONCAT(sh.class_hour_id) as class_hour_id

        from  dk_sign_history as sh, dk_class_hour as ch, dk_subjects as sj  , dk_teachers as th ,dk_students as std          

        where sh.user_id = std.id and sh.class_hour_id = ch.id and ch.subject_id = sj.id and ch.teacher_id = th.id and sh.sign_type=1 and ch.subject_id='.I('get.subject_id').'

        GROUP BY std.class_id , std.student_no , std.username ,sh.user_id'; 
 
     $history_list = M()->query($sql);
  foreach ($data_list_title as $key => $value) {
     $data_list_title[$key] = '';
  }
foreach ($history_list as $key => $value) {
     $data_list[$key] = array_merge_recursive($value,array_flip($data_list_title));
     $data_list[$key]['xh'] = $key;//序号
     $data_list[$key]['class_id'] = $value['class_id'] == '' ? '暂无数据' : $value['class_id'];
     $data_list[$key]['student_no'] = $value['student_no'];
     $data_list[$key]['username'] = $value['username'];  
}

foreach ($data_list as $key => $value){
  if($value['class_hour_id']!=''){
    if(strstr($value['class_hour_id'],",")){
      $ids = explode(',',$value['class_hour_id']);
      foreach ($ids as $k => $v){
        $star_time = M('sign_history')->where(array('class_hour_id'=>$ids[$k],'user_id'=>$value['user_id'],'sign_type'=>1))->find();
        
        $end_time = M('sign_history')->where(array('class_hour_id'=>$ids[$k],'user_id'=>$value['user_id'],'sign_type'=>'2'))->field("max(sign_time)")->find();//$end_time['max(sign_time)']
        
        $kkk = M('class_hour')->where(array('id'=>$ids[$k]))->getField('date');
        $kkk = $kkk.'-'.$ids[$k];
        $endtime = $end_time['max(sign_time)'] == '' ? '' : date('H:i',$end_time['max(sign_time)']);
        $data_list[$key][$kkk] = date('H:i',$star_time['sign_time']).'-'.$endtime;
      }
    }else{
        $star_time = M('sign_history')->where(array('class_hour_id'=>$value['class_hour_id'],'user_id'=>$value['user_id'],'sign_type'=>1))->find();
        
        $end_time = M('sign_history')->where(array('class_hour_id'=>$value['class_hour_id'],'user_id'=>$v['user_id'],'sign_type'=>'2',))->field("max(sign_time)")->find();//$end_time['max(sign_time)']
       
        $endtime = $end_time['max(sign_time)'] == '' ? '' : date('H:i',$end_time['max(sign_time)']);
        $kkk = M('class_hour')->where(array('id'=>$value['class_hour_id']))->getField('date');
        $kkk = $kkk.'-'.$value['class_hour_id'];
        //$array[$kkk] = $start_time['sign_time'].'-'.$end_time['max(sign_time)'];
        $data_list[$key][$kkk] = date('H:s',$star_time['sign_time']).'-'.$endtime;
     }
   }


 }
$xlsName = '云大考勤表';
        $xlsData = $data_list;        /*标题*/ /*格式*/ /*数据*/
     // print_r($xls_title);die;
        $this->exportExcel_subject($xls_title,$xlsName,$xlsCell,$xlsData);

     }


     /**
     * 按课程导出
     * 导出Excel
     *   A2:A3   B2:B3    C2:C3   D2:D3  |E1:F1  G1:H1  I1:J1   K1:L1|  M1:M2  
     */
    public function exportExcel_subject($xls_title,$expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);  //几列
        $dataNum = count($expTableData); //几行

        vendor("PHPExcel.PHPExcel");
            
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');


/*若要标题这里打开 ↓ */
        foreach ($xls_title as $key => $value) {
          if($value[0]!=''){
            $objPHPExcel->getActiveSheet(0)->mergeCells($value[0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($key, $value[1]); 
          }else{
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($key, $value[1]);
          }   
        }


        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);

        for($i=0;$i<$cellNum;$i++){                                /*若要标题 ↓ +1*/
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'4', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){                              /*若要标题 ↓ +1*/
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+4), $expTableData[$i][$expCellName[$j][0]]);
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