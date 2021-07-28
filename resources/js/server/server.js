require('dotenv').config();
const axios = require('axios');
const find = require('lodash/find');
const filter = require('lodash/filter');
const forEach = require('lodash/forEach');
const remove = require('lodash/remove');
const isEqual = require('lodash/isEqual');
const isEmpty = require('lodash/isEmpty');
const colors = require('colors');

var app = require('express')();
const port = process.env['MIX_CHAT_PORT'];
const domain = process.env['MIX_CHAT_DOMAIN'];

const server = app.listen(port, function () {
  console.log(`Chat horizon server is running on port ${port}`);
});

const io = require('socket.io')(server);
let clientRoomMap = [];

io.on('connection', function (socket) {
  console.log(`connecting socket ${socket.id}`.bgGreen.bold);
  io.emit('UPDATE_USERS_SOCKET_ID', socket.id);

  /**
   * Sending message to a room.
   */
  socket.on('SEND_MESSAGE', function (data) {
    let message = data;
    message.type = 'message';

    io.to(data.roomId).emit('MESSAGE', data);
    console.log(`brodcasting to room ${data.roomId}`);
  });

  /**
   * A user socket is disconnecting.
   */
  socket.on('disconnect', function (data) {
    console.log(`disconnecting socket ${socket.id}`.bgRed.bold);

    // We check if the disconnecting socket is a room reference.
    const room = find(clientRoomMap, ['socketId', socket.id]);

    if (!isEmpty(room)) {
      axios.delete(`${domain}/api/rooms/tenants/${room.user.aliasId}`, {
        headers: {
          Authorization: `Bearer ${room.token}`,
        }
      })
        .then((response) => {
          leaveRoom(room);
        })
        .catch((response) => {
          console.log(response);
        })
    }
  });

  /**
   * Join room.
   */
  socket.on('JOIN_ROOM', function (data) {
    console.log(`Room ${data.roomId} is using socket ${socket.id}`.cyan);
    console.log(`${data.user.name} wants to join room ${data.roomId}`);
    console.log(data);

    axios({
      method: 'post', //you can set what request you want to be
      url: `${domain}/api/rooms/${data.roomId}/tenants`,
      data: data.user,
      headers: {
        Authorization: `Bearer ${data.token}`,
      }
    })
      .then((response) => {
        // Join user to room.
        socket.join(data.roomId);

        // Send message to room that a user has joined.
        io.to(data.roomId).emit('USER_JOINED', data);

        // We add user and room id in the clients room list.
        let userRoom = find(clientRoomMap, {
          'room.id': data.user.id,
          'user.id': data.roomId,
          'user.aliasId': data.user.alias_id
        });

        if (isEmpty(userRoom)) {
          clientRoomMap.push({
            socketId: socket.id,
            token: data.token,
            room: {
              id: data.room.id,
              name: data.room.name,
            },
            user: {
              id: data.user.id,
              name: data.user.name,
              aliasId: data.user.alias_id,
              alias: data.user.alias,
            }
          });
        }

        // Send message globally so we can trigger room list refresh.
        io.emit('REFRESH_ROOM_LIST', '');

        // Send server connection stats.
        io.emit('CONNECTION_STATS', {roomsMap: clientRoomMap});

        console.log(`User ${data.user.name} (${data.user.alias}) has joined room ${data.roomId}`.green);
      })
      .catch((response) => {
        console.log(`Error joining ${data.user.name} to room ${data.roomId}`.red);
      })
  });

  /**
   * A suser leaves a room.
   */
  socket.on('LEAVE_ROOM', function (data) {
    leaveRoom(data);
  });

  /**
   * User leaves a room.
   *
   * @param data
   */
  function leaveRoom(data) {
    axios.delete(`${domain}/api/rooms/${data.room.id}/tenants/${data.user.alias_id}`, {
      headers: {
        Authorization: `Bearer ${data.token}`,
      }
    })
      .then((response) => {
        io.to(data.room.id).emit('USER_LEAVE', data);
        socket.leave(data.room.id);

        // We remove the user in the room list.
        remove(clientRoomMap, function (item) {
          return isEqual(item.user.id, data.user.id) && isEqual(item.room.id, data.room.id);
        });

        // Send message globally so we can trigger room list refresh.
        io.emit('REFRESH_ROOM_LIST', '');

        // Send server connection stats.
        io.emit('CONNECTION_STATS', {roomsMap: clientRoomMap});

        console.log(`${data.user.name}[${data.user.id}] (${data.user.alias}[${data.user.alias_id}]) has left room[${data.room.id}]`.yellow);
      })
      .catch((response) => {
        console.log(`Error while ${data.user.name} is leaving room ${data.room.id}`.red);
      })
  }
});
