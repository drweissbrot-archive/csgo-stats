# csgo-stats
This is a match history plattform for Counter-Strike: Global Offensive.  
Essentially, it shows a scoreboard and some more detailed info about CS:GO matches, as well as some performance metrics for players and teams.

All data is provided by CS:GO demos. These have to be imported by the site maintainer; there's no uploading demos or anything.

I've built this primarily for my friends and myself, so don't expect any customizability or anything.

You can find my instance of this project on https://csgo.drweissbrot.net.

## Setup
You'll need Docker (tested on version 20.10) and Docker Compose (version 1.29 or greater).

Clone this repository, `cd` into the directory, and run `docker-compose up`.  
In a separate terminal, `cd` into the directory again and run `docker-compose exec app bash` to log into the container. Run `composer install`, and copy `.env.example` to `.env`. Then run `php artisan key:generate` to generate an app key.  
The actual demo parsing is done by a JavaScript script, so install the dependencies via Yarn, `yarn --prod` (or without the `--prod` if you prefer).

### Config
Into the `.env` file, add your Steam API Key (from https://steamcommunity.com/dev/apikey).

If you want, you can also add the Steam2 IDs for all of your accounts (`OWNER_STEAM_IDS`), so any stats of your smurf accounts will be added to your main account instead (provide your main accounts Steam ID first, any others afterwards, comma-separated). If you provide `OWNER_STEAM_FLAG` and `OWNER_TEAM_NAMES`, a team using the provided flag or any of the provided names will be displayed on the left-hand side (or on the top) of match pages and such.

It should look something like this:
```env
// ...

OWNER_STEAM_IDS="STEAM_1:1:53558216,STEAM_1:0:56997699"
OWNER_STEAM_FLAG=de
OWNER_TEAM_NAMES="My Awesome Team Name,AWESOME TEAM,AWESOME"

STEAM_API_KEY=FVTDEYMLKB3BWNSGJZ2TAZFYGA3GH3SB

// ...
```

## Initializing the Database
Run `php artisan db:seed`. This will create some maps in the database (as well as a fallback user in case of some weird demo shenanigans).

Next, you'll need to create a ladder. Run `php artisan tinker`, and type `App\Ladder::create(['name' => 'Competetive Matchmaking']);`.

## Importing a Match
Head over to your demo import directory (`DEMO_INTAKE` in the `.env` file, i.e. `/csgo/stats/intake` in my case).

Create a subdirectory called `Competetive Matchmaking`, and inside that a subdirectory called `BO1 _1`. Get a demo, put it inside that folder, and name it according to this format `1970-01-01 01.00.00.dem`. The file name should be the time the match started, in UTC.

Now, back in the project folder, run `php artisan demo:import`, and once that's done, you should see your match listed in the software.

### More Details
To create series consisting of multiple matches, name the directory `BO2 _1`, `BO3 _1`, `BO5 _1`, etc. instead.

You can provide a title for the series by replacing the `_1` part with the title, e.g. `BO5 Group A Opening`.

If you want to import multiple matches/series without a name, use `BO1 _2`, `BO1 _3`, etc. As long as the "name" starts with an `_`, it will be ignored.

You can add notes to a series by creating a text file inside the series directory (e.g. `BO1 _1`) with the series name, followed by the `.txt` extension (e.g. `_1.txt` or `Group A Opening.txt`). The contents of this text file will be displayed as series notes on the series page.

Similarly, create a text file with the demo filename followed by `.txt` (e.g. `1970-01-01 01.00.00.dem.txt`) to add notes to a match. These will be displayed on the match page.

You can mark matches as "knife rounds", to prevent them from showing up in series overviews and from being considered for stats (i.e., kills in these matches won't count for a players total K/D ratio across all matches they've played). To do that, append ` knife` to the demo file name (e.g. `1970-01-01 01.00.00 knife.dem`). If you do this, and also want to add notes, you need to include the ` knife` in the text file's name (e.g. `1970-01-01 01.00.00 knife.dem.txt`).

## Automatically import demos
This is mostly provided by Laravel. You'll need to add an entry to your cron file:

```
* * * * * cd /path/to/csgo-stats-directory && docker-compose exec app php artisan schedule:run >> /dev/null 2>&1
```

This will, as provided in [app/Console/Kernel.php](app/Console/Kernel.php), import demos every night at 04:00 am Central European (Daylight) Time, and import Steam profile data (such as flags) an hour later.
