#!/usr/bin/env bash
while true; do
 curl -I "http://104.155.3.200/pgoweb/rest/clearExpired";
 sleep 20;
done
