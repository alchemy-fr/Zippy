#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
NODE_SERVER_PIDFILE="$DIR/node_server.pid"

test $# -eq 0 && { echo "Usage: $0 start|stop [node_bin]"; exit 1; }

case $1 in
  start)
    NODE=${2-node}
    test -r $NODE_SERVER_PIDFILE && echo "Previously registered pid: $(cat $NODE_SERVER_PIDFILE)"
    $NODE "$DIR/static-file-server.js" &
    echo $! > "${NODE_SERVER_PIDFILE}"
    echo "Server new pid: $(cat $NODE_SERVER_PIDFILE)"
    ;;
  stop)
    test -r $NODE_SERVER_PIDFILE && kill $(cat $NODE_SERVER_PIDFILE)
    rm -f $NODE_SERVER_PIDFILE
    ;;
esac

exit 0;
