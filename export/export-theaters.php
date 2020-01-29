<?php 

include("settings.php");
include("functions.php");

$prefixes = "
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . 
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . 
@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/> . 
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix wd: <http://www.wikidata.org/entity/> . 
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix schema: <http://schema.org/> . \n\n";
echo $prefixes;

$sql = "select v.*, i.new_id 
		from tblvenue as v 
		left join BiosID as i on v.venue_id = i.old_id
		limit 300000000";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/id/B" . voorloopnullen($row['new_id']) . ">\n";

    echo "\trdfs:label \"" . addslashes($row['name']) . "\" ;\n";
    echo "\tschema:location <http://www.cinemacontext.nl/place/" . $row['address_id'] . "> ;\n";
    if(strlen($row['info'])){
    	echo "\tschema:description \"" . addslashes($row['info']) . "\" ;\n";
	}


	$s1 = "select x.*, i.new_id
		from tblJoinVenueCompany as x 
		left join tblcompany as c on x.company_id = c.company_id
		left join RPID as i on c.company_id = i.old_id
		where x.venue_id = '" . $row['venue_id'] . "'
		order by x.s_order";
	$res1 = $mysqli->query($s1);

	while ($r1 = $res1->fetch_assoc()) {

		$period = turtletime($r1['start_date'],$r1['end_date']);

		echo "\tschema:parentOrganization [\n";
	    echo "\t\tschema:parentOrganization <http://www.cinemacontext.nl/id/R" . voorloopnullen($r1['new_id']) . "> ;\n";
	    if(strlen($r1['info'])){
	    	echo "\t\tschema:description \"" . addslashes($r1['info']) . "\" ;\n";
		}
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
	    echo "\t] ;\n";

	}



    if($row['venue_type']=="Cinema"){
    	echo  "\ta schema:MovieTheater .\n\n";
    }elseif($row['venue_type']=="mobile theatre"){
    	echo  "\ta schema:MovieTheater, wd:Q6605486 .\n\n";
    }else{
    	echo  "\ta schema:EventVenue .\n\n";
    }
}


