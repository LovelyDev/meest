NAME="server.js" # nodejs script's name here
RUN=`pgrep -f $NAME`

if [ "$RUN" == "" ]; then
  echo "Daemon will run the chat server"
  forever resources/js/server/server.js
else
 echo "Script is running"
fi
