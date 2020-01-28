<?php 

include("settings.php");
include("functions.php");

$prefixes = "
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . 
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . 
@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/> . 
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix schema: <http://schema.org/> . \n\n";

echo $prefixes;

$sql = "select p.*, i.new_id, f.first_name 
		from tblperson as p 
		left join PersID as i on p.person_id = i.old_id
		left join tblPersonFirstNames as f on p.person_id = f.person_id
		limit 30";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/id/P" . voorloopnullen($row['new_id']) . ">\n";

    $surname = trim($row['suffix'] . " " . str_replace("\n"," ",$row['last_name']));
    $literalName = trim($row['first_name'] . " " . $surname);

    echo "\trdfs:label \"" . $literalName . "\" ;\n";
    echo "\tpnv:hasName [\n";
    echo "\t\tpnv:literalName \"" . $literalName . "\" ;\n";
    echo "\t\tpnv:givenName \"" . $row['first_name'] . "\" ;\n";
    if(strlen($row['suffix'])){
    	echo "\t\tpnv:surnamePrefix \"" . $row['suffix'] . "\" ;\n";
    	echo "\t\tpnv:baseSurname \"" . str_replace("\n"," ",$row['last_name']) . "\" ;\n";
	}
    echo "\t\tpnv:surname \"" . $surname . "\" ;\n";
    echo "\t] ;\n";





    $s1 = "select x.*, i.new_id
		from tblJoinCompanyPerson as x 
		left join tblcompany as c on x.company_id = c.company_id
		left join RPID as i on c.company_id = i.old_id
		where x.person_id = '" . $row['person_id'] . "'
		order by x.s_order";
	$res1 = $mysqli->query($s1);

	while ($r1 = $res1->fetch_assoc()) {

		$period = turtletime($r1['start_date'],$r1['end_date']);

		echo "\tschema:worksFor [\n";
	    echo "\t\ta schema:OrganizationRole ;\n";
	    echo "\t\tschema:roleName \"" . $r1['job_type'] . "\" ;\n";
	    if(strlen($r1['info'])){
	    	echo "\t\tschema:description \"" . $r1['info'] . "\" ;\n";
		}
	    echo "\t\tschema:worksFor <http://www.cinemacontext.nl/id/R" . voorloopnullen($r1['new_id']) . "> ;\n";
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
	    echo "\t] ;\n";

	}



    if($row['date_birth']!="" && preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$row['date_birth'])){
        echo "\tschema:birthDate \"" . $row['date_birth'] . "\"^^xsd:date ;\n";
    }

    echo  "\ta schema:Person .\n\n";

}


