#!/bin/bash

if [ $# -eq 0 ]; then
    echo "Current Package Version Not Provided"
    exit 1
fi

if [ $# -eq 1 ]; then
    echo "Target Package Version Not Provided"
    exit 1
fi

echo "Create Product Package ${2}"

CUR_DIR=$PWD
PAR_DIR="$(dirname "$CUR_DIR")"
DIS_DIR="${PAR_DIR}/packages"

if [[ ! -e $DIS_DIR ]]; then
    mkdir "${DIS_DIR}"
elif [[ ! -d $DIS_DIR ]]; then
    rm -rf "${DIS_DIR}"
    mkdir "${DIS_DIR}"
else
    echo "$DIS_DIR already exists."
fi

if [[ -d "${DIS_DIR}/${CUR_DIR##*/}" ]]; then
    echo "Remove Directory ${CUR_DIR##*/}"
    rm -rf "${DIS_DIR}/${CUR_DIR##*/}"
fi

echo "Update package version on all files"

find "app/" -name "*.php" -exec sed -i "s/version     ${1}/version     ${2}/g" '{}' \;
find "database/seeders" -name "GlobalSettingSeeder.php" -exec sed -i "s/${1}/${2}/g" '{}' \;

echo "Minify Css and Js files"

npm run production

echo "Creating Package On ${DIS_DIR}"

cp -R "${CUR_DIR}" "${DIS_DIR}"

FINAL_DIR="${DIS_DIR}/${CUR_DIR##*/}"

cd "${FINAL_DIR}"

echo "Remove Dummy Files on $PWD"

php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan clear-compiled

if [ -d "${FINAL_DIR}/node_modules" ]; then rm -rf "${FINAL_DIR}/node_modules"; fi
if [ -d "${FINAL_DIR}/.git" ]; then rm -rf "${FINAL_DIR}/.git"; fi
if [ -d "${FINAL_DIR}/.idea" ]; then rm -rf "${FINAL_DIR}/.idea"; fi
if [ -f "${FINAL_DIR}/storage/installed" ]; then rm "${FINAL_DIR}/storage/installed"; fi
if [ -f "${FINAL_DIR}/.env" ]; then rm "${FINAL_DIR}/.env"; fi
if [ -f "${FINAL_DIR}/package.sh" ]; then rm "${FINAL_DIR}/package.sh"; fi

find "${FINAL_DIR}/storage/logs/" ! -name '.gitignore' -type f -exec rm -f {} +

rm -r "${FINAL_DIR}/public/images/hotels/"*/ "${FINAL_DIR}/public/images/users/"*/ "${FINAL_DIR}/public/images/payout_documents/"*/

cp "${FINAL_DIR}/.env.example" "${FINAL_DIR}/.env"

if [ -f "${FINAL_DIR}/database/seeders/UserSeeder.php" ]; then rm "${FINAL_DIR}/database/seeders/UserSeeder.php"; fi
if [ -f "${FINAL_DIR}/database/seeders/HotelSeeder.php" ]; then rm "${FINAL_DIR}/database/seeders/HotelSeeder.php"; fi
if [ -f "${FINAL_DIR}/database/seeders/ReservationSeeder.php" ]; then rm "${FINAL_DIR}/database/seeders/ReservationSeeder.php"; fi

find "app/Providers" -type f -name "ShareServiceProvider.php" -exec sed -i '/Load All JS & CSS without Cache$/d' '{}' \;
find "database/seeders" -type f -name "DatabaseSeeder.php" -exec sed -i -e '/.*PackageCommentStart.*/,+7d' '{}' \;

echo "Creating Archives for ${CUR_DIR##*/} - ${2}"

if [ -f "${DIS_DIR}/${CUR_DIR##*/}-${2}.zip" ]; then rm "${DIS_DIR}/${CUR_DIR##*/}-${2}.zip"; fi

zip -r "${DIS_DIR}/${CUR_DIR##*/}-${2}.zip" .

chmod -R 777 ${DIS_DIR}

rm -rf ${FINAL_DIR} 

echo " ${CUR_DIR##*/} - ${2} Package Archives Created Successfully"
