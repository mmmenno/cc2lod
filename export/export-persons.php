<?php 

include("settings.php");
include("functions.php");

$prefixes = "
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . 
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . 
@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/> . 
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix pnv: <https://w3id.org/pnv#> . 
@prefix schema: <http://schema.org/> . \n\n";

echo $prefixes;

echo "# default graph\n";
echo "{\n";
echo "\t<https://data.create.humanities.uva.nl/id/cinemacontext/> a schema:Dataset ;\n";
echo "\t\tschema:name \"Cinema Context\"@en ; \n";
echo "\t\tschema:description \"Data on Dutch Cinema: venues, people, companies, films, screenings, etc.\"@en . \n";
echo "}\n\n";


$sql = "select p.*, i.new_id
		from tblPerson as p 
		left join PersID as i on p.person_id = i.old_id
		limit 3000000";
$result = $mysqli->query($sql);


echo "# named graph\n";
echo "<https://data.create.humanities.uva.nl/id/cinemacontext/> {\n\n";


while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/id/P" . voorloopnullen($row['new_id']) . ">\n";

    $surname = trim(esc($row['suffix']) . " " . esc($row['last_name']));
    $literalName = trim(esc($row['first_name']) . " " . $surname);

    echo "\tschema:name \"" . $literalName . "\" ;\n";

    // sex is sometimes indicated by prefix 'mevr.' in 'first_name'!
    if(preg_match("/^mevr/i", $row['first_name'])){
    	$row['first_name'] = trim(str_replace("mevr.","",$row['first_name']));
    	$literalName = trim(str_replace("mevr.","",$literalName));
    	echo "\tschema:gender schema:Female ;\n";
    }

    echo "\tpnv:hasName [\n";
    echo "\t\tpnv:literalName \"" . $literalName . "\" ;\n";

    if(preg_match("/[a-z]/",$row['first_name'])){
    	echo "\t\tpnv:givenName \"" . esc($row['first_name']) . "\" ;\n";
    }elseif(strlen($row['first_name'])){
    	echo "\t\tpnv:initials \"" . esc($row['first_name']) . "\" ;\n";
    }

    if(strlen($row['suffix'])){
    	echo "\t\tpnv:surnamePrefix \"" . esc($row['suffix']) . "\" ;\n";
    	echo "\t\tpnv:baseSurname \"" . esc($row['last_name']) . "\" ;\n";
	}
    echo "\t\tpnv:surname \"" . $surname . "\" ;\n";
    echo "\t\ta pnv:PersonName ;\n";
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
	    	echo "\t\tschema:description \"" . esc($r1['info']) . "\" ;\n";
		}
	    echo "\t\tschema:worksFor <http://www.cinemacontext.nl/id/R" . voorloopnullen($r1['new_id']) . "> ;\n";
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
	    echo "\t] ;\n";

	}

	$s1 = "select x.*, i.new_id
		from tblJoinVenuePerson as x 
		left join tblvenue as v on x.venue_id = v.venue_id
		left join BiosID as i on v.venue_id = i.old_id
		where x.person_id = '" . $row['person_id'] . "'
		order by x.s_order";
	$res1 = $mysqli->query($s1);

	while ($r1 = $res1->fetch_assoc()) {

		$period = turtletime($r1['start_date'],$r1['end_date']);

		echo "\tschema:worksFor [\n";
	    echo "\t\ta schema:OrganizationRole ;\n";
	    echo "\t\tschema:roleName \"" . $r1['job_type'] . "\" ;\n";
	    if(strlen($r1['info'])){
	    	echo "\t\tschema:description \"" . esc($r1['info']) . "\" ;\n";
		}
	    echo "\t\tschema:worksFor <http://www.cinemacontext.nl/id/B" . voorloopnullen($r1['new_id']) . "> ;\n";
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
	    echo "\t] ;\n";

	}



    if($row['date_birth']!="" && preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$row['date_birth'])){
        echo "\tschema:birthDate \"" . $row['date_birth'] . "\"^^xsd:date ;\n";
    }elseif($row['date_birth']!="" && preg_match("/[0-9]{4}-([0-9]{2}|xx)-xx/",$row['date_birth'])){
        echo "\tschema:birthDate \"" . substr($row['date_birth'],0,4) . "\"^^xsd:gYear ;\n";
    }

    if($row['date_deceased']!="" && preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$row['date_deceased'])){
        echo "\tschema:deathDate \"" . $row['date_deceased'] . "\"^^xsd:date ;\n";
    }elseif($row['date_deceased']!="" && preg_match("/[0-9]{4}-([0-9]{2}|xx)-xx/",$row['date_deceased'])){
        echo "\tschema:deathDate \"" . substr($row['date_deceased'],0,4) . "\"^^xsd:gYear ;\n";
    }

    echo  "\ta schema:Person .\n\n";

}



// named graph end
echo "}\n";