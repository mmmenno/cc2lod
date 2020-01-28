# CinemaContext

## to do?

- Places (adressen) hebben geen eigen permalink. Is dat erg?
- Class voor 'mobile theater' is nu ook MovieTheater, maar heb daar wd:Q6605486 aan toegevoegd
- first_sound_date is eerste programma met geluid in bioscoop, hoe modelleren we dat?
- type koppeling tussen Movie Theater en company is `parentOrganization`, er was ook nog een veld `activity_type`, maar dat had altijd als waarde 'bios.exploitatie'
- companies hebben altijd 'nl' als land, buiten beschouwing gelaten.


## verbetering database

- Het fatsoeneren van de nieuwe en oude ids in de database zal ooit eens aangepakt moeten worden. Ik zou dit doen als er een nieuwe website gebouwd wordt.
- in tblAddressConstructionHistory als `construction_type` 'sloop' toevoegen
- wikidata ids van gebouwen toevoegen in veld `building_wikidata_id` in tblAddressConstructionHistory

