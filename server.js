'use strict';

var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
require('dotenv').config();

var redisPort = 6379;
var redisHost = '127.0.0.1';

var ioRedis = require('ioredis');

var redis = new ioRedis(redisPort, redisHost);

redis.subscribe('video-call');

redis.on('message', function (channel, message) {
  message  = JSON.parse(message);
  console.log(message);
  io.emit(channel + ':' + message.event, message.data);
});

var broadcastPort = 8081;

server.listen(broadcastPort, function () {
  console.log('Socket server is running.');
});

