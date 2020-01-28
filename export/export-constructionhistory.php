<?php 

include("settings.php");
include("functions.php");

$prefixes = "
@prefix rdfs:   <http://www.w3.org/2000/01/rdf-schema#> . 
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . 
@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/> . 
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix geo: <http://www.opengis.net/ont/geosparql#> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix schema: <http://schema.org/> . \n\n";

echo $prefixes;

$sql = "select h.*, i.new_id 
		from tblAddressConstructionHistory as h
        left join PersID as i on h.person_id = i.old_id
		limit 30";
$result = $mysqli->query($sql);


while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/constructionevent/" . $row['address_id'] . "-" . $row['s_order'] . ">\n";

    echo "\tsem:hasPlace <http://www.cinemacontext.nl/address/" . $row['address_id'] . "> ;\n";
    echo "\tdc:type \"" . $row['construction_type'] . "\" ;\n";

    if(strlen($row['person_id'])){
        echo "\tsem:hasActor [\n";
        echo "\t\trdf:value <http://www.cinemacontext.nl/id/P" . voorloopnullen($row['new_id']) . "> ;\n";
        echo "\t\tsem:roleType [\n";
        echo "\t\t\trdfs:label \"architect\" ; \n";
        echo "\t\t\ta sem:RoleType .\n";
        echo "\t\t] ;\n";
        echo "\t\ta sem:Role .\n";
        echo "\t] ;\n";
    }

    if(strlen($row['info'])){
        echo "\tschema:description \"" . $row['info'] . "\" ;\n";
    }


    echo  "\ta sem:Event .\n\n";

}


