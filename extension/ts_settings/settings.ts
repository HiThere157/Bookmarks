// Static HTML elements
const urlInput = document.getElementById("url-input") as HTMLInputElement;
const saveSettingsButton = document.getElementById("save-settings-btn") as HTMLButtonElement;

// Save the URL to local storage.
function saveBookmark(): void {
    chrome.storage.local.set({ "bookmarks_api_url": urlInput.value });
}

// Set the URL input to the cached value.
async function init(): Promise<void> {
    const bookmarksApiUrl = await chrome.storage.local.get("bookmarks_api_url");
    urlInput.value = bookmarksApiUrl.bookmarks_api_url || "";

    saveSettingsButton.onclick = saveBookmark;
}

window.onload = init;