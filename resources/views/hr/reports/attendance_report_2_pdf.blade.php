<style type="text/css">
body { font-family: 'bangla', sans-serif;}
</style> 
@if(!empty($info->otEmpAttendance))
  <div class="col-sm-12">
      <h4 style="text-align: center; text-decoration: underline;" >Attendance Summary Report of {{ $info->unit_name }}</h4>
      <h4 style="text-align: center; text-decoration: underline;" >Date:  {{ date('d-M-Y', strtotime($info->date)) }}</h4>
      <p>Run Time:&nbsp;<?php echo date('l\&\\n\b\s\p\;F \&\\n\b\s\p\;d \&\\n\b\s\p\;Y \&\\n\b\s\p\;h:m a'); ?></p>
      <hr>
  </div>
  <div class="col-sm-12">
      <table width="100%" >
          <tr>
              <td width="55%">
                  <table width="100%" cellpadding="0" cellspacing="0">
                      <tr>
                          <th width="55%" style="text-align: left; padding: 5px;"> Summary : </th>
                          <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Employee</td>
                          <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Present</td>
                          <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Absent</td>
                      </tr>
                  </table>
                  <table width="100%" border="1" cellpadding="0" cellspacing="0">

                      <tr>
                          <th width="55%" style="text-align: left; padding: 5px;">NON OT Employee : </th>
                          <td width="15%" style="text-align: center; padding: 5px;" id="non_ot_grand_e"></td>
                          <td width="15%" style="text-align: center; padding: 5px;" id="non_ot_grand_p"></td>
                          <td width="15%" style="text-align: center; padding: 5px;" id="non_ot_grand_a"></td>
                      </tr>
                      <tr>
                          <th width="55%" style="text-align: left; padding: 5px;">OT Employee : </th>
                          <td width="15%" style="text-align: center; padding: 5px;" id="ot_grand_e">d</td>
                          <td width="15%" style="text-align: center; padding: 5px;" id="ot_grand_p">d</td>
                          <td width="15%" style="text-align: center; padding: 5px;" id="ot_grand_a">d</td>
                      </tr>
                      <tr>
                          <th bgcolor="#C2C2C2" width="55%" style="text-align: left; padding: 5px;">Total:</th>
                          <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;" id="sum_e" ></td>
                          <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;" id="sum_p" ></td>
                          <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;" id="sum_a" ></td>
                      </tr>
                  </table>
              </td>
              <td width="4%"></td>
              <td width="41%">
                  {{-- <table>
                      <tr>
                         <td style="text-align: right; padding: 5px;">MMR</td> 
                         <td>&nbsp;&nbsp;=&nbsp;&nbsp;</td>
                         <td>
                             <p style="border-bottom: 1px solid; padding-bottom: 0px; margin-bottom: 0px; text-align: center;">P. NON OT + P. OT Holder</p>
                             <p style="margin-top: 0px; padding-top: 0px; text-align: center;"> Sewing Opr + Fin Opr</p>
                         </td> 
                      </tr>
                      <tr>
                         <td></td> 
                         <td>&nbsp;&nbsp;=&nbsp;&nbsp;</td> 
                          <td>
                              <p style="border-bottom: 1px solid; padding-bottom: 0px; margin-bottom: 0px; text-align: center;">
                                  <span id="p_ot_n">P. NON OT</span> + <font id="p_ot">P. OT Holder</font>

                                <!--   <input type="text" id="p_ot_n2" name=""> -->
                              </p>
                             <p style="margin-top: 0px; padding-top: 0px; text-align: center;">
                                 <font id="sw_opr">Sewing Opr</font> + <font id="fin_opr">Fin Opr</font>
                          </p>
                          </td>
                      </tr>
                      <tr> 
                         <td></td>
                         <td>&nbsp;&nbsp;=&nbsp;&nbsp;</td>
                          <td>
                              <font id="mmr_result">0</font>
                          </td>
                      </tr>
                  </table>
              </td> --}}
          </tr>
      </table>
      <h3 style="margin-top: 20px; margin-bottom: 20px;">OT Holder List:</h3>
      <div class="col-xs-12 ot_holder_list" style="margin-bottom: 20px; padding-left: 0px;">
          <table cellpadding="0" cellspacing="0" border="1" width="100%">
              <thead>
                  <tr class="alert-info tbl-header">
                      <th style="text-align: center; padding: 8px;">Sl</th>
                      <th style="text-align: center; padding: 8px;">Area</th>
                      <th style="text-align: center; padding: 8px;">Section</th>
                      <th style="text-align: center; padding: 8px;">Sub Section</th>
                      <th width="7%" style="text-align: center; padding: 8px;">Enroll</th>
                      <th width="7%" style="text-align: center; padding: 8px;">Present</th>
                      <th width="7%" style="text-align: center; padding: 8px;">Absent</th>
                      <th width="7%" style="text-align: center; padding: 8px;">Leave</th>
                      <th width="7%" style="text-align: center; padding: 8px;">Absent%</th>
                  </tr>
              </thead>
              <tbody> 
                  @php
                      $areaKey = 1;
                      $grand_eo = 0;
                      $grand_p = 0;
                      $grand_a = 0;
                      $grand_l = 0;
                      $areaList = [];
                      $sectionList = [];
                  @endphp
                  @foreach($info->otEmpAttendance as $areaName => $otEmpAttendance)
                      @foreach($otEmpAttendance as $sectionName => $sections)
                          @foreach($sections as $subsectionName => $subsection)
                                  <tr>
                                      <td>{{$areaKey++}}</td>
                                      @php
                                          if(!in_array($areaName, $areaList)){
                                          $areaList[] = $areaName;
                                      @endphp
                                      <td >{{$areaName}}</td>
                                      @php
                                          } else {
                                              echo '<td></td>';
                                          }
                                      @endphp

                                      @php
                                          if(!in_array($areaName.'-'.$sectionName, $sectionList)){
                                          $sectionList[] = $areaName.'-'.$sectionName;
                                      @endphp
                                          <td rowspan="{{count($info->otEmpAttendance[$areaName][$sectionName])}}">{{$sectionName}}</td>
                                      @php
                                          }
                                      @endphp
                                      <td>{{$subsectionName}}</td>
                                      <td>
                                          @php
                                              $single_ot = (isset($subsection['present'])?count($subsection['present']):0)+(isset($subsection['absent'])?count($subsection['absent']):0)+(isset($subsection['leave'])?count($subsection['leave']):0);
                                              $grand_eo += $single_ot;
                                              echo $single_ot;
                                          @endphp
                                      </td>
                                      <td>
                                          <?php 
                                              echo isset($subsection['present'])?count($subsection['present']):''; 
                                              $grand_p += isset($subsection['present'])?count($subsection['present']):0;
                                          ?>                                                            
                                      </td>
                                      <td>
                                          <?php 
                                              echo isset($subsection['absent'])?count($subsection['absent']):''; 
                                              $grand_a += isset($subsection['absent'])?count($subsection['absent']):0;
                                          ?>
                                      </td>
                                      <td>
                                          <?php 
                                              echo isset($subsection['leave'])?count($subsection['leave']):''; 
                                              $grand_l += isset($subsection['leave'])?count($subsection['leave']):0;
                                          ?>
                                      </td>
                                      @php
                                          $singlePercent = '';
                                          if($single_ot != 0 && (isset($subsection['absent'])?count($subsection['absent']):0) != 0) {
                                              $singlePercent = round((count($subsection['absent'])*100)/$single_ot);
                                          }
                                      @endphp
                                      <td style="@php 
                                          if((int)$singlePercent != 0) {
                                              if($singlePercent > 50) {
                                                  echo "background-color: #dc3545; color: #000;";
                                              } else {
                                                  echo "background-color: #ffc107; color: #000;";
                                              }
                                          } else {
                                              if((isset($subsection['present'])?count($subsection['present']):0) != 0) {
                                                  if($single_ot == count($subsection['present'])) {
                                                      echo "background-color: #28a745; color: #000;";
                                                  }
                                              }
                                          }
                                      @endphp">
                                          {{(int)$singlePercent!=0?$singlePercent.'%':'0%'}}
                                      </td>
                                  </tr>
                          @endforeach
                      @endforeach
                  @endforeach
                  {{-- //footer row here --}}
                  <tr class="label-info grand_total">
                      <td colspan="4" style="padding:5px;" >Grand Total: </td>
                      <td style='text-align: right; padding: 5px;' id="grand_e"> {{ $grand_eo }} </td>
                      <td style='text-align: right; padding: 5px;' id="grand_p">{{ $grand_p }}</td>
                      <td style='text-align: right; padding: 5px;' id="grand_a">{{ $grand_a }}</td>
                      <td style='text-align: right; padding: 5px;' id="grand_l">{{ $grand_l }}</td>
                      <td style='text-align: right; padding: 5px;'>
                          @php
                              if($grand_p!=0 && $grand_a != 0) {
                                  echo round(($grand_a*100)/$grand_eo).'%';
                              }
                          @endphp
                      </td>
                      {{-- <td style='text-align: right; padding: 5px;'>{{ round((isset($grand_a)??1*100)/isset($grand_e)??2) }}%</td> --}}
                  </tr>
              </tbody>
          </table>
      </div>
      <!-- Non OT Holder -->

      <h3 style="page-break-before: always; margin-top: 20px; margin-bottom: 20px;">NON OT Holder List:</h3>
      <div class="col-xs-12 non_ot_holder_list" style="padding-left: 0px;">
          <table cellpadding="0" cellspacing="0" border="1" width="100%">
              <thead>
                  <tr class="alert-info tbl-header">
                      <th style="text-align: center; padding: 10px;">Sl</th>
                      <th style="text-align: center; padding: 10px;">Area</th>
                      <th style="text-align: center; padding: 10px;">Section</th>
                      <th style="text-align: center; padding: 10px;">Sub Section</th>
                      <th width="7%" style="text-align: center; padding: 10px;">Enroll</th>
                      <th width="7%" style="text-align: center; padding: 10px;">Present</th>
                      <th width="7%" style="text-align: center; padding: 10px;">Absent</th>
                      <th width="7%" style="text-align: center; padding: 10px;">Leave</th>
                      <th width="7%" style="text-align: center; padding: 10px;">Absent%</th>
                  </tr>
              </thead>
              <tbody>
                  @php
                      $areaKey = 1;
                      $grand_en = 0;
                      $grand_p = 0;
                      $grand_a = 0;
                      $grand_l = 0;
                      $areaList = [];
                      $sectionList = [];
                  @endphp
                  @foreach($info->nonOtEmpAttendance as $areaName => $nonOtEmpAttendance)
                      @foreach($nonOtEmpAttendance as $sectionName => $sections)
                          @foreach($sections as $subsectionName => $subsection)
                                  <tr>
                                      <td>{{$areaKey++}}</td>
                                      @php
                                          if(!in_array($areaName, $areaList)){
                                          $areaList[] = $areaName;
                                      @endphp
                                      <td >{{$areaName}}</td>
                                      @php
                                          } else {
                                              echo '<td></td>';
                                          }
                                      @endphp

                                      @php
                                          if(!in_array($areaName.'-'.$sectionName, $sectionList)){
                                          $sectionList[] = $areaName.'-'.$sectionName;
                                      @endphp
                                          <td rowspan="{{count($info->nonOtEmpAttendance[$areaName][$sectionName])}}">{{$sectionName}}</td>
                                      @php
                                          }
                                      @endphp
                                      <td>{{$subsectionName}}</td>
                                      <td>
                                          @php
                                              $single_n_ot = (isset($subsection['present'])?count($subsection['present']):0)+(isset($subsection['absent'])?count($subsection['absent']):0)+(isset($subsection['leave'])?count($subsection['leave']):0);
                                              $grand_en += $single_n_ot;
                                              echo $single_n_ot;
                                          @endphp
                                      </td>
                                      <td>
                                          <?php 
                                              echo isset($subsection['present'])?count($subsection['present']):''; 
                                              $grand_p += isset($subsection['present'])?count($subsection['present']):0;
                                          ?>
                                      </td>
                                      <td>
                                          <?php 
                                              echo isset($subsection['absent'])?count($subsection['absent']):''; 
                                              $grand_a += isset($subsection['absent'])?count($subsection['absent']):0;
                                          ?>
                                      </td>
                                      <td>
                                          <?php 
                                              echo isset($subsection['leave'])?count($subsection['leave']):''; 
                                              $grand_l += isset($subsection['leave'])?count($subsection['leave']):0;
                                          ?>
                                      </td>
                                      @php
                                          $singlePercent = '';
                                          if($single_n_ot != 0 && (isset($subsection['absent'])?count($subsection['absent']):0) != 0) {
                                              $singlePercent = round((count($subsection['absent'])*100)/$single_n_ot);
                                          }
                                      @endphp
                                      <td style="@php 
                                          if((int)$singlePercent != 0) {
                                              if($singlePercent > 50) {
                                                  echo "background-color: #dc3545; color: #000;";
                                              } else {
                                                  echo "background-color: #ffc107; color: #000;";
                                              }
                                          } else {
                                              if((isset($subsection['present'])?count($subsection['present']):0) != 0) {
                                                  if($single_n_ot == count($subsection['present'])) {
                                                      echo "background-color: #28a745; color: #000;";
                                                  }
                                              }
                                          }
                                      @endphp">
                                          {{(int)$singlePercent!=0?$singlePercent.'%':'0%'}}
                                      </td>
                                  </tr>
                          @endforeach
                      @endforeach
                  @endforeach
                  {{-- //footer row here --}}
                  <tr class="label-info grand_total">
                      <td colspan="4" style="padding:5px;" >Grand Total: </td>
                      <td style='text-align: right; padding: 5px;' id="grand_e_n"> {{ $grand_en }} </td>
                      <td style='text-align: right; padding: 5px;' id="grand_p_n">{{ $grand_p }}</td>
                      <td style='text-align: right; padding: 5px;' id="grand_a_n">{{ $grand_a }}</td>
                      <td style='text-align: right; padding: 5px;' id="grand_l_n">{{ $grand_l }}</td>
                      <td style='text-align: right; padding: 5px;'>
                          @php
                              if($grand_p != 0 && $grand_a != 0) {
                                  echo round(($grand_a*100)/$grand_en).'%';
                              }
                          @endphp
                      </td>
                  </tr>
              </tbody>
          </table>
      </div>
  </div>
@endif
 

<script src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>
<script type="text/javascript">
    $(window).load(function() {
      //ot summary
      var ot_e=0;
      var ot_p=0;
      var ot_a=0;
      var ot_e=parseInt($("#grand_e").text());
      var ot_p=parseInt($("#grand_p").text());
      var ot_a=parseInt($("#grand_a").text());
      $("#ot_grand_e").text(ot_e);
      $("#ot_grand_p").text(ot_p);
      $("#ot_grand_a").text(ot_a);

      //non ot summary
      var ot_e_n=0;
      var ot_p_n=0;
      var ot_a_n=0;
      var ot_e_n=parseInt($("#grand_e_n").text());
      var ot_p_n=parseInt($("#grand_p_n").text());
      var ot_a_n=parseInt($("#grand_a_n").text());
      $("#non_ot_grand_e").text(ot_e_n);
      $("#non_ot_grand_p").text(ot_p_n);
      $("#non_ot_grand_a").text(ot_a_n);
      //total summary
      var sum_e= 0;
      var sum_p= 0;
      var sum_a= 0;
      var sum_e= ot_e+ot_e_n;
      var sum_p= ot_p+ot_p_n;
      var sum_a= ot_a+ot_a_n;

      $("#sum_e").text(sum_e);
      $("#sum_p").text(sum_p);
      $("#sum_a").text(sum_a);

      //MMR
      $("#p_ot").text(ot_p);
      $("#p_ot_n").text(ot_p_n);

    });
</script>