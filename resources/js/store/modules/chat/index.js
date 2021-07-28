import actions from './actions';
import mutations from './mutations';
import getters from './getters';

const state = {
  // The array of error messages.
  errorMessages: [],
  user: {},
  // Selected alias.
  aliasInfo: {},
  // The current room that the user is in.
  room: {},
  // The list of rooms that the user is in.
  rooms: [],
  // The list of rooms that the user has created.
  hostedRooms:[],
  officialRooms: [],
  publicRooms: [],
  showMyRooms: false,
  showRoomList: true,
  token: '',
  isLoading: false,
  showInterface: false,
  showCreateRoom: false,
  showAliasSelect: false,
  showCreateAvatar: false,
  avatarComponents: [],
  builtAvatars: [],
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
