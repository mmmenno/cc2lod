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
@prefix dc: <http://purl.org/dc/elements/1.1/> .
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix dcterms: <http://purl.org/dc/terms/format> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix schema: <http://schema.org/> . \n\n";
echo $prefixes;


$sql = "select p.*, v.venue_type
		from tblprogramme as p
		left join tblvenue as v on p.venue_id = v.venue_id";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    
	$s1 = "select new_id 
		from FilmvoorstellingID 
		where old_id = '" . $row['programme_id'] . "'";
	$res1 = $mysqli->query($s1);
	$r1 = $res1->fetch_assoc();

    echo "<http://www.cinemacontext.nl/id/V" . voorloopnullen($r1['new_id']) . ">\n";


    $s2 = "select programme_date 
		from tblProgrammeDate 
		where programme_id = '" . $row['programme_id'] . "'";
	$res2 = $mysqli->query($s2);
	while ($r2 = $res2->fetch_assoc()){
		echo "\tschema:startDate \"" . str_replace("-xx","",$r2['programme_date']) . "\"^^xsd:date ;\n";
	}
	
	if(strlen($row['programme_title'])){
    	echo "\trdfs:label \"" . addslashes($row['programme_title']) . "\" ;\n";
	}

	if(strlen($row['info'])){
    	echo "\tschema:description \"" . addslashes($row['info']) . "\" ;\n";
	}
    
    $s3 = "select new_id 
		from BiosID 
		where old_id = '" . $row['venue_id'] . "'";
	$res3 = $mysqli->query($s3);
	$r3 = $res3->fetch_assoc();

	if($res3->num_rows){
    	echo "\tschema:location <http://www.cinemacontext.nl/id/B" . voorloopnullen($r3['new_id']) . "> ;\n";
	}else{
		if($row['venue_type']=="mobile theatre"){
			echo "\tschema:location <http://www.cinemacontext.nl/stand/" . $row['venue_id'] . "> ;\n";
		}else{
			echo "\tschema:location <http://www.cinemacontext.nl/eventvenue/" . $row['venue_id'] . "> ;\n";
		}

		// also, programmes are joined to a company if it's a traveling cinema
	 	$s6 = "select c.*, i.new_id
			from tblJoinProgrammeCompany as x 
			left join tblcompany as c on x.company_id = c.company_id
			left join RPID as i on c.company_id = i.old_id
			where x.programme_id = '" . $row['programme_id'] . "'";
		$res6 = $mysqli->query($s6);
		while($r6 = $res6->fetch_assoc()){
			echo "\tschema:organizer <http://www.cinemacontext.nl/id/R" . voorloopnullen($r6['new_id']) . "> ;\n";
		}

	}


	$s4 = "select pi.*, ti.new_id, ti2.new_id AS var_new_id, fv.title as var_title, fv.language_code, fv.info as var_info
		from tblProgrammeItem as pi
		left join TitelID as ti on pi.film_id = ti.old_id
		left join tblFilmTitleVariation as fv on pi.film_variation_id = fv.film_variation_id
		left join TitelID as ti2 on fv.film_id = ti2.old_id
		where programme_id = '" . $row['programme_id'] . "'
		order by programme_item_order";
	$res4 = $mysqli->query($s4);
	
	while ($r4 = $res4->fetch_assoc()){
		if(strlen($r4['film_id'])){
			echo "\tschema:subEvent [\n";
			echo "\t\tschema:workPresented <http://www.cinemacontext.nl/id/F" . voorloopnullen($r4['new_id']) . "> ;\n";
			if(strlen($r4['programme_item_order'])){
				echo "\t\tschema:position \"" . (int)$r4['programme_item_order'] . "\"^^xsd:int ;\n";
			}
			echo "\t\ta schema:ScreeningEvent ;\n";
			echo "\t] ;\n";
		}elseif(strlen($r4['film_variation_id'])){
			echo "\tschema:subEvent [\n";
			echo "\t\tschema:workPresented <http://www.cinemacontext.nl/id/F" . voorloopnullen($r4['var_new_id']) . "> ;\n";
			if(strlen($r4['programme_item_order'])){
				echo "\t\tschema:position \"" . (int)$r4['programme_item_order'] . "\"^^xsd:int ;\n";
			}
			if(strlen($r4['var_title'])){
				echo "\t\tschema:alternateName \"" . esc($r4['var_title']) . "\" ;\n";
			}
			if(strlen($r4['language_code'])){
				echo "\t\tschema:inLanguage \"" . esc($r4['language_code']) . "\" ;\n";
			}
			echo "\t\ta schema:ScreeningEvent ;\n";
			echo "\t] ;\n";
		}
    }

    $s5 = "select * 
		from tblJoinProgrammePublication 
		where programme_id = '" . $row['programme_id'] . "'";
	$res5 = $mysqli->query($s5);
	$r5 = $res3->fetch_assoc();

	if($res3->num_rows){
    	echo "\tschema:citation [\n";
    	echo "\t\trdf:value <http://www.cinemacontext.nl/id/publication/" . $r5['publication_id'] . "> ;\n";
    	if(strlen($r5['info'])){
    		echo "\t\tschema:description \"" . esc($r5['info']) . "\" ;\n";
    		die;
    	}
    	echo "\t] ;\n";
	}


	

    echo  "\ta schema:Event .\n\n";

	

}


