// Static HTML elements
const bookmarkTemplate = (document.getElementById("bookmark-template") as HTMLTemplateElement).content;
const spinnerTemplate = (document.getElementById("spinner-template") as HTMLTemplateElement).content;

const bookmarksContainer = document.getElementById("content") as HTMLDivElement;

const listName = document.getElementById("list-name") as HTMLSpanElement;
const searchInput = document.getElementById("search-input") as HTMLInputElement;
const refreshButton = document.getElementById("refresh-btn") as HTMLButtonElement;
const noResultsFound = document.getElementById("no-results") as HTMLDivElement;
const noConnection = document.getElementById("no-connection") as HTMLDivElement;
const connectionStatus = document.getElementById("connection-status") as HTMLDivElement;

interface IBookmark {
  title: string;
  url: string;
}

// Set the connection status icon and visibility.
enum ConnectionStatus {
  Loading,
  Success,
  Error
}
function setConnectionStatus(status: ConnectionStatus): void {
  connectionStatus.classList.remove("spinning");

  if (status === ConnectionStatus.Loading) {
    connectionStatus.style.backgroundImage = "url(icons/sync-circle-outline.svg)";
    connectionStatus.classList.add("spinning");

  } else if (status === ConnectionStatus.Success) {
    connectionStatus.style.backgroundImage = "url(icons/checkmark-circle-outline.svg)";

  } else if (status === ConnectionStatus.Error) {
    connectionStatus.style.backgroundImage = "url(icons/alert-circle-outline.svg)";
  }
}

// Fetch bookmarks from the extension's API and return them as an array of objects.
async function fetchBookmarksCache(): Promise<IBookmark[]> {
  const bookmarks = await chrome.storage.local.get("bookmarks");

  // If the bookmarks are cached, return them.
  if (bookmarks.bookmarks) {
    // Run async function to fetch bookmarks from API to cache them for next time.
    catchFetchBookmarksApi();
    return bookmarks.bookmarks;
  }

  // If the bookmarks are not cached, await the API call and return the bookmarks.
  return await fetchBookmarksApi();
}

// Call bookmark API non blocking and catch errors to display the 'connection error' icon.
async function catchFetchBookmarksApi(): Promise<void> {
  try {
    await fetchBookmarksApi();
  } catch (error) {
    console.error(error);
    setConnectionStatus(ConnectionStatus.Error);
  }
}

// Fetches bookmarks from the extension's API and returns them as an array of objects.
async function fetchBookmarksApi(): Promise<IBookmark[]> {
  const fetchApiUrl = await chrome.storage.local.get("bookmarks_api_url");
  listName.innerText = fetchApiUrl.bookmarks_api_url.split("/")[2];
  const response = (await Promise.all([
    fetch(fetchApiUrl.bookmarks_api_url),
    // Add a minimum delay to ensure the spinner is visible before the bookmarks are fetched.
    // It somehow feels better. Am I crazy? I don't know. ¯\_(ツ)_/¯
    new Promise(resolve => setTimeout(resolve, 1000))
  ]))[0];

  // If the API call fails, return an empty array.
  if (!response.ok) { throw new Error("API call failed"); }

  const bookmarks = await response.json() as IBookmark[];

  if(!checkResponse(bookmarks)) { throw new Error("Invalid response"); }

  // Cache the bookmarks for next time.
  chrome.storage.local.set({ "bookmarks": bookmarks });
  setConnectionStatus(ConnectionStatus.Success);

  return bookmarks;
}

// Check response array for correct interface
function checkResponse(response: IBookmark[]): boolean {
  if(!Array.isArray(response)) {
    return false;
  }

  for(let i = 0; i < response.length; i++) {
    if (typeof response[i].title !== "string" || typeof response[i].url !== "string") {
      return false;
    }
  }

  return true;
}

// Searches for bookmarks that match the search query and returns them as an new array of objects.
function searchBookmarks(bookmarks: IBookmark[], query: string): IBookmark[] {
  return bookmarks.filter(bookmark => {
    return bookmark.title.toLowerCase().includes(query.toLowerCase());
  });
}

// Renders the bookmarks to the page.
function renderBookmarks(bookmarks: IBookmark[]): void {
  bookmarksContainer.innerHTML = "";

  const sortedBookmarks = bookmarks.sort((a, b) => {
    return a.title.localeCompare(b.title);
  });

  sortedBookmarks.forEach((bookmark) => {
    const bookmarkElement = bookmarkTemplate.cloneNode(true) as HTMLDivElement;

    const bookmarkContainer = bookmarkElement.querySelector(".bookmark") as HTMLDivElement;
    const bookmarkTitle = bookmarkElement.querySelector(".title") as HTMLSpanElement;
    const bookmarkIcon = bookmarkElement.querySelector(".icon") as HTMLImageElement;

    // Set the bookmark's title and url.
    bookmarkContainer.setAttribute("data-url", bookmark.url);
    bookmarkContainer.title = bookmark.url;
    bookmarkTitle.innerText = bookmark.title;
    bookmarkIcon.src = `chrome-extension://${chrome.runtime.id}/_favicon?size=64&pageUrl=${bookmark.url}`;

    bookmarksContainer.appendChild(bookmarkElement);
  });

  // Show the no results found message if there are no bookmarks to show.
  noResultsFound.classList.toggle("hidden", bookmarks.length !== 0);
}

function onBookmarkClick(event: MouseEvent): void {
  const target = event.target as HTMLDivElement;
  const bookmark = target.closest(".bookmark") as HTMLDivElement;
  const url = bookmark.getAttribute("data-url") as string;
  chrome.tabs.update({ url });
}

async function init() {
  bookmarksContainer.onclick = onBookmarkClick;

  // Clear all errors and empty the search input.
  noConnection.classList.add("hidden");
  noResultsFound.classList.add("hidden");
  searchInput.value = "";

  // Show the spinner while the bookmarks are being fetched.
  bookmarksContainer.innerHTML = "";
  bookmarksContainer.appendChild(spinnerTemplate.cloneNode(true) as HTMLDivElement);

  // Show connection status loading icon while the bookmarks are being fetched.
  setConnectionStatus(ConnectionStatus.Loading);

  // Clear the bookmarks cache and init the extension again.
  refreshButton.onclick = async () => {
    await chrome.storage.local.remove("bookmarks");
    init();
  }

  try {
    const bookmarks = await fetchBookmarksCache();
    renderBookmarks(bookmarks);

    // Search for bookmarks when the user types in the search input.
    searchInput.oninput = () => {
      renderBookmarks(searchBookmarks(bookmarks, searchInput.value));
    }

  } catch (error) {
    console.error(error);
    bookmarksContainer.innerHTML = "";
    noConnection.classList.remove("hidden");
    setConnectionStatus(ConnectionStatus.Error);
  }
}

window.onload = init;