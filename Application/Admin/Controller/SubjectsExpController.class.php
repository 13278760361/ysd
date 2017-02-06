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
 *导出管理
 * 
 */
class SubjectsExpController extends AdminController {
	
	public function index(){
		
			$cl_list = M('class')->select();
			$this->assign('cl_list',$cl_list);
			$this->assign('sb_list',M('subjects')->select());
			$this->assign('year_list',M('class_years')->select());
			$this->display();
		
		
	}
	public function class_list(){
		if(I('get.year_id')){
			$data = M('class')->where(array('class_year_id'=>I('get.year_id')))->select();
		}
	    echo json_encode($data);die;
	    
		//echo $data;die;
	}
  /**
     * 按课程导出
     * 导出Excel
     *   A2:A3   B2:B3    C2:C3   D2:D3  |E1:F1  G1:H1  I1:J1   K1:L1|  M1:M2  
     */


 public function exp_students_subject(){
 //print_r(I('get.class_id'));exit;
   if(I('get.subject_id')>0){
	$list = M('class_hour')->where(array('subject_id'=>I('get.subject_id')))->order('id asc')->select();
  // }elseif(I('get.subject_id')>0 && I('get.class_id')>0){
  //  		$list = M('class_hour')->where(array('subject_id'=>I('get.subject_id')))->order('id asc')->select();
  //  }elseif(I('get.subject_id')==0 && I('get.class_id')>0){
		// $list = M('class_hour')->where(array('class_room_id'=>I('get.class_id')))->order('id asc')->select();
   }else{
   		$list = M('class_hour')->order('id asc')->select();
   }
    // echo '<pre>';
    // print_r($list);exit;
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
// dump($class_hour_tody);
foreach ($class_hour_tody as $key => $value) {
  // dump(count($class_hour_tody[$key]) );
  if ( count($class_hour_tody[$key]) == 2 ) { //每天2节课，规则调用
      //规则
      if($i!=''){
        $xls_title_num[$num[$i+1].'2'] = array($num[$i+1].'2:'.$num[count($class_hour_tody[$key])+$i].'2',$key);
     }else{
        // dump(count($class_hour_tody[$key]));exit();
        $xls_title_num['E2'] = array('E2:'.$num[count($class_hour_tody[$key])-1].'2',$key);
     }
        $i +=  $i == '' ? count($class_hour_tody[$key])-1 : count($class_hour_tody[$key]);
     //规则   
        $flag = true ;
  }elseif ( count($class_hour_tody[$key]) == 1 ) { //每天一节课 规则调用
     //规则
      if($i!=''){
        if ($flag == true) { //旗帜
          $xls_title_num[$num[$i+1].'2'] = array($num[$i+1].'2:'.$num[$i+1].'2',$key);
        }else{
         
        $xls_title_num[$num[$i].'2'] = array($num[$i].'2:'.$num[$i].'2',$key);
        }
     }else{
        // dump(count($class_hour_tody[$key]));exit();
        $xls_title_num['E2'] = array('E2:'.$num[count($class_hour_tody[$key])-1].'2',$key);
     }
        $i +=  $i == '' ? count($class_hour_tody[$key]) : count($class_hour_tody[$key]);
     //规则   
      
  }
  
}

  $z=-1;
  // dump($xls_title_num);




 
    
//    $ee =   array_search(substr(end($xls_title_num)[0], -2,1), $num);
// ;

//   exit();

foreach ($list as $k => $v) {
    $z += 1;
    $data_title[$num[$z].'3'] = array('',date('H:i',$v['start_time']).'-'.date('H:i',$v['end_time']));
}

     $endEX  = array_search(substr(end($xls_title_num)[0], -2,1), $num);//最终单元格
     $xls_title = array(
          'A2'=> array('A2:A3','序号'),
          'B2'=> array('B2:B3','学号'),
          'C2'=> array('C2:C3','班级'),
          'D2'=> array('D2:D3','姓名'),
           $num[$endEX+1].'2' =>array( $num[$endEX+1].'2:'.$num[$endEX+1].'3' ,'课时统计'),
           $num[$endEX+2].'2' =>array( $num[$endEX+2].'2:'.$num[$endEX+2].'3' ,'是否合格'),
         ); 

     // dump($xls_title);exit;
   
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

      $lastEX   = array(
            array('sum_subject_time'),
            array('status',''),
         
        );
      $xlsCell = array_merge_recursive($xlsCell,$sign_data,$lastEX);

      //print_r(array_flip($data_list_title));die;
      //print_r($xlsCell);die;
/*
 *
 */

$sql = 'select std.class_id , std.student_no , std.username ,sh.user_id ,

        GROUP_CONCAT(sh.class_hour_id) as class_hour_id,cs.status

        from  dk_sign_history as sh, dk_class_hour as ch, dk_subjects as sj  , dk_teachers as th ,dk_students as std ,dk_choice_subjects as cs,dk_class as c          

        '; 
 	if(I('get.subject_id')>0 && I('get.class_id')>0){
 		$sql.='where sh.user_id = std.id and c.id = std.class_id and sh.class_hour_id = ch.id and ch.subject_id = sj.id and ch.teacher_id = th.id and sh.sign_type=1 and ch.subject_id='.I('get.subject_id').' and c.id='.I('get.class_id').' and cs.user_id = sh.user_id and cs.subject_id = sj.id

        GROUP BY std.class_id , std.student_no , std.username ,sh.user_id';
 	}elseif(I('get.class_id')>0){
		$sql.='where sh.user_id = std.id and c.id = std.class_id and sh.class_hour_id = ch.id and ch.subject_id = sj.id and ch.teacher_id = th.id and sh.sign_type=1 and c.id='.I('get.class_id').' and cs.user_id = sh.user_id and cs.subject_id = sj.id

        GROUP BY std.class_id , std.student_no , std.username ,sh.user_id';
 	}elseif(I('get.subject_id')>0){
 		$sql.='where sh.user_id = std.id and c.id = std.class_id and sh.class_hour_id = ch.id and ch.subject_id = sj.id and ch.teacher_id = th.id and sh.sign_type=1 and ch.subject_id='.I('get.subject_id').' and cs.user_id = sh.user_id and cs.subject_id = sj.id

        GROUP BY std.class_id , std.student_no , std.username ,sh.user_id';
 	}else{
 		$sql.='where sh.user_id = std.id and c.id = std.class_idand sh.class_hour_id = ch.id and ch.subject_id = sj.id and ch.teacher_id = th.id and sh.sign_type=1 and cs.user_id = sh.user_id and cs.subject_id = sj.id

        GROUP BY std.class_id , std.student_no , std.username ,sh.user_id';
 	}
     $history_list = M()->query($sql);
       //dump($history_list);exit();
     // echo M()->getLastSql();die;
  foreach ($data_list_title as $key => $value) {
     $data_list_title[$key] = '';
  }
foreach ($history_list as $key => $value) {
     $data_list[$key] = array_merge_recursive($value,array_flip($data_list_title));
     $data_list[$key]['xh'] = $key;//序号
     $data_list[$key]['class_id'] = $value['class_id'] == '' ? '暂无数据' : M('Class')->where(array('id'=>$value['class_id']))->getField('class_name');
     $data_list[$key]['student_no'] = $value['student_no'];
     $data_list[$key]['username'] = $value['username'];  
     $data_list[$key]['status'] = $value['status'] == '1' ?'合格':'不合格';
}

// dump($data_list);exit();

foreach ($data_list as $key => $value){
  if($value['class_hour_id']!=''){
    if(strstr($value['class_hour_id'],",")){
      $ids = explode(',',$value['class_hour_id']);
      foreach ($ids as $k => $v){
        $star_time = M('sign_history')->where(array('class_hour_id'=>$ids[$k],'user_id'=>$value['user_id'],'sign_type'=>'1'))->find();
        
      

        $end_time = M('sign_history')->where(array('class_hour_id'=>$ids[$k],'user_id'=>$value['user_id'],'sign_type'=>'2'))->field("max(sign_time)")->find();//$end_time['max(sign_time)']
        
        $kkk = M('class_hour')->where(array('id'=>$ids[$k]))->getField('date');
        $kkk = $kkk.'-'.$ids[$k];

   

        $endtime = $end_time['max(sign_time)'] == '' ? '' : date('H:i',$end_time['max(sign_time)']);

     
        $star_time = $star_time['sign_time'] == '' ? '' : date('H:i',$star_time['sign_time']);
           if(strtotime($star_time)> strtotime($endtime)){
           $endtime = '';
        }
        $data_list[$key][$kkk] = $star_time.'-'.$endtime; 
        
        //统计学时
        $data_list[$key]['sum_subject_time']=  M('Choice_class_hour')->where("user_id ={$value['user_id']} and class_hour_id IN ({$value['class_hour_id']}) ")->sum('sum_subject_time');

      

        unset($star_time,$endtime);
      }
    }else{


        $star_time = M('sign_history')->where(array('class_hour_id'=>$value['class_hour_id'],'user_id'=>$value['user_id'],'sign_type'=>'1'))->find();

         // dump($star_time);
        
        $end_time = M('sign_history')->where(array('class_hour_id'=>$value['class_hour_id'],'user_id'=>$value['user_id'],'sign_type'=>'2',))->field("max(sign_time)")->find();//$end_time['max(sign_time)']
  

        $endtime = $end_time['max(sign_time)'] == '' ? '' : date('H:i',$end_time['max(sign_time)']);
        $kkk = M('class_hour')->where(array('id'=>$value['class_hour_id']))->getField('date');
        $kkk = $kkk.'-'.$value['class_hour_id'];
        //$array[$kkk] = $start_time['sign_time'].'-'.$end_time['max(sign_time)'];
        $star_time = $star_time['sign_time'] == '' ? '' : date('H:s',$star_time['sign_time']);

       if(strtotime($star_time)> strtotime($endtime)){
           $endtime = '';
        }
        $data_list[$key][$kkk] = $star_time.'-'.$endtime;

        //统计学时
        $data_list[$key]['sum_subject_time']=  M('Choice_class_hour')->where("user_id ={$value['user_id']} and class_hour_id IN ({$value['class_hour_id']}) ")->sum('sum_subject_time');
        unset($star_time,$endtime);
     }
   }


 }
         // exit();
         // dump($data_list);exit();
        $xlsName = '云师大考勤表';
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

      // $font=$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont();
      // $font->setSize('16');
      // $font->setBold(true);
        foreach ($cellName as $key => $val) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($val)->setWidth(16);//单元格设置宽度
        }


// dump($xls_title);exit();
/*若要标题这里打开 ↓ */
// dump($xls_title);exit();
        foreach ($xls_title as $key => $value) {
          if($value[0]!=''){

            $objPHPExcel->getActiveSheet(0)->mergeCells($value[0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($key, $value[1]); 
            $objPHPExcel->getActiveSheet()->getStyle($value[0])->getFont()->setSize(12);//字体大小
            $objPHPExcel->getActiveSheet()->getStyle($value[0])->getFont()->setBold(true);//字体加粗
              $objPHPExcel->getActiveSheet()->getStyle($value[0])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //居中
           
          }else{
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($key, $value[1]);
             $objPHPExcel->getActiveSheet()->getStyle($key)->getFont()->setSize(11);
            $objPHPExcel->getActiveSheet()->getStyle($key)->getFont()->setBold(true);
             $objPHPExcel->getActiveSheet()->getStyle($key)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //居中
          }   
        }


        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

       $objPHPExcel->getActiveSheet()->getStyle('A1:'.$cellName[$cellNum-1].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //居中
        // dump($cellName);exit();
        for($i=0;$i<$cellNum;$i++){              
        // dump($cellName[$i].'4') ; exit();                /*若要标题 ↓ +1*/
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'4', $expCellName[$i][1]);
           
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){                              /*若要标题 ↓ +1*/
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+4), $expTableData[$i][$expCellName[$j][0]]);
                 $objPHPExcel->getActiveSheet()->getStyle($cellName[$j].($i+4))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //居中
            }
        }
      

      //最后单元格设置  
          $objPHPExcel->getActiveSheet(0)->mergeCells('A'.($i+4).':'.$cellName[$cellNum-1].($i+5));//合并单元格
          $objPHPExcel->getActiveSheet()->getStyle('A'.($i+4).':'.$cellName[$cellNum-1].($i+5))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //居中
          $objPHPExcel->getActiveSheet()->getStyle('A'.($i+4).':'.$cellName[$cellNum-1].($i+5))->getFont()->setSize(11);
          $objPHPExcel->getActiveSheet()->getStyle('A'.($i+4).':'.$cellName[$cellNum-1].($i+5))->getFont()->setBold(true);
          
          
          
       
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($i+4), "助教:                       教务审核:");


        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}
?>