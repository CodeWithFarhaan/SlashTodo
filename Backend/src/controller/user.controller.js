const User = require("../model/user.model.js");
const jwt = require("jsonwebtoken");
const cookieParser = require("cookie-parser");


const register = async (req, res) => {
  try {
    const {uuid, name, email, password } = req.body;

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
    const token = jwt.sign({ userId: user._id }, "farru@2002", { expiresIn: "1h" });
    res.cookie("token", token);

    return res.status(201).json({ message: "User registered successfully", user, token });
  } catch (error) {
    return res.status(500).json({ message: "internal server error", error: error.message });
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
    const token = jwt.sign({ userId: user._id }, "your_jwt_secret", { expiresIn: "1h" });
    res.cookie("token", token);

    return res.status(200).json({ message: "login successful", token });
  } catch (error) {
    return res.status(500).json({ message: "internal server error", error: error.message });
  }
};


module.exports = {
  register,
  login
};
