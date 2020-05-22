<?php 

include("settings.php");
include("functions.php");

$prefixes = "
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . 
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
        from tblCensorship";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    
    $s1 = "select new_id 
        from TitelID 
        where old_id = '" . $row['film_id'] . "'";
    $res1 = $mysqli->query($s1);
    $r1 = $res1->fetch_assoc();

    echo "<http://www.cinemacontext.nl/id/F" . voorloopnullen($r1['new_id']) . ">\n";

    echo "\tschema:contentRating <http://www.cinemacontext.nl/id/rating/" . $row['censorship_id'] . "> . \n\n";

    echo "<http://www.cinemacontext.nl/id/rating/" . $row['censorship_id'] . ">\n";

    echo "\tschema:identifier \"" . esc($row['filing_nr']) . "\" ;\n";
    if(strlen($row['censorship_date'])){
        echo "\tschema:dateCreated \"" . $row['censorship_date'] . "\"^^xsd:date ;\n";
    }
    
    if(strlen($row['company_id'])){
        $s2 = "select new_id 
                from RPID 
                where old_id = '" . $row['company_id'] . "'";
        $res2 = $mysqli->query($s2);
        $r2 = $res2->fetch_assoc();
        echo "\tschema:author <http://www.cinemacontext.nl/id/R" . voorloopnullen($r2['new_id']) . "> ;\n";
    }
    if(strlen($row['rating'])){
        echo "\tschema:text \"" . esc($row['rating']) . "\" ;\n";
    }

    if(strlen($row['recommendation']=="Y")){
        echo "\tschema:ratingValue \"recommended\" ;\n";
    }elseif(strlen($row['recommendation']=="N")){
        echo "\tschema:ratingValue \"not recommended\" ;\n";
    }

    if(strlen($row['comment_by_censor'])){
        echo "\tschema:ratingExplanation \"" . esc($row['comment_by_censor']) . "\" ;\n";
    }

    $s3 = "select * FROM tblCensorshipTitle 
            where censorship_id = '" . $row['censorship_id'] . "'";
    $res3 = $mysqli->query($s3);
    while($r3 = $res3->fetch_assoc()){
        echo "\tschema:about [\n";
        echo "\t\ta schema:Role ;\n";
        echo "\t\tschema:name \"" . esc($r3['title']) . "\" ;\n"; 
        if($r3['censorshiptitle_note']){
            echo "\t\tschema:description \"" . esc($r3['censorshiptitle_note']) . "\" ;\n"; 
        }
        echo "\t\tschema:about <http://www.cinemacontext.nl/id/F" . voorloopnullen($r1['new_id']) . "> ;\n";
        echo "\t] ;\n";
    }

    echo  "\ta schema:Rating, schema:CreativeWork .\n\n";

}





// named graph end
echo "}\n";

