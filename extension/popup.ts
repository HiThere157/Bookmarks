const bookmarkTemplate = (document.getElementById("bookmark-template") as HTMLTemplateElement).content;
const bookmarksContainer = document.getElementById("content") as HTMLDivElement;

const listNameSpan = document.getElementById("list-name") as HTMLSpanElement;
const searchInput = document.getElementById("search-input") as HTMLInputElement;
const noResultsFound = document.getElementById("no-results") as HTMLDivElement;

type Bookmark = {
  title: string;
  url: string;
}
async function fetchBookmarks(listName: string): Promise<Bookmark[]> {
  const response = await fetch("test_data.json");
  listNameSpan.innerText = listName.toUpperCase();
  // const response = await fetch("https://bookmarks.itlab/getBookmarks/" + listName);

  if (response.status === 200) {
    const bookmarks = await response.json() as Bookmark[];
    return bookmarks.sort((a, b) => {
      return a.title.localeCompare(b.title);
    });
  }

  return [];
}

function searchBookmarks(bookmarks: Bookmark[], query: string): Bookmark[] {
  return bookmarks.filter(bookmark => {
    return bookmark.title.toLowerCase().includes(query.toLowerCase());
  });
}

function renderBookmarks(bookmarks: Bookmark[]): void {
  bookmarksContainer.innerHTML = "";

  bookmarks.forEach((bookmark) => {
    const bookmarkElement = bookmarkTemplate.cloneNode(true) as HTMLDivElement;

    const bookmarkLink = bookmarkElement.querySelector(".bookmark") as HTMLAnchorElement;
    const bookmarkTitle = bookmarkElement.querySelector(".bookmark-title") as HTMLSpanElement;
    const bookmarkIcon = bookmarkElement.querySelector(".bookmark-icon") as HTMLImageElement;

    bookmarkLink.href = bookmark.url;
    bookmarkTitle.innerText = bookmark.title;
    bookmarkIcon.src = "chrome://favicon/https://www.reddit.com";

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
  }
}