"use strict";
// Static HTML elements
const urlInput = document.getElementById("url-input");
const saveSettingsButton = document.getElementById("save-settings-btn");
// Get the current bookmarks API URL from local storage.
async function getBookmarksUrl() {
    const bookmarksApiUrl = await chrome.storage.local.get("bookmarks_api_url");
    return bookmarksApiUrl.bookmarks_api_url || "";
}
// Save the URL to local storage.
function setBookmarksUrl() {
    chrome.storage.local.set({ "bookmarks_api_url": urlInput.value });
    saveSettingsButton.disabled = true;
}
// Enable the save button if the URL input has changed.
async function onUrlInputChange() {
    saveSettingsButton.disabled = await getBookmarksUrl() === urlInput.value;
}
// Set the URL input to the cached value.
async function init() {
    urlInput.value = await getBookmarksUrl();
    saveSettingsButton.onclick = setBookmarksUrl;
    urlInput.onchange = setBookmarksUrl;
    urlInput.oninput = onUrlInputChange;
}
window.onload = init;
