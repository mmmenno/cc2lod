<?php 

include("settings.php");
include("functions.php");

$legalforms = array(
	"B.V." => "54M6",
	"C.V." => "CODH",
	"Firma" => "54M6",
	"N.V." => "B5PM",
	"Stichting" => "V44D",
	"Vereniging" => "33MN"
);

$prefixes = "
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . 
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . 
@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/> . 
@prefix owl: <http://www.w3.org/2002/07/owl#> . 
@prefix pext: <http://www.ontotext.com/proton/protonext#> .
@prefix skos: <http://www.w3.org/2004/02/skos/core#> .
@prefix dc: <http://purl.org/dc/elements/1.1/> .
@prefix dct: <http://purl.org/dc/terms/> .
@prefix wd: <http://www.wikidata.org/entity/> . 
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> . 
@prefix gleio: <http://lei.info/gleio/> .
@prefix schema: <http://schema.org/> . \n\n";
echo $prefixes;


echo "# default graph\n";
echo "{\n";
echo "\t<https://data.create.humanities.uva.nl/id/cinemacontext/> a schema:Dataset ;\n";
echo "\t\tschema:name \"Cinema Context\"@en ; \n";
echo "\t\tschema:license <https://creativecommons.org/licenses/by-sa/4.0/> ;\n";
echo "\t\tschema:description \"Data on Dutch Cinema: venues, people, companies, films, screenings, etc.\"@en . \n";
echo "}\n\n";

echo "# named graph\n";
echo "<https://data.create.humanities.uva.nl/id/cinemacontext/> {\n\n";



$sql = "select c.*, i.new_id 
		from tblCompany as c 
		left join RPID as i on c.company_id = i.old_id
		limit 30000000";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    


    echo "<http://www.cinemacontext.nl/id/R" . voorloopnullen($row['new_id']) . ">\n";

    echo "\tschema:name \"" . esc($row['name']) . "\" ;\n";
    if(strlen($row['info'])){
    	echo "\tschema:description \"" . esc($row['info']) . "\" ;\n";
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
		from tblJoinCompanyCompany as x 
		left join tblCompany as c on x.company_id = c.company_id
		left join RPID as i on c.company_id = i.old_id
		where x.subsidiary_id = '" . $row['company_id'] . "'
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
	    echo "\t\ta schema:Role ;\n";
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
	    echo "\t\tschema:name \"" . $r2['branch_name'] . "\" ;\n";
	    if(strlen($r2['info'])){
	    	echo "\t\tschema:description \"" . $r2['info'] . "\" ;\n";
		}
	    if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
	    echo  "\t\ta pext:IndustrySector ;\n";
	    echo "\t] ;\n";

	}

	$s3 = "select *
		from tblCompanyLegalForm
		where company_id = '" . $row['company_id'] . "'
		order by s_order";
	$res3 = $mysqli->query($s3);

	while ($r3 = $res3->fetch_assoc()) {

		$period = turtletime($r3['start_date'],$r3['end_date']);

		echo "\tgleio:hasLegalForm [\n";
		echo "\t\ta gleio:LegalForm ;\n";
		echo "\t\tskos:altLabel \"" . esc($r3['legal_form']) . "\" ;\n";
		if(isset($legalforms[$r3['legal_form']])){
			echo "\t\tgleio:hasEntityLegalFormCode \"" . $legalforms[$r3['legal_form']] . "\" ;\n";
		}
		if(strlen($period)){
	    	echo str_replace("\t","\t\t",$period);
	    }
		echo "\t] ;\n";
	}

	$s4 = "select *
		from tblCompanyOtherNames
		where company_id = '" . $row['company_id'] . "'
		order by s_order";
	$res4 = $mysqli->query($s4);

	while ($r4 = $res4->fetch_assoc()) {
		echo "\tschema:alternateName \"" . esc($r4['other_name']) . "\" ;\n";
	}


	$s5 = "select *
		from tblJoinCompanyArchive
		where company_id = '" . $row['company_id'] . "'
		order by s_order";
	$res5 = $mysqli->query($s5);

	while ($r5 = $res5->fetch_assoc()) {
		echo "\tdct:source <http://www.cinemacontext.nl/id/archivalsource/" . $r5['archive_id'] . "> ; \n";
	}


	$s6 = "select * 
		from tblJoinCompanyPublication 
		where company_id = '" . $row['company_id'] . "'";
	$res6 = $mysqli->query($s6);
	$r6 = $res6->fetch_assoc();

	if($res6->num_rows){
    	echo "\tschema:citation [\n";
    	echo "\t\tschema:citation <http://www.cinemacontext.nl/id/publication/" . $r6['publication_id'] . "> ;\n";
    	if(strlen($r6['info'])){
    		echo "\t\tschema:description \"" . esc($r6['info']) . "\" ;\n";
    	}
    	echo "\t\ta schema:Role ;\n";
    	echo "\t] ;\n";
	}




    echo  "\ta schema:Organization .\n\n";

	

}

// named graph end
echo "}\n";
