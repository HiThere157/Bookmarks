const express = require("express");
const http = require("http");

var httpApp = express();

//Cors configuration
httpApp.use(function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
});

httpApp.get("*", function (req, res) {
  res.status(200).json([{
    url: "https://www.google.com",
    title: "Google"
  }]);
});

http.createServer(httpApp).listen(8005);