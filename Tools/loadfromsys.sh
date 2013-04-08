#! /bin/bash

mofile_path="$1"

load_mofile () {
 mofile_path="$1"
 echo "Loading $mofile_path..."
 package_name="$(dpkg -S "$mofile_path" | cut -d ":" -f 1)"
 package_version="$(dpkg -s "$package_name" | grep -e "^Version: " | cut -d " " -f 2)"
 ./load.py mofile "$package_name" "$package_version" "$mofile_path"
}

if [ "$mofile_path" != "" ]; then
 load_mofile "$mofile_path"
else
 find /usr/share/ -name "*.mo" |
  while read mofile_path; do
   load_mofile "$mofile_path"
  done
fi
