#!/bin/bash

function sync_git() {
    if [ -d "$2" ]; then
      cd "$2" || return
      git pull "$1"
    else
      git clone "$1" "$2"
    fi
}

function sync() {
    if [ ! -f "$1" ]; then
      echo "file does not exist"
      return
    fi

    echo "syncing..."

    while read line; do
      IFS=' ' read -ra words <<< "$line"

      if [ "${#words[@]}" -lt 3 ]; then
        echo "invalid amount of arguments"
        continue
      fi

      # extract required information
      type="${words[0]}"
      url="${words[1]}"
      directory="$SHARE_PATH/${words[2]}"

      if [ "$type" = "git" ]; then
        echo "syncing git repository..."
        sync_git "$url" "$directory"
      elif [ "$type" = "curl" ]; then
        echo "syncing with curl..."
        curl "$url" "-o $directory"
      fi

    done < "$1"
}

filename="/srv/config/repos.tsv"

while true; do
  echo "starting sync job..."
  sync "$filename"
  echo "waiting..."
  sleep 15m
done