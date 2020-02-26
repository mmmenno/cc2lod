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

$sql = "select * 
		from tbladdress 
		limit 3000000";
$result = $mysqli->query($sql);

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


    echo  "\ta schema:Place .\n\n";

}


