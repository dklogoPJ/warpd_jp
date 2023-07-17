#!/bin/sh
#
# Ths script is to help start up VV with one command
#
echo "********************** Shutting Down Starting Virtual WarpD **************************"
cd ~/www/warpd_jp/
make down
echo "!!!!!!! Virtual WarpD Is Down !!!!!!"
exec $SHELL