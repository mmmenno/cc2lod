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
@prefix wd: <http://www.wikidata.org/entity/> . 
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



$sql = "select * 
		from tblpublication
		limit 3000000000";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/id/publication/" . $row['publication_id'] . ">\n";
    
    if(strlen($row['title'])){
    	echo "\tschema:headline \"\"\"" . esc($row['title']) . "\"\"\" ;\n";
	}
    if(strlen($row['book_journal_title'])){
    	echo "\tdc:title \"\"\"" . esc($row['book_journal_title']) . "\"\"\" ;\n";
	}
    if(strlen($row['publication_date'])){
    	echo "\tschema:datePublished \"" . substr($row['publication_date'],0,4) . "\"^^xsd:gYear ;\n";
	}
    if(strlen($row['publication_volume'])){
    	echo "\tschema:volumeNumber \"\"\"" . $row['publication_volume'] . "\"\"\" ;\n";
	}
    if(strlen($row['publication_number'])){
    	echo "\tschema:issueNumber \"\"\"" . $row['publication_number'] . "\"\"\" ;\n";
	}
    if(strlen($row['author'])){
    	echo "\tdc:creator \"\"\"" . $row['author'] . "\"\"\" ;\n";
	}
    if(strlen($row['editor'])){
    	echo "\tschema:editor \"\"\"" . $row['editor'] . "\"\"\" ;\n";
	}
    if($row['publication_type']=="Artikel"){
    	echo "\ta schema:Article .\n\n";
	}elseif($row['publication_type']=="Boek"){
    	echo "\ta schema:Book .\n\n";
	}elseif($row['publication_type']=="Boek"){
    	echo "\ta schema:Chapter .\n\n";
	}elseif($row['publication_type']=="Boek"){
    	echo "\ta schema:Manuscript .\n\n";
	}else{
    	echo "\ta schema:CreativeWork .\n\n";
	}
    

	

}



// named graph end
echo "}\n";

