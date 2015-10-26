# Please do not manually call this file!
# This script is run by the docker container when it is "run"

service mongod start


# Start the cron service in the foreground
cron -f
