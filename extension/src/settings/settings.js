"use strict";
// Static HTML elements
const urlInput = document.getElementById("url-input");
const saveSettingsButton = document.getElementById("save-settings-btn");
// Save the URL to local storage.
function saveBookmark() {
    chrome.storage.local.set({ "bookmarks_api_url": urlInput.value });
}
// Set the URL input to the cached value.
async function init() {
    const bookmarksApiUrl = await chrome.storage.local.get("bookmarks_api_url");
    urlInput.value = bookmarksApiUrl.bookmarks_api_url || "";
    saveSettingsButton.onclick = saveBookmark;
}
window.onload = init;
