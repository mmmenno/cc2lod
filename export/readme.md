# Exportscripts

For each entity, a script queries the MySQL database, converts the data to rdf and outputs it as a turtle file. Sometimes, most notably in [export-programmes.php](export-programmes.php), this is a rather timeconsuming process - largely due to a wondrous system of replacing ids with new ids, but only for presentation.

The resulting turtle looks like this:

## companies

```
<http://www.cinemacontext.nl/id/R000038>
	rdfs:label "Tuschinski Maatschappij NV" ;
	schema:description "Beheerde sinds 1936 het onroerend goed van het Tuschinski-concern, terwijl NV Tubem de exploitatie deed. In 1973 omgezet in BV Tuschinski Theaters." ;
	sem:hasEarliestBeginTimeStamp "1936-01-01"^^xsd:date ;
	sem:hasLatestBeginTimeStamp "1936-12-31"^^xsd:date ;
	sem:hasEarliestEndTimeStamp "1973-01-01"^^xsd:date ;
	sem:hasLatestEndTimeStamp "1973-12-31"^^xsd:date ;
	pext:activeInSector [
		rdfs:label "bioscoopexploitatie" ;
		a pext:IndustrySector ;
	] ;
	a schema:Organization .
```

## persons

```
<http://www.cinemacontext.nl/id/P000007>
	rdfs:label "F.E. Brave" ;
	pnv:hasName [
		pnv:literalName "F.E. Brave" ;
		pnv:givenName "F.E." ;
		pnv:surname "Brave" ;
	] ;
	schema:worksFor [
		a schema:OrganizationRole ;
		schema:roleName "directielid" ;
		schema:worksFor <http://www.cinemacontext.nl/id/R000055> ;
		sem:hasEarliestBeginTimeStamp "1956-01-01"^^xsd:date ;
		sem:hasLatestBeginTimeStamp "1956-12-31"^^xsd:date ;
		sem:hasEarliestEndTimeStamp "1957-01-01"^^xsd:date ;
		sem:hasLatestEndTimeStamp "1957-12-31"^^xsd:date ;
	] ;
	a schema:Person .
```


## theaters

```
<http://www.cinemacontext.nl/id/B000006>
	rdfs:label "Cineac Damrak" ;
	schema:location <http://www.cinemacontext.nl/place/Perc8> ;
	schema:parentOrganization [
		schema:parentOrganization <http://www.cinemacontext.nl/id/R000007> ;
		sem:hasEarliestBeginTimeStamp "1938-01-01"^^xsd:date ;
		sem:hasLatestBeginTimeStamp "1938-12-31"^^xsd:date ;
		sem:hasEarliestEndTimeStamp "1983-01-01"^^xsd:date ;
		sem:hasLatestEndTimeStamp "1983-12-31"^^xsd:date ;
	] ;
	a schema:MovieTheater .
```


## places

```
<http://www.cinemacontext.nl/place/id1104540487036>
	geo:hasGeometry [
		geo:asWKT "POINT(4.911206 52.366434)" ;
	] ;
	schema:address [
		schema:streetAddress "Plantage Middenlaan 24 " ;
		schema:addressLocality "Amsterdam" ;
	] ;
	schema:description "Hollandsche Schouwburg" ;
	a schema:Place .
```

## construction history

```
<http://www.cinemacontext.nl/constructionevent/Perc188-01>
	sem:hasPlace <http://www.cinemacontext.nl/address/Perc188> ;
	dc:type "Nieuwbouw" ;
	sem:hasActor [
		rdf:value <http://www.cinemacontext.nl/id/P002395> ;
		sem:roleType [
			rdfs:label "architect" ; 
			a sem:RoleType ;
		] ;
		a sem:Role ;
	] ;
	a sem:Event .
```

## programmes

```
<http://www.cinemacontext.nl/id/V082378>
	schema:startDate "1937-08-13"^^xsd:date ;
	schema:location <http://www.cinemacontext.nl/id/B000696> ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F009244> ;
		schema:position "1"^^xsd:int ;
		schema:alternateName "Geheim van de blauwe kamer, Het" ;
		schema:inLanguage "NL" ;
		a schema:ScreeningEvent ;
	] ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F009097> ;
		schema:position "2"^^xsd:int ;
		schema:alternateName "Bij de blonde Kathrien" ;
		schema:inLanguage "NL" ;
		a schema:ScreeningEvent ;
	] ;
	a schema:Event .
```


## films

```
<http://www.cinemacontext.nl/id/F002877>
	rdfs:label "Daydreams (1922)" ;
	schema:description "Buster Keaton" ;
	schema:dateCreated "1922"^^xsd:gYear ;
	owl:sameAs <https://www.imdb.com/title/tt0013055> ;
	schema:countryOfOrigin "USA" ;
	dcterms:extent "800" ;
	dcterms:format "35mm" ;
	a schema:Movie .
```