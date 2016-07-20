#!/usr/bin/env bash
while true; do
 curl -I "http://localhost/rs/pgoweb/rest/clearExpired";
 sleep 20;
done
