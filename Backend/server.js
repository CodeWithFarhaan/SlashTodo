const express = require("express");
const port = process.env.port || 5000;
const connectDB = require("./src/db/index");
require("dotenv").config();
const cors = require("cors");
const cookieParser = require("cookie-parser");
const router = require("./src/routes/user.routes");
const app = express();

app.use(
  cors({
    origin: process.env.CORS_ORIGIN,
    credentials: true,
  })
);
app.use(express.json({ limit: "16kb" }));
app.use(express.urlencoded({ extended: true, limit: "16kb" }));
app.use(cookieParser());

app.use('/api', router);

connectDB()
  .then(() => {
    app.listen(process.env.port, () => {
      console.log(`Server is listening on port ${process.env.port}`);
    });
  })
  .catch((err) => {
    console.log("Mongodb connection failed!", err);
  });