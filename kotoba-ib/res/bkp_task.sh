#!/bin/bash

# Default backup delay is 1 hour
let "BKP_DELAY = 60 * 60"

while true; do
    echo "Create backup `date +%F\ %T` kotoba bkp.sql..."
    ./dump\ base\ bkp.sh
    echo "Wait $BKP_DELAY seconds to next backup..."
    sleep $BKP_DELAY
done