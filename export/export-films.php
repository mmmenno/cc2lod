<?php 

include("settings.php");
include("functions.php");

$text2AAT = array(
    "animatie" => "http://vocab.getty.edu/aat/300410317",
    "documentaire" => "http://vocab.getty.edu/aat/300375156",
    "fictie" => "http://vocab.getty.edu/aat/300375156",
    "journaal" => "http://vocab.getty.edu/aat/300263837",
    "nonfictie" => "http://vocab.getty.edu/aat/300375156",
    "reclame" => "http://vocab.getty.edu/aat/300193993",
    "serie" => "http://vocab.getty.edu/aat/300266334",
    "trailer" => "http://vocab.getty.edu/aat/300263866"
);

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

echo "# default graph\n";
echo "{\n";
echo "\t<https://data.create.humanities.uva.nl/id/cinemacontext/> a schema:Dataset ;\n";
echo "\t\tschema:name \"Cinema Context\"@en . \n";
echo "}\n\n";

echo "# named graph\n";
echo "<https://data.create.humanities.uva.nl/id/cinemacontext/> {\n\n";



$sql = "select * 
        from tblfilm limit 0";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    
    $s1 = "select new_id 
        from TitelID 
        where old_id = '" . $row['film_id'] . "'";
    $res1 = $mysqli->query($s1);
    $r1 = $res1->fetch_assoc();

    echo "<http://www.cinemacontext.nl/id/F" . voorloopnullen($r1['new_id']) . ">\n";

    echo "\trdfs:label \"" . esc($row['title']) . "\" ;\n";
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
        echo "\tdc:creator \"" . esc($row['film_director']) . "\" ;\n";
    }
    if(strlen($row['country'])){
        echo "\tschema:countryOfOrigin \"" . $row['country'] . "\" ;\n";
    }
    if(strlen($row['film_length'])){
        echo "\tdcterms:extent [\n";
        echo "\t\tschema:value \"" . $row['film_length'] . "\"^^xsd:int ;\n";
        echo "\t\tschema:unitCode \"MTR\" ;\n";
        echo "\t] ;\n";
    }
    if(strlen($row['film_gauge'])){
        echo "\tdcterms:format \"" . $row['film_gauge'] . "\" ;\n";
    }

    // genre/category, could be more than one
    $s2 = "select *
        from tblFilmCategory 
        where film_id = '" . $row['film_id'] . "'
        order by s_order";
    $res2 = $mysqli->query($s2);

    while ($r2 = $res2->fetch_assoc()) {

        echo "\tschema:genre [\n";
        echo "\t\trdfs:label \"" . esc($r2['category']) . "\" ;\n";
        if(isset($text2AAT[$r2['category']])){
            echo "\t\tschema:genre <" . $text2AAT[$r2['category']] . "> ;\n";
        }else{
            echo "\t\tschema:genre <http://vocab.getty.edu/aat/300136900> ;\n";
        }
        echo "\t] ;\n";
    }


    echo  "\ta schema:Movie .\n\n";

    

}


// Now, episodes:

$sql = "select * 
        from tblfilmepisode";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    
    echo "<http://www.cinemacontext.nl/episode/" . $row['episode_id'] . ">\n";

    echo "\trdfs:label \"" . esc($row['title']) . "\" ;\n";
    if(preg_match("/^[0-9]{4}$/", $row['episode_year'])){
        echo "\tschema:dateCreated \"" . $row['episode_year'] . "\"^^xsd:gYear ;\n";
    }
    if(strlen($row['film_length'])){
        echo "\tdcterms:extent [\n";
        echo "\t\tschema:value \"" . $row['film_length'] . "\"^^xsd:int ;\n";
        echo "\t\tschema:unitCode \"MTR\" ;\n";
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

