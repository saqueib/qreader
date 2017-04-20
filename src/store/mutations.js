import { storage } from '../utils'

// Show add channel box
export const TOGGLE_ADD_CAHNNEL = state => {
  state.showAddChannel = !state.showAddChannel
}

// Add a channel
export const ADD_CAHNNEL = (state, newChannel) => {
  let allChannel = state.channels;

  // ceck if already exists
  let index = allChannel.findIndex( (data) => {
    return newChannel.channel.link == data.channel.link
  });

  // if alreday have it just update it
  if( index !== -1 ) {
    // dont update title and color
    newChannel.channel.title = allChannel[index].channel.title;
    allChannel[index] = newChannel;
    state.channels.splice(index, 1, newChannel);
  } else {
    allChannel.unshift(newChannel);
  }

  // persist it
  storage.set('qr_channels', allChannel);
}

// Rename a channel by url
export const RENAME_CHANNEL = (state, data) => {
  // check if already exists
  let index = state.channels.findIndex( (channel) => {
    return data.url == channel.url
  });

  // if alreday have it just update it
  if( index !== -1 ) {
    let channel = state.channels[index];
    channel.channel.title = data.title;
    channel.color = data.color;

    // update channel
    state.channels.splice(index, 1, channel);

    // persist it
    storage.set('qr_channels', state.channels);
  }
}

// Delete a channel by url
export const DELETE_CHANNEL = (state, url) => {
  // find channel
  let index = state.channels.findIndex( (channel) => {
    return url == channel.url
  });

  if( index !== -1 ) {
    state.channels.splice(index, 1);

    // persist it
    storage.set('qr_channels', state.channels);
  }
}

// Toggle fav post
export const TOGGLE_FAV = (state, post) => {
  // find channel
  let index = state.favs.findIndex( (item) => {
    return item.link == post.link
  });

  if( index !== -1 ) {
    state.favs.splice(index, 1);
  } else {
    state.favs.unshift(post);
  }

  // persist it
  storage.set('qr_favs', state.favs);
}

// Collapse sidebar
export const TOGGLE_SIDEBAR = (state) => {
  state.collapseSideBar = !state.collapseSideBar;

  // persist it
  storage.set('qr_sidebar_collapse', state.collapseSideBar);
}
