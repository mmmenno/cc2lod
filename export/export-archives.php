<?php 

include("settings.php");
include("functions.php");

$prefixes = "
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . 
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . 
@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/> . 
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix pext: <http://www.ontotext.com/proton/protonext#> .
@prefix dc: <http://purl.org/dc/elements/1.1/> .
@prefix dct: <http://purl.org/dc/terms/> .
@prefix wd: <http://www.wikidata.org/entity/> . 
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix schema: <http://schema.org/> . \n\n";
echo $prefixes;


echo "# default graph\n";
echo "{\n";
echo "\t<https://data.create.humanities.uva.nl/id/cinemacontext/> a schema:Dataset ;\n";
echo "\t\tschema:name \"Cinema Context\"@en . \n";
echo "}\n\n";

echo "# named graph\n";
echo "<https://data.create.humanities.uva.nl/id/cinemacontext/> {\n\n";



$sql = "select * 
		from tblarchive";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/archivalsource/" . $row['archive_id'] . ">\n";

    echo "\trdfs:label \"" . esc($row['inventory']) . "\" ;\n";

    $description = array();
    if(strlen($row['description'])){
    	$description[] = $row['description'];
	}
    if(strlen($row['info'])){
    	$description[] = $row['info'];
	}
    if(count($description)){
    	echo "\tschema:description \"" . esc(implode(" ",$description)) . "\" ;\n";
	}
    if(strlen($row['institution'])){
    	echo "\tschema:holdingArchive [\n";
    	echo "\t\trdfs:label \"" . esc($row['institution']) . "\" ;\n";
    	echo "\t\ta schema:ArchiveOrganization ;\n";
    	echo "\t] ;\n";
	}
    if(strlen($row['item_nr'])){
    	echo "\tschema:identifier \"" . esc($row['item_nr']) . "\" ;\n";
	}

    echo  "\ta schema:ArchiveComponent .\n\n";

	

}

// named graph end
echo "}\n";
