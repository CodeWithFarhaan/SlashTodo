const router = require("express").Router();
const { register, login, deleteuser, updateuser } = require("../controller/user.controller");

router.route("/register").post(register);
router.route("/login").post(login);
router.route("/deleteuser/:uuid").delete(deleteuser);
router.route("/updateuser").post(updateuser);
module.exports = router;