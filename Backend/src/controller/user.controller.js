const mongoose = require("mongoose");
const User = require("../model/user.model");
const jwt = require("jsonwebtoken");
const cookieParser = require("cookie-parser");

const register = async (req, res) => {
  try {
    const { uuid, name, email, password } = req.body;
    if ([uuid, name, email, password].some((field) => field?.trim() === "")) {
      return res.status(400).json({ message: "field is empty" });
    }

    const existedUser = await User.findOne({ email });
    if (existedUser) {
      return res.status(400).json({ message: "user already exists" });
    }

    const user = await User.create({ uuid, name, email, password });
    if (!user) {
      return res.status(500).json({ message: "something went wrong" });
    }

    // Generate JWT token
    const token = jwt.sign({ userId: user._id }, "farru@2002", {
      expiresIn: "1h",
    });
    res.cookie("token", token);

    return res
      .status(201)
      .json({ message: "User registered successfully", user, token });
  } catch (error) {
    return res
      .status(500)
      .json({ message: "internal server error", error: error.message });
  }
};

const login = async (req, res) => {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ message: "email or password is required" });
    }

    const user = await User.findOne({ email });
    if (!user) {
      return res.status(400).json({ message: "user not found" });
    }

    const isPasswordValid = await user.comparePassword(password);
    if (!isPasswordValid) {
      return res.status(401).json({ message: "Invalid credentials" });
    }

    // Generate JWT token
    const token = jwt.sign({ userId: user._id }, "your_jwt_secret", {
      expiresIn: "1h",
    });
    res.cookie("token", token);
    return res.status(200).json({ message: "login successful", token });
  } catch (error) {
    return res
      .status(500)
      .json({ message: "internal server error", error: error.message });
  }
};

const deleteuser = async (req, res) => {
  try {
    const { uuid } = req.params; // Access uuid from URL parameter

    // Find the user by UUID and delete from MongoDB
    const user = await User.findOneAndDelete({ uuid });

    if (!user) {
      return res.status(404).json({ message: "User not found" });
    }

    // Generate JWT token (though you're not using the token for anything here)
    const token = jwt.sign({ uuid: user.uuid }, "your_jwt_secret", {
      expiresIn: "1h",
    });
    res.cookie("token", token);

    return res.status(200).json({ message: "User deleted successfully" });
  } catch (error) {
    return res
      .status(500)
      .json({ message: "Failed to delete user", error: error.message });
  }
};

const updateuser = async (req, res) => {
  try {
    const {uuid, name, email } = req.body;
    console.log(req.body);
    if ([name, email].some((field) => field?.trim() === "")) {
      return res.status(400).json({ message: "field is empty" });
    }
    const user = await User.findOne({uuid});
    if (!user) {
      return res.status(404).json({ message: "user not found" });
    }
    // Generate JWT token (though you're not using the token for anything here)
    // const token = jwt.sign({ id: user.id }, "your_jwt_secret", {
    //   expiresIn: "1h",
    // });
    //res.cookie("token", token);
    if (name) user.name = name;
    if (email) user.email = email;
    await user.save();
    return res.status(200).json({ message: "user updated successfully" });
  } catch (error) {
    console.log(error);
    return res.status(500).send({ message: "internal server errror" });
  }
};

module.exports = {
  register,
  login,
  deleteuser,
  updateuser,
};
