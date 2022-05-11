"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const fs_1 = __importDefault(require("fs"));
const app = (0, express_1.default)();
const port = 8080;
app.use(function (req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});
app.get("/getBookmarks/:listName", (req, res) => {
    const listName = req.params.listName;
    if (fs_1.default.existsSync(`./lists/${listName}.json`)) {
        const bookmarks = fs_1.default.readFileSync(`./lists/${listName}.json`, "utf8");
        return res.status(200).send(bookmarks);
    }
    res.status(400).send("List not found");
});
app.listen(port, () => {
    console.log("[server]: Server is running at https://localhost:" + port);
});
