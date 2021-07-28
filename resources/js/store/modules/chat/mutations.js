import remove from 'lodash/remove';

export default {
  SET_USER: function (state, value) {
    state.user = value;
  },

  SET_ROOMS: function (state, value) {
    state.rooms.push(value);
  },

  SET_ROOM: function (state, value) {
    state.room = value;
  },

  SET_SHOW_MY_ROOMS: function (state, value) {
    if (state.rooms.length > 0) {
      state.showMyRooms = value;
      state.showRoomList = !value;
    }
  },

  SET_SHOW_ROOM_LIST: function (state, value) {
    state.showRoomList = value;
    state.showMyRooms = !value;
  },

  SET_OFFICIAL_ROOMS: function (state, value) {
    state.officialRooms = value;
  },

  SET_PUBLIC_ROOMS: function (state, value) {
    state.publicRooms = value;
  },

  SET_TOKEN: function (state, value) {
    state.token = value;
  },

  SET_IS_LOADING: function (state, value) {
    state.isLoading = value;
  },

  setShowInterface: function (state, value) {
    state.showInterface = value;
  },

  SET_SHOW_CREATE_ROOM: function (state, value) {
    state.showCreateRoom = value;
  },

  setShowAliasSelect: function (state, value) {
    state.showAliasSelect = value;
  },

  setShowCreateAvatar: function (state, value) {
    state.showCreateAvatar = value;
  },

  setErrorMessages: function (state, value) {
    state.errorMessages = value;
  },

  SET_HOSTED_ROOMS: function (state, value) {
    state.hostedRooms = value;
  },

  SET_AVATAR_COMPONENTS: function (state, value) {
    state.avatarComponents = value;
  },

  PUSH_AVATAR_COMPONENT: function (state, value) {
    console.log(value);
    console.log('here');
    state.avatarComponents.push(value);
  },

  SET_BUILT_AVATARS: function (state, value) {
    state.builtAvatars = value;
  },

  setAliasInfo: function (state, value) {
    state.aliasInfo = value;
  },

  /**
   * Remove a room from the room list.
   *
   * @param state
   * @param value
   */
  removeRoom(state, value) {
    remove(state.rooms, (room) => {
      return room.id === value.id;
    });
  }
}
