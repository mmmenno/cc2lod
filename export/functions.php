<?php

function turtletime($start,$end) {

    $daysinmonth = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
    $startparts  = explode("-", $start);
    if($startparts[1]=="xx"){
        $startmin = $startparts[0] . "-01-01";
        $startmax = $startparts[0] . "-12-31";
    }elseif($startparts[2]=="xx"){
        $monthkey = (int)$startparts[1];
        $startmin = $startparts[0] . "-" . $startparts[1] . "-01";
        $startmax = $startparts[0] . "-" . $startparts[1] . "-" . $daysinmonth[$monthkey];
    }else{
        $startmin = $startparts[0] . "-" . $startparts[1] . "-" . $startparts[2];
        $startmax = $startparts[0] . "-" . $startparts[1] . "-" . $startparts[2];
    }
    
    $endparts  = explode("-", $end);
    if($endparts[1]=="xx"){
        $endmin = $endparts[0] . "-01-01";
        $endmax = $endparts[0] . "-12-31";
    }elseif($endparts[2]=="xx"){
        $monthkey = (int)$endparts[1];
        $endmin = $endparts[0] . "-" . $endparts[1] . "-01";
        $endmax = $endparts[0] . "-" . $endparts[1] . "-" . $daysinmonth[$monthkey];
    }else{
        $endmin = $endparts[0] . "-" . $endparts[1] . "-" . $endparts[2];
        $endmax = $endparts[0] . "-" . $endparts[1] . "-" . $endparts[2];
    }
    
    $ttl = "";
    if($start!="xxxx-xx-xx" && strlen($start)){
        $ttl .= "\tsem:hasEarliestBeginTimeStamp \"" . $startmin . "\"^^xsd:date ;\n";
        $ttl .= "\tsem:hasLatestBeginTimeStamp \"" . $startmax . "\"^^xsd:date ;\n";
    }
    if($end!="xxxx-xx-xx" && strlen($end)){
        $ttl .= "\tsem:hasEarliestEndTimeStamp \"" . $endmin . "\"^^xsd:date ;\n";
        $ttl .= "\tsem:hasLatestEndTimeStamp \"" . $endmax . "\"^^xsd:date ;\n";
    }

    return $ttl;
}

function voorloopnullen($id){

    if(strlen($id)==1){
        return "00000" . $id;
    }
    if(strlen($id)==2){
        return "0000" . $id;
    }
    if(strlen($id)==3){
        return "000" . $id;
    }
    if(strlen($id)==4){
        return "00" . $id;
    }
    if(strlen($id)==5){
        return "0" . $id;
    }

    return $id;
}

?>