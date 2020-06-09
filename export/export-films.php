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
@prefix dcterms: <http://purl.org/dc/terms/> .
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
        from tblFilm";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    
    $s1 = "select new_id 
        from TitelID 
        where old_id = '" . $row['film_id'] . "'";
    $res1 = $mysqli->query($s1);
    $r1 = $res1->fetch_assoc();

    echo "<http://www.cinemacontext.nl/id/F" . voorloopnullen($r1['new_id']) . ">\n";

    echo "\tschema:name \"" . esc($row['title']) . "\" ;\n";
    if(strlen($row['info'])){
        echo "\tschema:description \"" . esc($row['info']) . "\" ;\n";
    }
    if(strlen($row['film_year'])){
        echo "\tschema:dateCreated \"" . $row['film_year'] . "\"^^xsd:gYear ;\n";
    }
    if(strlen($row['imdb'])){
        echo "\tschema:sameAs <https://www.imdb.com/title/tt" . trim($row['imdb']) . "> ;\n";
    }
    if(strlen($row['film_director'])){
        echo "\tschema:creator \"" . esc($row['film_director']) . "\" ;\n";
    }
    if(strlen($row['country'])){
        echo "\tschema:countryOfOrigin \"" . $row['country'] . "\" ;\n";
    }
    if(strlen($row['film_length'])){
        echo "\tdcterms:extent [\n";
        echo "\t\tschema:value \"" . $row['film_length'] . "\"^^xsd:int ;\n";
        echo "\t\tschema:unitCode \"MTR\" ;\n";
        echo "\t\ta schema:PropertyValue ;\n";
        echo "\t] ;\n";
    }
    if(strlen($row['film_gauge'])){
        echo "\tdcterms:format \"" . $row['film_gauge'] . "\" ;\n";
    }

    if($row['fiction'] == "Y"){
        echo "\tschema:genre \"fiction\" ;\n";
    }elseif($row['fiction'] == "N"){
        echo "\tschema:genre \"nonfiction\" ;\n";
    }

    $s2 = "select x.*, i.new_id
        from tblJoinFilmCompany as x 
        left join tblcompany as c on x.company_id = c.company_id
        left join RPID as i on c.company_id = i.old_id
        where x.film_id = '" . $row['film_id'] . "'
        order by x.s_order";
    $res2 = $mysqli->query($s2);

    while ($r2 = $res2->fetch_assoc()) {

        $period = turtletime($r2['start_date'],$r2['end_date']);

        echo "\tschema:publisher [\n";
        echo "\t\tschema:publisher <http://www.cinemacontext.nl/id/R" . voorloopnullen($r2['new_id']) . "> ;\n";
        //if(strlen($r2['info'])){
        //    echo "\t\tschema:description \"" . esc($r2['info']) . "\" ;\n";
        //}
        if(strlen($period)){
            echo str_replace("\t","\t\t",$period);
        }
        echo "\t\ta schema:Role ; \n";
        echo "\t] ;\n";

    }

    echo  "\ta schema:Movie .\n\n";

}


// Now, episodes:

$sql = "select * 
        from tblFilmEpisode";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    
    echo "<http://www.cinemacontext.nl/id/episode/" . $row['episode_id'] . ">\n";

    echo "\tschema:name \"" . esc($row['title']) . "\" ;\n";
    if(preg_match("/^[0-9]{4}$/", $row['episode_year'])){
        echo "\tschema:dateCreated \"" . $row['episode_year'] . "\"^^xsd:gYear ;\n";
    }
    if(strlen($row['film_length'])){
        echo "\tdcterms:extent [\n";
        echo "\t\tschema:value \"" . $row['film_length'] . "\"^^xsd:int ;\n";
        echo "\t\tschema:unitCode \"MTR\" ;\n";
        echo "\t\ta schema:PropertyValue ;\n";
        echo "\t] ;\n";
    }
    if(strlen($row['film_gauge'])){
        echo "\tdcterms:format \"" . $row['film_gauge'] . "\" ;\n";
    }

    $s1 = "select new_id 
        from TitelID 
        where old_id = '" . $row['film_id'] . "'";
    $res1 = $mysqli->query($s1);
    $r1 = $res1->fetch_assoc();

    
    echo "\tschema:isPartOf <http://www.cinemacontext.nl/id/F" . voorloopnullen($r1['new_id']) . "> ;\n";

    echo  "\ta schema:Movie, schema:Episode .\n\n";

    

}



// named graph end
echo "}\n";

