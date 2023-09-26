# Chare
Chare is a file hosting service with a builtin syncing feature for various protocols such as https and git.
It is essentially a more fancy web folder explorer written in PHP. The CSS theme is inspired by GitHub. It was designed
to be fast and lightweight in use.
## Features
- GitHub like ui
- Serving files via HTTP
- Syncing of HTTP(s) via `curl`
- Syncing with `git`
- Excluding files via a list
- Show information about folders such as used license, code of conduct or remote URL of git repository
- Render a readme file with `CommonMark`
- Download folder using HTTP link or ZIP file

## Demo
![Image preview of gui](https://github.com/Servostar/chare/blob/main/images/demo.png)

Demo site can be visited at: [demo](https://cdn.montehaselino.de)

## Deployment
The recommended way is to run chare as docker container: `docker run servostar/chare -p 80:80`.
An example configuration via docker compose can be found in the `example` folder.
Example docker-compose:
```yaml
version: "3.1"
services:
  chare:
      image: servostar/chare:latest
      restart: unless-stopped
      ports:
        - "8080:80"
      volumes:
        - ./public:/var/share
        - ./repos.tsv:/srv/config/repos.tsv
        - ./.ignore:/srv/config/.ignore
      environment:
        # should be setup if running behind reverse proxy
        - OVERWRITE_URL = https://cdn.example.com
        # these are all optional
        - OVERWRITE_SERVER_NAME = cdn.example.com
        - HOME_PAGE = https://home.example.com
        - LEGAL_PAGE = https://home.example.com
        - IMPRESSUM_PAGE = https://legal.example.com
```
## Dependencies for manual install
If installing manually chare requires php 6.0+ and the package manager compose for downloading plugins.
`curl` and `git` should also be available.
## Configuration
Chare can mostly be configured through environment variables:
### Environment Variables
- `OVERWRITE_URL`: the public url of this instance (required if running behind reverse proxy)
- `OVERWRITE_SERVER_NAME`: domain name of ip address of this server (only used for the ui)
- `HOME_PAGE`: optional link to a homepage that will be shown as a button above the file listing
- `LEGAL_PAGE`: optional link to a legal that will be shown as a button above the file listing
- `IMPRESSUM_PAGE`: optional link to an impressum that will be shown as a button above the file listing
### Syncing files and repositories
Chare has the ability to automatically sync folder, files and repositories in set intervals.
These must be configured in a file mounted at `/srv/config/repos.tsv`. The format is as follows:

`protocol`  `url`   `path`

This is a single entry. Every entry is contained within a single line. An entry is made out of the protocol/program to use,
the target url to fetch from and the destination path to download to.
Every entry has to be separated by a single tab. Adding multiple entries can be done by adding a new line.
Available protocols are currently:
- `git` will use git to clone any git compatible repository
- `curl` will use curl to download anything over http(s)

The specified path is always relative to the public share folder. Meaning if the public share folder is `/var/share` then
the path `downloads/images` will resolve to the path `/var/share/downloads/images`.
Example configuration:
```tsv
git https://github.com/Servostar/chare.git  repos/chare
git https://user:token@git.myinstance.com/user/coolstuff.git repos/coolstuff
curl https://google.com downloads/websites

```
### Ignoring specific files
Chare can be instructed to not show specific files in the web ui. These files are listed in the file at `/srv/config/.ignore`.
This file contains a list of file and folders to ignore. Every file will be matched against this list to see if it is to be ignored.
Example file:
```
.
.gitignore
.git
.config
.extra
```
NOTE: this file does not allow for comments like a .gitignore file!
### Public share path

### HTTPS and security
Chare does not provide any SSL capabilities out house, thus you should deploy chare behind a reverse proxy which handles encryption.
