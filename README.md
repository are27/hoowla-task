# hoowla-task
Technical task for Hoowla

## Setup
Should run with little configuration changes on a typical LAMP setup - just need to adjust the host / path info on line 3 of web/index.php to reflect the host machine.

## Details
The API calls are implented in plain PHP with an SQLite backed supporting the following calls
- listDrivers
- getDriver
- updateDriver
- addDriver

getDriver takes an id variable and updateDriver & addDriver will take a data json array containing name,age,team (and id in the case of update)

Bootstrapp based web front end was done just to test the API backend and uses Mustache templating engine as well as AlpacaJS to do quick form and data display