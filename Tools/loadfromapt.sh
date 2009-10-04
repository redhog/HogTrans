#! /bin/bash

self="$0"
scriptroot="$(cd "$(dirname $self)"; pwd)"

get_pkg () {
 wget "$1" > /dev/null 2>&1
}

get_files () {
 get_pkg "$1"
 dpkg -x "$(basename "$1")" "$(basename "$1" .deb)" 
}

load_files () {
 get_files "$1"
 package_name="$(dpkg -f "$(basename "$1")" | grep Package: | sed -e "s+Package: ++g")"
 package_version="$(dpkg -f "$(basename "$1")" | grep Version: | sed -e "s+Version: ++g")"
 (
  cd "$(basename "$1" .deb)"
  find . -name "*.mo" |
   while read mofile_path; do
    echo $scriptroot/load.py mofile "$package_name" "$package_version" "$mofile_path"
   done;
 )
 rm -rf "$(basename "$1")" "$(basename "$1" .deb)"
}

get_pkg_urls () {
 apt-get install --reinstall --print-uris --yes "$1" | grep -E "https?:" | sed -e "s+^'\([^']*\)'.*$+\1+g"
}

load_pkg () {
 get_pkg_urls "$1" |
  while read url; do
   load_files "$url"
  done
}

mkdir -p "$1"
cd "$1"
shift

if [ "$1" != "" ]; then
 for pkg in "$@"; do
  load_pkg "$pkg";
 done
else
 apt-cache search "" |
  cut -d " " -f 1 |
  while read pkg; do
   echo "XXXXX $pkg"
   load_pkg "$pkg";
  done
fi
