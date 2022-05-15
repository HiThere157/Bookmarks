"use strict";
// Api URL for bookmarks
const fetchApiUrl = "http://localhost:8004/api/bookmarks";
// static HTML elements
const bookmarkTemplate = document.getElementById("bookmark-template").content;
const spinnerTemplate = document.getElementById("spinner-template").content;
const bookmarksContainer = document.getElementById("content");
const listNameSpan = document.getElementById("list-name");
const searchInput = document.getElementById("search-input");
const refreshButton = document.getElementById("refresh-btn");
const noResultsFound = document.getElementById("no-results");
const noConnection = document.getElementById("no-connection");
// Fetch bookmarks from the extension's API and return them as an array of objects.
async function fetchBookmarksCache() {
    const bookmarks = await chrome.storage.local.get("bookmarks");
    // If the bookmarks are cached, return them.
    if (bookmarks.bookmarks) {
        // Run async function to fetch bookmarks from API to cache them for next time.
        fetchBookmarksApi();
        return bookmarks.bookmarks;
    }
    // If the bookmarks are not cached, await the API call and return the bookmarks.
    return await fetchBookmarksApi();
}
// Clear the bookmarks cache.
async function clearBookmarksCache() {
    await chrome.storage.local.remove("bookmarks");
    noConnection.classList.add("hidden");
    init();
}
// Fetches bookmarks from the extension's API and returns them as an array of objects.
async function fetchBookmarksApi() {
    listNameSpan.innerText = fetchApiUrl.split("/")[2];
    const response = await fetch(fetchApiUrl);
    // If the API call fails, return an empty array.
    if (!response.ok) {
        return [];
    }
    const bookmarks = await response.json();
    // Cache the bookmarks for next time.
    chrome.storage.local.set({ "bookmarks": bookmarks });
    return bookmarks;
}
// Searches for bookmarks that match the search query and returns them as an new array of objects.
function searchBookmarks(bookmarks, query) {
    return bookmarks.filter(bookmark => {
        return bookmark.title.toLowerCase().includes(query.toLowerCase());
    });
}
// Renders the bookmarks to the page.
function renderBookmarks(bookmarks) {
    bookmarksContainer.innerHTML = "";
    const sortedBookmarks = bookmarks.sort((a, b) => {
        return a.title.localeCompare(b.title);
    });
    sortedBookmarks.forEach((bookmark) => {
        const bookmarkElement = bookmarkTemplate.cloneNode(true);
        const bookmarkLink = bookmarkElement.querySelector(".bookmark");
        const bookmarkTitle = bookmarkElement.querySelector(".title");
        const bookmarkIcon = bookmarkElement.querySelector(".icon");
        // Set the bookmark's title and url.
        bookmarkLink.href = bookmark.url;
        bookmarkTitle.innerText = bookmark.title;
        bookmarkIcon.src = "chrome://favicon/https://www.reddit.com";
        bookmarksContainer.appendChild(bookmarkElement);
    });
    // Show the no results found message if there are no bookmarks to show.
    noResultsFound.classList.toggle("hidden", bookmarks.length !== 0);
}
async function init() {
    // Show the spinner while the bookmarks are being fetched.
    bookmarksContainer.innerHTML = "";
    bookmarksContainer.appendChild(spinnerTemplate.cloneNode(true));
    // Clear the bookmarks cache and init the extension again.
    refreshButton.onclick = () => {
        clearBookmarksCache();
    };
    try {
        const bookmarks = await fetchBookmarksCache();
        renderBookmarks(bookmarks);
        // Search for bookmarks when the user types in the search input.
        searchInput.oninput = () => {
            renderBookmarks(searchBookmarks(bookmarks, searchInput.value));
        };
    }
    catch (error) {
        console.error("Failed to fetch bookmarks");
        // Show the no connection message if the API call fails.
        bookmarksContainer.innerHTML = "";
        noConnection.classList.remove("hidden");
    }
}
window.onload = init;
