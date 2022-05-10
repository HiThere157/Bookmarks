"use strict";
const bookmarkTemplate = document.getElementById("bookmark-template").content;
const bookmarksContainer = document.getElementById("content");
const listNameSpan = document.getElementById("list-name");
const searchInput = document.getElementById("search-input");
const noResultsFound = document.getElementById("no-results");
async function fetchBookmarks(listName) {
    // const response = await fetch("test_data.json");
    listNameSpan.innerText = listName.toUpperCase();
    const response = await fetch("http://localhost:8080/getBookmarks/" + listName);
    if (response.status === 200) {
        const bookmarks = await response.json();
        return bookmarks.sort((a, b) => {
            return a.title.localeCompare(b.title);
        });
    }
    return [];
}
function searchBookmarks(bookmarks, query) {
    return bookmarks.filter(bookmark => {
        return bookmark.title.toLowerCase().includes(query.toLowerCase());
    });
}
function renderBookmarks(bookmarks) {
    bookmarksContainer.innerHTML = "";
    bookmarks.forEach((bookmark) => {
        const bookmarkElement = bookmarkTemplate.cloneNode(true);
        const bookmarkLink = bookmarkElement.querySelector(".bookmark");
        const bookmarkTitle = bookmarkElement.querySelector(".bookmark-title");
        const bookmarkIcon = bookmarkElement.querySelector(".bookmark-icon");
        bookmarkLink.href = bookmark.url;
        bookmarkTitle.innerText = bookmark.title;
        bookmarkIcon.src = bookmark.url + "/favicon.ico";
        bookmarksContainer.appendChild(bookmarkElement);
    });
    noResultsFound.classList.toggle("hidden", bookmarks.length !== 0);
}
window.onload = async () => {
    const bookmarks = await fetchBookmarks("itlab");
    renderBookmarks(bookmarks);
    searchInput.oninput = () => {
        const searchResult = searchBookmarks(bookmarks, searchInput.value);
        renderBookmarks(searchResult);
    };
};
