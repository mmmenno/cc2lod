<?php 

include("settings.php");
include("functions.php");

$prefixes = "
@prefix rdfs:   <http://www.w3.org/2000/01/rdf-schema#> . 
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . 
@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/> . 
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix geo: <http://www.opengis.net/ont/geosparql#> .
@prefix dc: <http://purl.org/dc/elements/1.1/> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix schema: <http://schema.org/> . \n\n";

echo $prefixes;

echo "# default graph\n";
echo "{\n";
echo "\t<https://data.create.humanities.uva.nl/id/cinemacontext/> a schema:Dataset ;\n";
echo "\t\tschema:name \"Cinema Context\"@en ; \n";
echo "\t\tschema:description \"Data on Dutch Cinema: venues, people, companies, films, screenings, etc.\"@en . \n";
echo "}\n\n";

echo "# named graph\n";
echo "<https://data.create.humanities.uva.nl/id/cinemacontext/> {\n\n";



$sql = "select h.*, i.new_id 
		from tblAddressConstructionHistory as h
        left join PersID as i on h.person_id = i.old_id";
$result = $mysqli->query($sql);


while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/id/constructionevent/" . $row['address_id'] . "-" . $row['s_order'] . ">\n";

    echo "\tsem:hasPlace <http://www.cinemacontext.nl/id/place/" . $row['address_id'] . "> ;\n";
    echo "\tdc:type \"" . $row['construction_type'] . "\" ;\n";

    if(!preg_match("/^[0-9]{4}(-([0-9]{2}|xx))?(-([0-9]{2}|xx))?$/", $row['construction_year']) && strlen($row['construction_year'])){
        $row['construction_year'] = substr($row['construction_year'],0,4);
    }
    $start = turtletime($row['construction_year'],$row['construction_year']);
    if(strlen($start)){
        echo $start;
    }

    if(strlen($row['person_id'])){
        echo "\tsem:hasActor [\n";
        echo "\t\trdf:value <http://www.cinemacontext.nl/id/P" . voorloopnullen($row['new_id']) . "> ;\n";
        echo "\t\tsem:roleType [\n";
        echo "\t\t\trdfs:label \"architect\" ; \n";
        echo "\t\t\ta sem:RoleType ;\n";
        echo "\t\t] ;\n";
        echo "\t\ta sem:Role ;\n";
        echo "\t] ;\n";
    }
    
    if(strlen($row['wikidata_id_building'])){
        echo "\tsem:hasActor [\n";
        echo "\t\towl:sameAs <http://www.wikidata.org/entity/" . $row['wikidata_id_building'] . "> ;\n";
        echo "\t\ta sem:Object ;\n";
        echo "\t] ;\n";
    }

    if(strlen($row['info'])){
        echo "\tschema:description \"" . esc($row['info']) . "\" ;\n";
    }


    echo  "\ta sem:Event .\n\n";

}


// named graph end
echo "}\n";

