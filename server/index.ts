import express, { Express, Request, Response } from "express";
import fs from "fs";

const app: Express = express();
const port = 8080;

//configure cors
app.use(function (req: Request, res: Response, next: () => void) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
});

app.get("/getBookmarks/:listName", (req: Request, res: Response) => {
  const listName = req.params.listName;
  //if file exists in the directory
  if (fs.existsSync(`./lists/${listName}.json`)) {
    const bookmarks = fs.readFileSync(`./lists/${listName}.json`, "utf8");
    return res.status(200).send(bookmarks);
  }

  res.status(400).send("List not found");
});

app.listen(port, () => {
  console.log("[server]: Server is running at https://localhost:" + port);
});