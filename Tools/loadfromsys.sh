#! /bin/bash

mofile_path="$1"
package_name="$(dpkg -S "$mofile_path" | cut -d ":" -f 1)"
package_version="$(dpkg -s "$package_name" | grep -e "^Version: " | cut -d " " -f 2)"

./load.py mofile "$package_name" "$package_version" "$mofile_path"
