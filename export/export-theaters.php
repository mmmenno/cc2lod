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
@prefix dbo: <http://dbpedia.org/ontology/> .
@prefix schema: <http://schema.org/> . \n\n";
echo $prefixes;

echo "# default graph\n";
echo "{\n";
echo "\t<https://data.create.humanities.uva.nl/id/cinemacontext/> a schema:Dataset ;\n";
echo "\t\tschema:name \"Cinema Context\"@en ; \n";
echo "\t\tschema:description \"Data on Dutch Cinema: venues, people, companies, films, screenings, etc.\"@en . \n";
echo "}\n\n";

$sql = "select v.*, i.new_id 
		from tblVenue as v 
		left join BiosID as i on v.venue_id = i.old_id
		limit 300000000";
$result = $mysqli->query($sql);


echo "# named graph\n";
echo "<https://data.create.humanities.uva.nl/id/cinemacontext/> {\n\n";

while ($row = $result->fetch_assoc()) {
    

	if(!strlen($row['new_id'])){
		if($row['venue_type']=="mobile theatre"){
			echo "<http://www.cinemacontext.nl/id/stand/" . $row['venue_id'] . ">\n";
		}else{
			echo "<http://www.cinemacontext.nl/id/eventvenue/" . $row['venue_id'] . ">\n";
		}
	}else{
    	echo "<http://www.cinemacontext.nl/id/B" . voorloopnullen($row['new_id']) . ">\n";
	}

    echo "\tschema:name \"" . esc($row['name']) . "\" ;\n";
    echo "\tschema:location <http://www.cinemacontext.nl/id/place/" . $row['address_id'] . "> ;\n";
    if(strlen($row['info'])){
    	echo "\tschema:description \"" . esc($row['info']) . "\" ;\n";
	}


	$s1 = "select x.*, i.new_id
		from tblJoinVenueCompany as x 
		left join tblCompany as c on x.company_id = c.company_id
		left join RPID as i on c.company_id = i.old_id
		where x.venue_id = '" . $row['venue_id'] . "'
		order by x.s_order";
	$res1 = $mysqli->query($s1);

	while ($r1 = $res1->fetch_assoc()) {

		$period = turtletime($r1['start_date'],$r1['end_date']);

		echo "\tschema:parentOrganization [\n";
	    echo "\t\tschema:parentOrganization <http://www.cinemacontext.nl/id/R" . voorloopnullen($r1['new_id']) . "> ;\n";
	    if(strlen($r1['info'])){
	    	echo "\t\tschema:description \"" . esc($r1['info']) . "\" ;\n";
		}
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
        echo "\t\ta schema:Role ; \n";
	    echo "\t] ;\n";

	}

	// period of activity, could be more than one
	$s2 = "select *
		from tblVenueActivePeriode 
		where venue_id = '" . $row['venue_id'] . "'
		order by s_order";
	$res2 = $mysqli->query($s2);

	while ($r2 = $res2->fetch_assoc()) {

		$period = turtletime($r2['date_opened'],$r2['date_closed']);

		echo "\tschema:temporalCoverage [\n";
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
	    echo "\t] ;\n";

	}

	// number of screens, changes over time
	$s3 = "select *
		from tblVenueScreen 
		where venue_id = '" . $row['venue_id'] . "'
		order by s_order";
	$res3 = $mysqli->query($s3);

	while ($r3 = $res3->fetch_assoc()) {

		$start = turtletime($r3['date_opened']);

		echo "\tschema:screenCount [\n";
		echo "\t\tschema:screenCount \"" . $r3['number_of_screens'] . "\"^^xsd:int ;\n";
	    if(strlen($start)){
	    	echo str_replace("\t","\t\t",$start);
	    }
	    echo "\t\ta schema:Role ;\n";
	    echo "\t] ;\n";

	}

	//http://dbpedia.org/ontology/seatingCapacity

	$s4 = "select *
		from tblVenueSeats 
		where venue_id = '" . $row['venue_id'] . "'
		order by s_order";
	$res4 = $mysqli->query($s4);

	while ($r4 = $res4->fetch_assoc()) {

		//$start = turtletime($r4['seats_year']);

		echo "\tdbo:seatingCapacity [\n";
		echo "\t\tdbo:seatingCapacity \"" . $r4['number_of_seats'] . "\"^^xsd:int ;\n";
		echo "\t\tsem:hasLatestBeginTimeStamp \"" . $r4['seats_year'] . "-12-31\"^^xsd:date ;\n";
        echo "\t\ta schema:Role ; \n";
	    echo "\t] ;\n";

	}

	$s5 = "select * 
		from tblJoinVenuePublication 
		where venue_id = '" . $row['venue_id'] . "'";
	$res5 = $mysqli->query($s5);
	$r5 = $res5->fetch_assoc();

	if($res5->num_rows){
    	echo "\tschema:citation [\n";
    	echo "\t\tschema:citation <http://www.cinemacontext.nl/id/publication/" . $r5['publication_id'] . "> ;\n";
    	if(strlen($r5['info'])){
    		echo "\t\tschema:description \"" . esc($r5['info']) . "\" ;\n";
    	}
        echo "\t\ta schema:Role ; \n";
    	echo "\t] ;\n";
	}




    if($row['venue_type']=="Cinema"){
    	echo  "\ta schema:MovieTheater .\n\n";
    }elseif($row['venue_type']=="mobile theatre"){
    	echo  "\tschema:additionalType wd:Q6605486 ;\n";
    	echo  "\ta schema:MovieTheater .\n\n";
    }else{
    	echo  "\ta schema:EventVenue .\n\n";
    }
}


// named graph end
echo "}\n";



