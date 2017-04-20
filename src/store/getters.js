// Get the channels
export const channels = state => state.channels

// Get the favs
export const favs = state => state.favs

// Add Channel Dialog
export const showAddChannel = state => state.showAddChannel

// get random quote
export const quotes = state => state.quotes

// Check if post is faved
export const isFaved = (state, getters) => (link) => {
    return state.favs.find(fav => fav.link === link)
}

// Fave count
export const favCount = state => state.favs.length

// Get toggle status of sidebar
export const collapse = state => state.collapseSideBar
