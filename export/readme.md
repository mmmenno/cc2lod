# Exportscripts

For each entity, a script queries the MySQL database, converts the data to rdf and outputs it as a turtle file. Sometimes, most notably in [export-programmes.php](export-programmes.php), this is a rather timeconsuming process - largely due to a wondrous system of replacing ids with new ids and using them both.

The resulting turtle looks like this:

## companies

```
<http://www.cinemacontext.nl/id/R000017>
	schema:name "Eindhovensche Bioscope Maatschappij NV" ;
	sem:hasEarliestBeginTimeStamp "1913-07-01"^^xsd:date ;
	sem:hasLatestBeginTimeStamp "1913-07-31"^^xsd:date ;
	schema:parentOrganization [
		schema:parentOrganization <http://www.cinemacontext.nl/id/R000180> ;
		schema:description "Eindhovense Bioscope Mij. neemt in 1939 de aandelen van D. Hamburger over." ;
		sem:hasEarliestBeginTimeStamp "1929-01-01"^^xsd:date ;
		sem:hasLatestBeginTimeStamp "1929-12-31"^^xsd:date ;
		a schema:Role ;
	] ;
	pext:activeInSector [
		schema:name "bioscoopexploitatie" ;
		a pext:IndustrySector ;
	] ;
	gleio:hasLegalForm [
		a gleio:LegalForm ;
		skos:altLabel "N.V." ;
		gleio:hasEntityLegalFormCode "B5PM" ;
	] ;
	schema:citation [
		schema:citation <http://www.cinemacontext.nl/id/publication/id1151255550914> ;
		a schema:Role ;
	] ;
	a schema:Organization .
```

## persons

A person might work for a company.

```
<http://www.cinemacontext.nl/id/P000015>
	schema:name "mevr. W. Gunneman" ;
	schema:gender schema:Female ;
	pnv:hasName [
		pnv:literalName "W. Gunneman" ;
		pnv:initials "W." ;
		pnv:surname "Gunneman" ;
		a pnv:PersonName ;
	] ;
	schema:worksFor [
		a schema:OrganizationRole ;
		schema:roleName "bedrijfsleider" ;
		schema:worksFor <http://www.cinemacontext.nl/id/B000006> ;
		sem:hasEarliestBeginTimeStamp "1977-01-01"^^xsd:date ;
		sem:hasLatestBeginTimeStamp "1977-12-31"^^xsd:date ;
	] ;
	a schema:Person .
```


## theaters

Theaters, or 'venues' are organizations existing on a specific location with a specific name. Usually they are owned by a company.

```
<http://www.cinemacontext.nl/id/B000006>
	schema:name "Cineac Damrak" ;
	schema:location <http://www.cinemacontext.nl/id/place/Perc8> ;
	schema:parentOrganization [
		schema:parentOrganization <http://www.cinemacontext.nl/id/R000007> ;
		sem:hasEarliestBeginTimeStamp "1938-01-01"^^xsd:date ;
		sem:hasLatestBeginTimeStamp "1938-12-31"^^xsd:date ;
		sem:hasEarliestEndTimeStamp "1983-01-01"^^xsd:date ;
		sem:hasLatestEndTimeStamp "1983-12-31"^^xsd:date ;
		a schema:Role ; 
	] ;
	schema:temporalCoverage [
		sem:hasEarliestBeginTimeStamp "1938-03-17"^^xsd:date ;
		sem:hasLatestBeginTimeStamp "1938-03-17"^^xsd:date ;
		sem:hasEarliestEndTimeStamp "1983-07-06"^^xsd:date ;
		sem:hasLatestEndTimeStamp "1983-07-06"^^xsd:date ;
	] ;
	schema:screenCount [
		schema:screenCount "2"^^xsd:int ;
		sem:hasEarliestBeginTimeStamp "1975-01-01"^^xsd:date ;
		sem:hasLatestBeginTimeStamp "1975-01-31"^^xsd:date ;
		a schema:Role ;
	] ;
	schema:screenCount [
		schema:screenCount "1"^^xsd:int ;
		sem:hasEarliestBeginTimeStamp "1938-01-01"^^xsd:date ;
		sem:hasLatestBeginTimeStamp "1938-12-31"^^xsd:date ;
		a schema:Role ;
	] ;
	schema:maximumAttendeeCapacity [
		schema:maximumAttendeeCapacity "750"^^xsd:int ;
		sem:hasLatestBeginTimeStamp "1938-12-31"^^xsd:date ;
		a schema:Role ; 
	] ;
	schema:citation [
		schema:citation <http://www.cinemacontext.nl/id/publication/Pub165> ;
		a schema:Role ; 
	] ;
	a schema:MovieTheater .
```

### mobile theaters

Mobile theaters are like MovieTheaters, in the sense that you could go there to see a movie. And they are located at a schema:Place, at least at a specific moment in time. Mobile cinemas have an additionalType of `wd:Q6605486` (mobile cinema) as well.

```
<http://www.cinemacontext.nl/id/B001973>
	schema:name "Mobile Theatre" ;
	schema:location <http://www.cinemacontext.nl/id/place/id1140887559002> ;
	schema:additionalType wd:Q6605486 ;
	a schema:MovieTheater .
``` 
### eventvenues

A `schema:EventVenue` might be a hotel, a concerthall or even a church.

```
<http://www.cinemacontext.nl/id/B001744>
	schema:name "Hollandsche Schouwburg" ;
	schema:location <http://www.cinemacontext.nl/id/place/id1104540487036> ;
	a schema:EventVenue .
```


## places

A 'place' is just that: a point on the map. Sometimes a place might have a 1:1 relation with a building, but it's perfectly possible there were several buildings on that spot, over time.

```
<http://www.cinemacontext.nl/id/place/id1104540487036>
	geo:hasGeometry [
		geo:asWKT "POINT(4.911206 52.366434)" ;
	] ;
	schema:address [
		schema:streetAddress "Plantage Middenlaan 24" ;
		schema:addressLocality "Amsterdam" ;
		a schema:PostalAddress ;
	] ;
	schema:description "Hollandsche Schouwburg" ;
	a schema:Place .
```

## construction history

Construction events include the dc:types 'Nieuwbouw' and 'Verbouwing'. Constructionevents are of the class `sem:Event`, which helps to distinguish these events from 'programmes' - modelled as `schema:Event`.

```
<http://www.cinemacontext.nl/id/constructionevent/Perc188-01>
	sem:hasPlace <http://www.cinemacontext.nl/id/place/Perc188> ;
	dc:type "Nieuwbouw" ;
	sem:hasEarliestBeginTimeStamp "1914-01-01"^^xsd:date ;
	sem:hasLatestBeginTimeStamp "1914-12-31"^^xsd:date ;
	sem:hasEarliestEndTimeStamp "1914-01-01"^^xsd:date ;
	sem:hasLatestEndTimeStamp "1914-12-31"^^xsd:date ;
	sem:hasActor [
		rdf:value <http://www.cinemacontext.nl/id/P002395> ;
		sem:roleType [
			schema:name "architect" ; 
			a sem:RoleType ;
		] ;
		a sem:Role ;
	] ;
	a sem:Event .
```

Surprisingly, when a building is demolished, often the dc:type 'Verbouwing' is used.

```
<http://www.cinemacontext.nl/id/constructionevent/id1114084252705-03>
	sem:hasPlace <http://www.cinemacontext.nl/id/place/id1114084252705> ;
	dc:type "Verbouwing" ;
	sem:hasEarliestBeginTimeStamp "1940-05-14"^^xsd:date ;
	sem:hasLatestBeginTimeStamp "1940-05-14"^^xsd:date ;
	sem:hasEarliestEndTimeStamp "1940-05-14"^^xsd:date ;
	sem:hasLatestEndTimeStamp "1940-05-14"^^xsd:date ;
	schema:description "verwoest bij bombardement" ;
	a sem:Event .
```


## programmes

A 'programme' is a `schema:Event` consisting of one or more `schema:ScreeningEvent`s, that was held in a specific theater on one or more dates. Sometimes a film was screened under another name (a Dutch name, for example). In such cases this name is mentioned as a `schema:alternateName`.

```
<http://www.cinemacontext.nl/id/V062775>
	schema:startDate "1918-10-25"^^xsd:date ;
	schema:location <http://www.cinemacontext.nl/id/B001452> ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F031808> ;
		schema:position "1"^^xsd:int ;
		schema:alternateName "Regimentsdochter, De" ;
		schema:inLanguage "NL" ;
		a schema:ScreeningEvent ;
	] ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F007737> ;
		schema:position "2"^^xsd:int ;
		schema:alternateName "Prinses van Neutralië, De" ;
		schema:inLanguage "NL" ;
		a schema:ScreeningEvent ;
	] ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F011327> ;
		schema:position "3"^^xsd:int ;
		schema:alternateName "Medaillon, Het" ;
		schema:inLanguage "NL" ;
		a schema:ScreeningEvent ;
	] ;
	schema:citation [
		schema:citation <http://www.cinemacontext.nl/id/publication/id1113821541222> ;
		schema:description "Krant uit de week van de voorstelling" ;
		a schema:Role ;
	] ;
	a schema:Event .
```

If the location where the schema:Event was held is a mobile theater, the event is likely to have a `schema:organizer`, presumably the mobile theater company.

```
<http://www.cinemacontext.nl/id/V087704>
	schema:startDate "1903-05"^^xsd:gYearMonth ;
	rdfs:label """Mobile Theatre""" ;
	schema:description """Sint Servaaskermis""" ;
	schema:location <http://www.cinemacontext.nl/id/stand/Venid1136236790135> ;
	schema:organizer <http://www.cinemacontext.nl/id/R001354> ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F032368> ;
		schema:alternateName "Soesman en Zwaaf zingen Bokkie-Bè" ;
		schema:inLanguage "NL" ;
		a schema:ScreeningEvent ;
	] ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F032428> ;
		schema:alternateName "Paus Leo XIII in zijn laatste levensdagen" ;
		schema:inLanguage "NL" ;
		a schema:ScreeningEvent ;
	] ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F032389> ;
		a schema:ScreeningEvent ;
	] ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F032461> ;
		a schema:ScreeningEvent ;
	] ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F032464> ;
		a schema:ScreeningEvent ;
	] ;
	schema:subEvent [
		schema:workPresented <http://www.cinemacontext.nl/id/F032507> ;
		a schema:ScreeningEvent ;
	] ;
	a schema:Event .
```


## films

For 'distributors', the property `schema:publisher` is used.

```
<http://www.cinemacontext.nl/id/F002877>
	schema:name "Daydreams (1922)" ;
	schema:description "Buster Keaton" ;
	schema:dateCreated "1922"^^xsd:gYear ;
	schema:sameAs <https://www.imdb.com/title/tt0013055> ;
	schema:countryOfOrigin "USA" ;
	dcterms:extent [
		schema:value "800"^^xsd:int ;
		schema:unitCode "MTR" ;
		a schema:PropertyValue ;
	] ;
	dcterms:format "35mm" ;
	schema:genre "fiction" ;
	schema:publisher [
		schema:publisher <http://www.cinemacontext.nl/id/R000097> ;
		a schema:Role ; 
	] ;
	a schema:Movie .
```


## ratings

Films might be rated by a `schema:Rating`. These ratings might have a `schema:ratingValue` of either 'recommended' or 'not recommended', a `schema:text` and a `schema:ratingExplanation`. In the `schema:about` nodes, names and descriptions of the film as written in the rating report are mentioned.

```
<http://www.cinemacontext.nl/id/F020122>
	schema:contentRating <http://www.cinemacontext.nl/id/rating/Dossier10001> . 

<http://www.cinemacontext.nl/id/rating/Dossier10001>
	schema:identifier "V1308" ;
	schema:dateCreated "1953-09-15"^^xsd:date ;
	schema:author <http://www.cinemacontext.nl/id/R001681> ;
	schema:text "18 jaar" ;
	schema:ratingValue "not recommended" ;
	schema:ratingExplanation "te sensationeel voor onder de 18" ;
	schema:about [
		a schema:Role ;
		schema:name "Vrijdag, de dertiende" ;
		schema:description "r. Arthur Lubin, Boris Karloff" ;
		schema:about <http://www.cinemacontext.nl/id/F020122> ;
	] ;
	schema:about [
		a schema:Role ;
		schema:name "Black Friday" ;
		schema:about <http://www.cinemacontext.nl/id/F020122> ;
	] ;
	a schema:Rating, schema:CreativeWork .
```