# The Return of the BallotDrop

## Explain yourself

The Return of the BallotDrop is a gritty reboot of the service provided by [BallotDrop.org](http://ballotdrop.org/).

It's current in development for [JustVoteColorado.org](http://justvotecolorado.org) (that's why it only works in Colorado) but will hopefully be generalized soon for **ANY** state and *maybe* BallotDrop.org will even get a funky-fresh makeover. *Maybe*.

### What does it do?

Unlike BallotDrop.org - which gets its data from file that's saved on the server - Better BallotDrop is 100% powered by Google Spreadsheet, meaning it's really really really easy for normal people to update and maintain the data.

### What's different?

Better Ballot Drop contains two components:

 * A javascript widget for displaying voting locations on a Google Map that are stored in a Google Spreadsheet - the data displayed is what's live in the spreadsheet
 * A generator script for creating and configuring that map for future elections. The generator will be available as website or as a php command line script

It also contains some extra features beyond BallotDrop.org *including*

 * Filtering voting locations by when they open - greying out locations that haven't opened yet
 * Filtering voting locations by type - i.e. ballot drop boxes versus vote centers
 * Configurable icons for the different voting locations types
 * Zooming and filtering by county

### What's on the roadmap?

As you can see this is all just a bunch of mismash - but what's in the pipeline?

 * Automate the generation of the county list and the "starting position" (right now both are hardcoded to Colorado)
 * Finish creating / styling the map generator page - so anyone can create a new BallotDrop on their own (and with minimal programming experience)
 * Remove jQuery dependency or automate adding jQuery
 * Build scipt to assemble js and minify.
 * Should dynamically prepend all Dom elements - county dropdown, search box, map, etc
 * Some simple styling by importing a stylesheet
 * Shouldn't require pregenerating the lat/lngs... somehow
 * Tests! What you shut up
 * Better date filtering... this one is v vague. Maybe an Open Now button like on Yelp?
 * Other language support: format_week function override, date works better
 * [AUTOMATICALLY GEOCODE THINGS CAUSE THE CENSUS CAN DO THAT FOR FREE WITH BATCHES](http://geocoding.geo.census.gov/geocoder/Geocoding_Services_API.pdf)
