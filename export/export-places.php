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

echo "# default graph\n";
echo "{\n";
echo "\t<https://data.create.humanities.uva.nl/id/cinemacontext/> a schema:Dataset ;\n";
echo "\t\tschema:name \"Cinema Context\"@en . \n";
echo "}\n\n";


$sql = "select * 
		from tbladdress 
		limit 3000000";
$result = $mysqli->query($sql);

echo "# named graph\n";
echo "<https://data.create.humanities.uva.nl/id/cinemacontext/> {\n\n";

while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/id/place/" . $row['address_id'] . ">\n";

    if(strlen($row['geodata'])){
        $exploded = explode(",", $row['geodata']);
        $wkt = "POINT(" . trim($exploded[1]) . " " . trim($exploded[0]) . ")";
        echo "\tgeo:hasGeometry [\n";
        echo "\t\tgeo:asWKT \"" . $wkt . "\" ;\n";
        echo "\t] ;\n";
    }

    if(strlen($row['street_name'])){
        echo "\tschema:address [\n";
        echo "\t\tschema:streetAddress \"" . esc($row['street_name']) . "\" ;\n";
        if(strlen($row['alt_street_name'])){
            echo "\t\tschema:streetAddress \"" . esc($row['alt_street_name']) . "\" ;\n";
        }
        echo "\t\tschema:addressLocality \"" . esc($row['city_name']) . "\" ;\n";
        if(strlen($row['alt_city_name'])){
            echo "\t\tschema:addressLocality \"" . esc($row['alt_city_name']) . "\" ;\n";
        }
        echo "\t] ;\n";
    }

    if(strlen($row['info'])){
        echo "\tschema:description \"" . esc($row['info']) . "\" ;\n";
    }

    $s2 = "select * 
        from tblJoinAddressPublication 
        where address_id = '" . $row['address_id'] . "'";
    $res2 = $mysqli->query($s2);
    $r2 = $res2->fetch_assoc();

    if($res2->num_rows){
        echo "\tschema:citation [\n";
        echo "\t\trdf:value <http://www.cinemacontext.nl/id/publication/" . $r2['publication_id'] . "> ;\n";
        if(strlen($r2['info'])){
            echo "\t\tschema:description \"" . esc($r2['info']) . "\" ;\n";
        }
        echo "\t] ;\n";
    }


    echo  "\ta schema:Place .\n\n";

}

// named graph end
echo "}\n";
