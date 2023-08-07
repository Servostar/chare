#!/bin/bash

log() {
    local level=$1
    local message=$2
    local timestamp=$(date +"%Y-%m-%d %T")
    echo "[$timestamp] [$level] $message" >> logfile.txt
}

function sync_git() {
    if [ -d "$2" ]; then
      cd "$2" || return
      git pull "$1"
    else
      mkdir -p "$2"
      git clone "$1" "$2"
    fi
}

function sync() {
    if [ ! -f "$1" ]; then
      log "file does not exist"
      return
    fi

    log "syncing..."

    while read line; do
      IFS=' ' read -ra words <<< "$line"

      if [ "${#words[@]}" -lt 3 ]; then
        log "invalid amount of arguments"
        continue
      fi

      # extract required information
      type="${words[0]}"
      url="${words[1]}"
      directory="$SHARE_PATH/${words[2]}"

      if [ "$type" = "git" ]; then
        log "syncing git repository..."
        sync_git "$url" "$directory"
      elif [ "$type" = "curl" ]; then
        log "syncing with curl..."
        curl "$url" "-o $directory"
      fi

    done < "$1"
}

filename="/srv/config/repos.tsv"

while true; do
  log "starting sync job..."
  sync "$filename"
  log "waiting..."
  sleep 15m
done