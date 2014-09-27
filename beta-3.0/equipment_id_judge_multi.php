<link href="css/index2.css" rel="stylesheet" type="text/css"><!-- x -->
<link href="css/reset.css" rel="stylesheet" type="text/css"><!-- x -->
<link href="css/laser.css" rel="stylesheet" type="text/css"><!-- x -->
<style>
/**
 * Emulating <blink> using WebKit CSS3 animation        
 *   This code is part of John Smith's blog
 *
 * Copyright 2010 by John Smith, All Rights Reserved
 *
 * @link   http://www.john-smith.me/emulating--lt-blink-gt--using-webkit-css3-animation
 * @date   18th October 2010 at 11:01 p.m.
 * @author John Smith
 */
@-webkit-keyframes blinker { from {opacity:1.0;} to {opacity:0.0;} }
        @keyframes blinker { from {opacity:1.0;} to {opacity:0.0;} }

.blink {
   text-decoration:blink;

  -webkit-animation-name:blinker;
          animation-name:blinker;  
  -webkit-animation-iteration-count:infinite;  
          animation-iteration-count:infinite;  
  -webkit-animation-timing-function:cubic-bezier(1.0,0,0,1.0);
          animation-timing-function:cubic-bezier(1.0,0,0,1.0);
  -webkit-animation-duration:1.5s; 
          animation-duration:1.5s; 
}
</style>
<?php
    session_start();
    ob_start();
    require './connect_database.php';
?>
<div>
<?php
    //global variables for line 42
    $_SESSION['one'] = 1;
    //accept the equipment ID from the front side
    $equipment_id_search = $_POST["equipment_id_search_1"];
    //get the latest record in "trade" by equipment ID
    $query_tra = "SELECT * FROM  `trade` WHERE `e_id`='$equipment_id_search' ORDER BY `sequence` DESC LIMIT 1";
    if($query_tra_run =  mysql_query($query_tra)){
        //make sure the grabed data is not null
        if(mysql_num_rows($query_tra_run) != NULL){
            while($query_tra_row = mysql_fetch_assoc($query_tra_run)){
                //get the continueborrow times according to the latest record grabed above
                $_SESSION['delaytimes'] = $query_tra_row[ 'delaytimes' ] ;
            }
        }
        else{
            echo mysql_error();
        }
    }
    //grab equipment data by equipment ID
    $query = "SELECT * FROM equipment WHERE `id`='$equipment_id_search'";
    if ($_POST) {
        if($query_run =  mysql_query($query)){
            //if the grabed data by equipment ID from front side is null, send it to equipment_insert_1 to establish the data 
            if (mysql_num_rows($query_run) == NULL) {
                    $_SESSION['new_equid_id'] = $equipment_id_search;
                    header('Location: ./equipment_insert_1.php');
                }
            //first establish global variables(id,name,class...), and the value sent to global variables are grabed from database
            elseif(mysql_num_rows($query_run) != NULL){
                while ($query_row = mysql_fetch_assoc($query_run)) { /*Show equipment information*/
                    $_SESSION['e_id'] = $query_row[ 'id' ] ;
                    $_SESSION['e_name'] = $query_row[ 'name' ];
                    $_SESSION['e_class'] = $query_row[ 'class' ];
                    $_SESSION['e_status'] =  $query_row[ 'status' ];
                    $_SESSION['e_owner'] =  $query_row[ 'current_owner' ];
                    $_SESSION['e_addition'] = $query_row[ 'addition' ];
                    
                    //establish global variables to show the times successfully grab data from database
                    @$_SESSION['times'] = @$_SESSION['times']+$_SESSION['one'];
                    $GLOBALS['id_search'] = $_POST["equipment_id_search_1"];
                    //output echoo's function as a form(equipment information and input fill)
                    echoo();
                }
                /*限制次數
                if($_SESSION['times']-$_SESSION['counts']>=5){ 
                    echo '<SCRIPT>';
                    echo 'alert(\'單次項目數量上限為五筆!\');';
                    echo '</SCRIPT>';
                }*/
            }
            else{ 
                echo mysql_error();
            }
        }
    }
    function echoo(){
        $_SESSION['$subcategory']=null;
        //establish variables which will be used later, and these variables are grabed from global variables 
        $fill_class = 'fill_class'.$_SESSION['times'];
        $over_check_day = 'over_check_day'.$_SESSION['times'];
        $divv = 'divv'.$_SESSION['times'];
        $inputt = 'inpot'.$_SESSION['times'];
        $equ_id = 'equ_id'.$_SESSION['times'];
        $usr_id = 'usr_id'.$_SESSION['times'];
        $tra_handler = 'tra_handler';
        $equ_cls = 'equ_cls'.$_SESSION['times'];
        $equ_add = 'equ_add'.$_SESSION['times'];
        $equ_add_clas = 'equ_add_clas'.$_SESSION['times'];
        $equ_stats = 'equ_stats'.$_SESSION['times'];
        $bck_chs = 'bck_chs'.$_SESSION['times'];
        $informationn = '物品名稱 : '.$_SESSION['e_name'].'<br>物品類別 : '.$_SESSION['e_class'];
            //judge the current equipment's status whether it's available to be borrowed
            if($_SESSION['e_status']=='return'){
                //judge the login user ID whether it's in black list
                if(@$_SESSION['blacklisted']=='n'){
                    //grab the data from "setting" by current equipment class(type)
                    $sql = 'SELECT * FROM `setting` WHERE `setting`.`e_class` = \''.$_SESSION['e_class'].'\'';
                        //grab accessories from "setting" by current equipment class(type)
                        if($query_run =  mysql_query($sql)){
                            while($query_row = mysql_fetch_assoc($query_run)){
                                if($query_row[ 'accessories' ]!=NUll){
                                    $accessories_arr = explode(";", $query_row[ 'accessories' ]);
                                    $_SESSION['$accessories']="";
                                    foreach ($accessories_arr as $value) {   
                                        if($_SESSION['$accessories']!=""){
                                            $_SESSION['$accessories'] =  $_SESSION['$accessories']."<input type=\"checkbox\" value=\"".$value."\"><label>".$value."</label><br>";    
                                        }else{
                                             $_SESSION['$accessories'] = "<input type=\"checkbox\" value=\"".$value."\"><label>".$value."</label><br>"; 
                                        }
                                    }
                                }
                                
                                if($query_row[ 'enable_overday' ]==1){
                                    $input_enable_overday = '<input name="'.$over_check_day.'" type="checkbox" value="ok"><label style="color:#a14792">隔天借用</label>';
                                }
                                if($query_row[ 'subcategory' ]!=NULL){
                                    $subcategory_arr = explode(";", $query_row[ 'subcategory' ]);
                                    $_SESSION['$subcategory']="";
                                    // for ($subcategory_arr as $value){
                                    for ($i=0;$i< sizeof($subcategory_arr)-1;$i++) {   
                                        if($_SESSION['$subcategory']!=""){
                                            $_SESSION['$subcategory'] =  $_SESSION['$subcategory']."<div><label class=\"label_subid\" name=\"lbl_".$subcategory_arr[$i]."\" style=\"color:#888888;border-bottom-style: solid;border-color: #444444;\" value=\"".$subcategory_arr[$i]."\">子類別".$subcategory_arr[$i]."尚無項目</label><img class=\"blink\" style=\"height:23px;vertical-align:middle\" src=\"img/question.png\" name=\"img_".$subcategory_arr[$i]."\"><input type=\"button\" value=\"clear\" onclick=\"this.parentNode.getElementsByTagName('label')[0].innerText='子類別".$subcategory_arr[$i]."尚無項目'\"></div>";    
                                        }else{
                                            $_SESSION['$subcategory'] = "<div><label class=\"label_subid\" name=\"lbl_".$subcategory_arr[$i]."\" style=\"color:#888888;border-bottom-style: solid;border-color: #444444;\" value=\"".$subcategory_arr[$i]."\">子類別".$subcategory_arr[$i]."尚無項目</label><img class=\"blink\" style=\"height:23px;vertical-align:middle\" src=\"img/question.png\" name=\"img_".$subcategory_arr[$i]."\"></div>"; 
                                        }
                                    }
                                    $_SESSION['$subcategory'] = '<input class="i_subcategory" type="hidden" value="'.$query_row[ 'subcategory' ].'">'.$_SESSION['$subcategory'];
                                }

                            }
                        }else{
                            echo mysql_error();
                        }


                    //set a global variable including equipment information, input fill and buttons 
                    @$_SESSION[$divv] = '<div id="'.$divv.'" style="display:inline-block"><input style="float:right" type="button" value="移除" onClick="removed('.$_SESSION['times'].');"><br>'.$informationn
                        .'<br><input type="hidden" name="'.$divv.'" value="'.$divv.'"><input type="hidden" name="'
                        .$inputt.'" value="'.$informationn.'"><input type="hidden" class="returned" value="'.$_SESSION['e_id'].'"><input type="hidden" id="equ_val'
                        .$_SESSION['e_id'].'" name="'.$equ_id.'" value="'.$_SESSION['e_id'].'"><input type="hidden" name="'
                        .$usr_id.'" value="'.$_SESSION['id'].'"><input type="hidden" name="'
                        .$equ_stats.'" value="'.$_SESSION['e_status'].'"><input type="hidden" name="'
                        .$equ_cls.'" value="'.$_SESSION['e_class'].'">'.$_SESSION['$accessories'].$input_enable_overday.'
                        <br>'.$_SESSION['$subcategory'].'<br><label>使用課程: </label><input id="'.$fill_class.'" type="text" name="'.$equ_add_clas.'"><br><label>其他備註: </label>
                        <input name="e_addition_note'.$_SESSION['times'].'" type="text"><br><br><hr></div>';
                    //detect error div if there's any blank
                    echo '<div id="textDiv"></div>';
                    //establish the form include equipment information
                    echo '<form id="childform" action="./equipment_lend_return_multi.php" method="post" onsubmit="return ray.ajax('.$_SESSION['times'].')">';
                    //output all the div which have been inputted 
                    for($x=0;$x<$_SESSION['times'];$x++){
                        $hi=$x+1;
                        echo @$_SESSION['divv'.$hi];
                    }
                    //if there's still divs left, show the information below(assistent ID, submit button)
                    if($_SESSION['times']-@$_SESSION['counts']>0){
                        echo '<div id="adot">';
                        echo '<br>';
                        echo '<input type="text" placeholder="經辦助理ID" style="ime-mode: disabled;float:left" id="assname" name="tra_handler_test" style="float:left" onkeyup="showHint(this.value)">';
                        echo '<input type="hidden" id = "assname1" name="tra_handler" style="float:left">';
                        echo '<div id="handler_name" style="float:left;padding-left: 5px;"></div>';
                        echo '<br><br>';
                        echo "<input id=\"submit0\" type=\"submit\" value=\" \" onClick=\"CKAddGust3(".$_SESSION['times'].")\">";
                        echo '</div>';
                    }
                    echo '</form>';
                }
                //if the login user ID was judged as blacklist, then show this alert window
                else if(@$_SESSION['blacklisted'] == 'y'){
                    echo "<SCRIPT Language=javascript>";
                    echo "window.alert('您目前被列管中,無法借用任何物品!')";
                    echo "</SCRIPT>";
                }
            }
            //judge the equipment's status whether it can be borrowed
            elseif($_SESSION['e_status']=='borrowed'){
                //grab the data from "setting" by current equipment class(type)
                $sql = 'SELECT * FROM `setting` WHERE `setting`.`e_class` = \''.@$_SESSION['e_class'].'\'';
                if($query_run =  mysql_query($sql)){
                    //grab continue_deadline_gap from "setting" by current equipment class(type)
                    while($query_row = mysql_fetch_assoc($query_run)){
                        $_SESSION['continue_borrow_interval'] = $query_row[ 'continue_deadline_gap' ];
                    }
                }else{
                    echo mysql_error();
                }
                //judge the user ID that currently borrow equipment is the login user ID or not
                if($_SESSION['e_owner']==@$_SESSION['id']){
                    //if this equipment is borrowed by this login user ID, judge how many days can be continueborrowed
                    if(@$_SESSION['continue_borrow_interval']>0){
                        //if this equipment is borrowed by this login user ID, judge if it had been continueborrowed
                        if($_SESSION['delaytimes']=='0'){
                            $kind_equ_b = '<input type="radio" name="'
                            .$bck_chs.'" value="continue_borrow">續借<input type="radio" name="'
                            .$bck_chs.'" value="just_return" checked>歸還  ';
                        }
                        elseif ($_SESSION['delaytimes']!='0') {
                            $kind_equ_b = '<input type="radio" name="'
                            .$bck_chs.'" value="continue_borrow" disabled><font color="gray">續借</font><input type="radio" name="'
                            .$bck_chs.'" value="just_return" checked><font color="gray">歸還(已續借過一次)</font>  ';
                        }
                    }
                    //if continueborrow days is 0, show the just_return button
                    else if(@$_SESSION['continue_borrow_interval']==0){
                        $kind_equ_b = '<input type="radio" name="'
                        .$bck_chs.'" value="continue_borrow" disabled><font color="gray">續借</font><input type="radio" name="'
                        .$bck_chs.'" value="just_return" checked><font color="gray">歸還(此類別不得續借)</font>  ';
                    }
                    //set a global variable including equipment information, input fill and buttons 
                    @$_SESSION[$divv] = '<div id="'.$divv.'">'.$informationn.'<br>this is   '.$_SESSION['times']
                        .' times (over 5 times should reload!)<input type="hidden" name="'.$divv.'" value="'.$divv.'"><input type="hidden" name="'
                        .$inputt.'" value="'.$informationn.'"><input type="hidden" class="borrowed" value="'.$_SESSION['e_id'].'"><input type="hidden" id="equ_val'
                        .$_SESSION['e_id'].'" name="'.$equ_id.'" value="'.$_SESSION['e_id'].'"><input type="hidden" name="'
                        .$usr_id.'" value="'.$_SESSION['id'].'"><br>'.$kind_equ_b.'<input type="hidden" name="'
                        .$equ_stats.'" value="'.$_SESSION['e_status'].'"><input type="hidden" name="'
                        .$equ_cls.'" value="'.$_SESSION['e_class'].'"><input type="hidden" id="'
                        .$bck_chs.'" value="'.$_SESSION['e_id'].'"><input type="button" value="移除" onClick="removed('.$_SESSION['times'].');"<br><br><hr></div>';
                    //detect error div if there's any blank
                    echo '<div id="textDiv"></div>';
                    //establish the form include equipment information
                    echo '<form id="childform" action="./equipment_lend_return_multi.php" method="post" onsubmit="return ray.ajax('.$_SESSION['times'].')">';
                    //output all the div which have been inputted
                    for($x=0;$x<$_SESSION['times'];$x++){
                        $hi=$x+1;
                        echo @$_SESSION['divv'.$hi];
                    }
                    //if there's still divs left, show the information below(assistent ID, submit button)
                    if($_SESSION['times']-@$_SESSION['counts']>0){
                        echo '<div id="adot">';
                        echo '<br>';
                        echo '<input type="text" placeholder="經辦助理ID" style="ime-mode: disabled;float:left" id="assname" name="tra_handler_test" style="float:left" onkeyup="showHint(this.value)">';
                        echo '<input type="hidden" id = "assname1" name="tra_handler" style="float:left">';
                        echo '<div id="handler_name" style="float:left;padding-left: 5px;"></div>';
                        echo '<br><br>';
                        echo "<input id=\"submit1\" type=\"submit\" value=\" \" onClick=\"CKAddGust3(".$_SESSION['times'].")\">";
                        echo '</div>';
                    }
                    echo '</form>';
                }
                //if the equipment is borrowed, and the user ID is different from login user ID, shoe the alert window
                elseif ($_SESSION['e_owner']!=@$_SESSION['id']) {
                    echo "<SCRIPT Language=javascript>";
                    echo "window.alert('已被別人借走囉!')";
                    echo "</SCRIPT>";
                    //detect error div if there's any blank
                    echo '<div id="textDiv"></div>';
                    //establish the form include equipment information
                    echo '<form id="childform" action="./equipment_lend_return_multi.php" method="post" onsubmit="return ray.ajax('.$_SESSION['times'].')">';
                    //output all the div which have been inputted
                    for($x=0;$x<$_SESSION['times'];$x++){
                        $hi=$x+1;
                        echo @$_SESSION['divv'.$hi];
                    }
                    //if there's still divs left, show the information below(assistent ID, submit button)
                    if(checkifanysessionexist()=='y'){ 
                        echo '<div id="adot">';
                        echo '<br>';
                        echo '<input type="text" placeholder="經辦助理ID" style="ime-mode: disabled;float:left" id="assname" name="tra_handler_test" style="float:left" onkeyup="showHint(this.value)">';
                        echo '<input type="hidden" id = "assname1" name="tra_handler" style="float:left">';
                        echo '<div id="handler_name" style="float:left;padding-left: 5px;"></div>';
                        echo '<br><br>';
                        echo "<input id=\"submit2\" type=\"submit\" value=\" \" onClick=\"CKAddGust3(".$_SESSION['times'].")\">";
                        echo '</div>';
                    }
                    echo '</form>';
                }
            }
            //judge the equipment's status is neither borrowed nor returned
            else{
                //judge if the login user ID is the blacklist or not 
                if(@$_SESSION['blacklisted']=='n'){
                    //grab the data from "setting" by current equipment class(type)
                    $sql = 'SELECT * FROM `setting` WHERE `setting`.`e_class` = \''.$_SESSION['e_class'].'\'';
                        //grab accessories from "setting" by current equipment class(type)
                        if($query_run =  mysql_query($sql)){
                            while($query_row = mysql_fetch_assoc($query_run)){
                                $_SESSION['$accessories'] = $query_row[ 'accessories' ];
                                if($query_row[ 'enable_overday' ]==1){
                                    $input_enable_overday = '<input name="'.$over_check_day.'" type="checkbox" value="ok"><label style="color:#a14792">隔天借用</label>';
                                }
                            }
                        }else{
                            echo mysql_error();
                        }

                    $spec_item_func = 'bck_chs'.$_SESSION['times'];

                    //set a global variable including equipment information, input fill and buttons 
                    @$_SESSION[$divv] = '<div id="'.$divv.'" style="display:inline-block"><input style="float:right" type="button" value="移除" onClick="removed('.$_SESSION['times'].');"><br>'.$informationn.
                        '
                        <input type="hidden" name="'.$divv.'" value="'.$divv.'">
                        <input type="hidden" name="'.$inputt.'" value="'.$informationn.'">
                        <input type="hidden" id="decide_end_menu'.$_SESSION['times'].'" class="returned" value="'.$_SESSION['e_id'].'">
                        <input type="hidden" id="equ_val'.$_SESSION['e_id'].'" name="'.$equ_id.'" value="'.$_SESSION['e_id'].'">
                        <input type="hidden" id="equ_multi'.$_SESSION['e_id'].'" name="'.$equ_id.'" value="'.$_SESSION['e_id'].'">
                        <input type="hidden" name="'.$usr_id.'" value="'.$_SESSION['id'].'">
                        <br><hr>
                        <input type="radio" id="spec_item_func_lend'.$_SESSION['times'].'" name="'.$spec_item_func.'" value="just_lend" onClick="insert_class(\''.$_SESSION['times'].'\');" checked><label>借用</label>
                        <input type="radio" id="spec_item_func_return'.$_SESSION['times'].'" name="'.$spec_item_func.'" value="just_return" onClick="insert_class(\''.$_SESSION['times'].'\');"><label>歸還</label>
                        <input type="radio" id="spec_item_func_continue_borrow'.$_SESSION['times'].'" name="'.$spec_item_func.'" value="continue_borrow" onClick="insert_class(\''.$_SESSION['times'].'\');"><label>續借</label>
                        <br><hr><b
                        <input type="hidden" name="'.$equ_stats.'" value="'.$_SESSION['e_status'].'">
                        <input type="hidden" name="'.$equ_cls.'" value="'.$_SESSION['e_class'].'">'.$_SESSION['$accessories'].'
                        <input type="hidden" id="'.$spec_item_func.'" value="'.$_SESSION['e_id'].'">
                        <div id = "galaxsis'.$_SESSION['times'].'">
                            '.$input_enable_overday.'
                            <br>
                            <label>使用課程: </label><input id="'.$fill_class.'" type="text" name="'.$equ_add_clas.'">
                            <br>
                            <label>其他備註: </label><input name="e_addition_note'.$_SESSION['times'].'" type="text">
                            <br><br><hr>
                        </div>
                        </div>';
                    //detect error div if there's any blank
                    echo '<div id="textDiv"></div>';
                    //establish the form include equipment information
                    echo '<form id="childform" action="./equipment_lend_return_multi.php" method="post" onsubmit="return ray.ajax('.$_SESSION['times'].')">';
                    //output all the div which have been inputted
                    for($x=0;$x<$_SESSION['times'];$x++){
                        $hi=$x+1;
                        echo @$_SESSION['divv'.$hi];
                    }
                    //if there's still divs left, show the information below(assistent ID, submit button)
                    if($_SESSION['times']-@$_SESSION['counts']>0){
                        echo '<div id="adot">';
                        echo '<br>';
                        echo '<input type="text" placeholder="經辦助理ID" style="ime-mode: disabled;float:left" id="assname" name="tra_handler_test" style="float:left" onkeyup="showHint(this.value)">';
                        echo '<input type="hidden" id = "assname1" name="tra_handler" style="float:left">';
                        echo '<div id="handler_name" style="float:left;padding-left: 5px;"></div>';
                        echo '<br><br>';
                        echo "<input id=\"submit3\" type=\"submit\" value=\" \" onClick=\"CKAddGust3(".$_SESSION['times'].")\">";
                        echo '</div>';
                    }
                    echo '</form>';
                }
                //if the login user ID was judged as blacklist, then show this alert window
                else if(@$_SESSION['blacklisted'] == 'y'){
                    echo "<SCRIPT Language=javascript>";
                    echo "window.alert('您目前被列管中,無法借用任何物品!')";
                    echo "</SCRIPT>";
                }
            }

    }
    //if there's still divs left, show the information below(assistent ID, submit button)
    function checkifanysessionexist(){
        for($z=1;$z<=$_SESSION['times'];$z++){
            if(@$_SESSION['divv'.$z]!=NULL){
                return 'y';
            }
        }
        return 'n';
    }
?>
</div>
<script type="text/javascript">
//to show the information(bail, fine) on the checking sheet
var ray={
            ajax:function(total_timess){
                var assitnat_name = document.getElementById("handler_name").innerHTML;
                var myList = document.getElementsByClassName("returned");
                var prom_doll_in = 0;
                var prom_doll_out = 0;
                var back_str = "";  
                
                if(myList.length==0){
                    back_str = '無';
                }
                else if(myList.length>1){
                    back_str = myList[0].value;
                    for(var x=1;x<myList.length;x++){
                        back_str = back_str+','+myList[x].value;
                    }
                }else if(myList.length<=1){
                    for(var x=0;x<myList.length;x++){
                        back_str = back_str+myList[x].value+'  ';
                    }
                }

                var a = 0;
                var b = 0;
                var STRS = new Array();
                var STRS2 = new Array();
                for(var j=1;j<=total_timess;j++){
                    var word = 'bck_chs'+j;
                    var oRadio = document.getElementsByName(word);
                    if((oRadio[0]||oRadio[1])&&(!oRadio[2])){
                        if(oRadio[0].checked){
                            a = document.getElementById(word).value;
                            STRS.push(a);
                        }else if(oRadio[1].checked){
                            b = document.getElementById(word).value;
                            STRS2.push(b);
                        }
                    }else if((oRadio[0]||oRadio[1])||(oRadio[2])){
                        if(oRadio[2].checked){
                            a = document.getElementById(word).value;
                            STRS.push(a);
                        }else if(oRadio[1].checked){
                            b = document.getElementById(word).value;
                            STRS2.push(b);
                        }
                    }
                }
                var out_str_1 = "無";
                var out_str_2 = "無";
                for(var k=0;k<STRS.length;k++){
                    if(k==0){
                        out_str_1 = STRS[k];
                    }else{
                        out_str_1 = out_str_1+','+STRS[k];
                    }
                }
                for(var l=0;l<STRS2.length;l++){
                    if(l==0){
                        out_str_2 = STRS2[l];
                    }else{
                        out_str_2 = out_str_2 +','+STRS2[l];
                    }
                }

                
                var xmlhttp1;
                if(window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp1=new XMLHttpRequest();
                }
                else{// code for IE6, IE5
                    xmlhttp1=new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp1.onreadystatechange=function(){
                    if (xmlhttp1.readyState==4 && xmlhttp1.status==200){
                        prom_doll_in = xmlhttp1.responseText;
                        window.top.document.getElementById('money').innerText = '此次保證金金額共為: '+prom_doll_in+' 元';
                    }
                }
                xmlhttp1.open("GET","calcu_margin.php?q="+back_str,true);
                xmlhttp1.send();

                var xmlhttp2;
                if(window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp2=new XMLHttpRequest();
                }
                else{// code for IE6, IE5
                    xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp2.onreadystatechange=function(){
                    if (xmlhttp2.readyState==4 && xmlhttp2.status==200){
                        prom_doll_out = xmlhttp2.responseText;
                        window.top.document.getElementById('money_back').innerText = '此次歸還保證金金額共為: '+prom_doll_out+' 元';
                    }
                }
                xmlhttp2.open("GET","calcu_margin.php?q="+out_str_2,true);
                xmlhttp2.send();



                window.top.document.getElementById('out').innerText = '此次借用項目為: '+back_str;
                window.top.document.getElementById('back_c').innerText = '此次續借項目為: '+ out_str_1;
                window.top.document.getElementById('back').innerText = '此次歸還項目為: '+ out_str_2;
                window.top.document.getElementById('astname').innerText = assitnat_name;
                window.top.document.getElementById('sign_up').style.display='';
                window.top.document.getElementById('lightbox-shadow').style.display='';

                var xmlhttp;
                if(window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp=new XMLHttpRequest();
                }
                else{// code for IE6, IE5
                    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange=function(){
                    if (xmlhttp.readyState==4 && xmlhttp.status==200){
                        fine_text_array = xmlhttp.responseText.split("*");
                        if(fine_text_array[0]==''){
                            fine_text_array[0]='無';
                        }
                        window.top.document.getElementById('out_deadline').innerText = '此次超出歸還時間項目為: ' + fine_text_array[0] ;
                        window.top.document.getElementById('fine').innerText = '此次需繳交罰金金額為: ' + fine_text_array[1] + '元';
                        var total_pay_rec_mon = prom_doll_in - prom_doll_out + parseInt(fine_text_array[1]);  
                        var tprm_txt = "";
                        if(total_pay_rec_mon>0){
                            tprm_txt = '借用者共需繳交: ' + total_pay_rec_mon + '元 保證金';
                        }else if(total_pay_rec_mon<0){
                            total_pay_rec_mon = 0 - total_pay_rec_mon;
                            tprm_txt = 'Itlab共須退還: ' + total_pay_rec_mon + '元 保證金';;
                        }else{
                            tprm_txt = '此次借還程序雙方無須繳交/退還保證金';
                        }
                        window.top.document.getElementById('final_account').innerText = tprm_txt;
                    }
                }
                xmlhttp.open("GET","over_deadline_form.php?q="+STRS2,true);
                xmlhttp.send();

                return false;
            }
        }
    //it change the hidden value in the form which will be sent(when the equipment's status is neither borrowed nor returned)
    function insert_class(x){
        if(document.getElementById('spec_item_func_lend'+x).checked){
            document.getElementById('decide_end_menu'+x).className = 'returned';
            document.getElementById('galaxsis'+x).style.display="block";
            document.getElementById('fill_class'+x).value='';
        }else if(document.getElementById('spec_item_func_return'+x).checked){
            document.getElementById('decide_end_menu'+x).className = 'borrowed';
            document.getElementById('galaxsis'+x).style.display="none";
            document.getElementById('fill_class'+x).value='none(returning)';
        }else if(document.getElementById('spec_item_func_continue_borrow'+x).checked){
            document.getElementById('decide_end_menu'+x).className = 'borrowed';
            document.getElementById('galaxsis'+x).style.display="none";
            document.getElementById('fill_class'+x).value='none(returning)';
        }
    }
    //remove the equipment's div which including the remove button that user pressed
    function removed(tim){
        var xmlhttp;
        document.getElementById("textDiv").innerText = "";
        //if there's no equipment on the list, remove adot div(assistent ID and submit button)
        if(document.getElementById("childform").getElementsByTagName("div").length<4){
            document.getElementById("adot").parentNode.removeChild(document.getElementById("adot"));
        }
        document.getElementById("divv"+tim).parentNode.removeChild(document.getElementById("divv"+tim));
        if(window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else{// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
            if(xmlhttp.readyState==4 && xmlhttp.status==200){
                // document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
            }
        }
            xmlhttp.open("GET","remove_multi.php?q=divv"+tim,true);
            xmlhttp.send();
    }
    //detect error(if useclass and assistant ID are blank)
    function CKAddGust3(total_times){
        var div = document.getElementById("textDiv");
        if((document.getElementById('handler_name').innerText == "No such person!")||(document.getElementById('handler_name').innerText == "")){
            div.innerText = "經辦助理欄位錯誤(查無此人)!";
            div.style.color = "red";
            document.getElementById('assname').focus();
            document.getElementById('handler_name').innerHTML == "";
            event.returnValue = false;
        }
        if(document.getElementById('assname').value == ""){
            div.innerText = "尚未填寫經辦助理欄位";
            div.style.color = "red";
            document.getElementById('assname').focus();
            event.returnValue = false;
        }
        for(var x=1;x<=total_times;x++){
            if(document.getElementById('fill_class'+x)){
                var valuee = document.getElementById('fill_class'+x).value;
                if(valuee == ""){
                    div.innerText = "器材尚未填寫[使用課程]欄位!";
                    div.style.color = "red";
                    document.getElementById('fill_class'+x).focus();
                    event.returnValue = false;
                    break;
                }
            }
        }
        if(document.getElementsByClassName("blink").length>0){
            div.innerText = "尚有一個以上的子類別沒有填寫!";
            div.style.color = "red";
            window.top.document.getElementById('equipment_search_0').focus();
            event.returnValue = false;
        }
        document.getElementById('assname1').value = document.getElementById('handler_name').innerHTML;
    }
    //when typing in assistant ID, show the assistant name
    function showHint(str){
        var xmlhttp;
        if(str.length==0){ 
            document.getElementById("handler_name").innerHTML="";
            return;
        }
        if(window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else{// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                document.getElementById("handler_name").innerHTML=xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","gethint.php?q="+str,true);
        xmlhttp.send();
    }
</script>