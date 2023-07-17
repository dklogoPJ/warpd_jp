#!/bin/sh
#
# Ths script is to help start up VV with one command
#
echo "********************** Starting Virtual WarpD **************************"
echo "Bringing up virtual WarpD"
cd ~/www/warpd_jp/
make up
echo "Copying all sql script to warpd-dbase container"
docker cp ../app/db/autorun/ warpd-dbase:/
echo "Executing all sql script in warpd-dbase container"
#docker exec dbase /bin/sh -c 'mysql -u root -ppassword -D venus <configure_virtual_venue.sql'
echo "!!!!!! Virtual WarpD Is Up And Ready !!!!!!!"
