#!/bin/bash
cd /home/user/VoxTrade
while true; do
    /usr/bin/php8.4 artisan schedule:run >> /home/user/VoxTrade/storage/logs/scheduler.log 2>&1
    sleep 60
done
