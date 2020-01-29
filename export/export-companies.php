<?php 

include("settings.php");
include("functions.php");

$prefixes = "
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . 
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . 
@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/> . 
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix pext: <http://www.ontotext.com/proton/protonext#> .
@prefix wd: <http://www.wikidata.org/entity/> . 
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix schema: <http://schema.org/> . \n\n";
echo $prefixes;

$sql = "select c.*, i.new_id 
		from tblcompany as c 
		left join RPID as i on c.company_id = i.old_id
		limit 30000000";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/id/R" . voorloopnullen($row['new_id']) . ">\n";

    echo "\trdfs:label \"" . addslashes($row['name']) . "\" ;\n";
    if(strlen($row['info'])){
    	echo "\tschema:description \"" . addslashes($row['info']) . "\" ;\n";
	}
    if(strlen($row['date_established'])){
    	$founded = turtletime($row['date_established'],false);
    	echo $founded;
	}
    if(strlen($row['date_disbanded'])){
    	$disbanded = turtletime(false,$row['date_disbanded']);
    	echo $disbanded;
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
	    	echo "\t\tschema:description \"" . $r1['info'] . "\" ;\n";
		}
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
	    echo "\t] ;\n";

	}

	$s2 = "select *
		from tblCompanyBranch
		where company_id = '" . $row['company_id'] . "'
		order by s_order";
	$res2 = $mysqli->query($s2);

	while ($r2 = $res2->fetch_assoc()) {

		$period = turtletime($r2['start_date'],$r2['end_date']);

		echo "\tpext:activeInSector [\n";
	    echo "\t\trdfs:label \"" . $r2['branch_name'] . "\" ;\n";
	    if(strlen($r2['info'])){
	    	echo "\t\tschema:description \"" . $r2['info'] . "\" ;\n";
		}
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
	    echo  "\t\ta pext:IndustrySector ;\n";
	    echo "\t] ;\n";

	}


    echo  "\ta schema:Organization .\n\n";

	continue;
	

}


