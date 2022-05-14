// Api URL for bookmarks
const fetchApiUrl = "http://localhost:8004/api/bookmarks";

// static HTML elements
const bookmarkTemplate = (document.getElementById("bookmark-template") as HTMLTemplateElement).content;
const spinnerTemplate = (document.getElementById("spinner-template") as HTMLTemplateElement).content;

const bookmarksContainer = document.getElementById("content") as HTMLDivElement;

const listNameSpan = document.getElementById("list-name") as HTMLSpanElement;
const searchInput = document.getElementById("search-input") as HTMLInputElement;
const refreshButton = document.getElementById("refresh-btn") as HTMLButtonElement;
const noResultsFound = document.getElementById("no-results") as HTMLDivElement;
const noConnection = document.getElementById("no-connection") as HTMLDivElement;

interface IBookmark {
  title: string;
  url: string;
}

// Fetch bookmarks from the extension's API and return them as an array of objects.
async function fetchBookmarksCache(): Promise<IBookmark[]> {
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

async function clearBookmarksCache(): Promise<void> {
  await chrome.storage.local.remove("bookmarks");
  init();
}

// Fetches bookmarks from the extension's API and returns them as an array of objects.
async function fetchBookmarksApi(): Promise<IBookmark[]> {
  listNameSpan.innerText = fetchApiUrl.split("/")[2];
  const response = await fetch(fetchApiUrl);

  // If the API call fails, return an empty array.
  if (!response.ok) { return []; }

  const bookmarks = await response.json() as IBookmark[];

  // Cache the bookmarks for next time.
  chrome.storage.local.set({ "bookmarks": bookmarks });
  return bookmarks;
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

    const bookmarkLink = bookmarkElement.querySelector(".bookmark") as HTMLAnchorElement;
    const bookmarkTitle = bookmarkElement.querySelector(".title") as HTMLSpanElement;
    const bookmarkIcon = bookmarkElement.querySelector(".icon") as HTMLImageElement;

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
  bookmarksContainer.appendChild(spinnerTemplate.cloneNode(true) as HTMLDivElement);

  try {
    const bookmarks = await fetchBookmarksCache();
    renderBookmarks(bookmarks);

    // Search for bookmarks when the user types in the search input.
    searchInput.oninput = () => {
      renderBookmarks(searchBookmarks(bookmarks, searchInput.value));
    }

    // Clear the bookmarks cache and init the extension again.
    refreshButton.onclick = () => {
      clearBookmarksCache();
    }

  } catch (error) {
    console.error("Failed to fetch bookmarks");
    // Show the no connection message if the API call fails.
    noConnection.classList.remove("hidden");
  }
}

window.onload = init;