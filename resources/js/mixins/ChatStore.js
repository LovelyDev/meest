import io from 'socket.io-client';
import {mapState, mapMutations, mapGetters, mapActions} from 'vuex';
import includes from 'lodash/includes';
import isEmpty from 'lodash/isEmpty';
import forEach from 'lodash/forEach';
require('dotenv').config();

export default {

  computed: {
    ...mapState('chat', {
      userInfo: 'user',
      rooms: 'rooms',
      roomInfo: 'room',
      showMyRooms: 'showMyRooms',
      showRoomList: 'showRoomList',
      officialRooms: 'officialRooms',
      publicRooms: 'publicRooms',
      token: 'token',
      isLoading: 'isLoading',
      showInterface: 'showInterface',
      showCreateRoom: 'showCreateRoom',
      showAliasSelect: 'showAliasSelect',
      showCreateAvatar: 'showCreateAvatar',
      showError: 'showError',
      errorMessages: 'errorMessages',
      avatarComponents: 'avatarComponents',
      builtAvatars: 'builtAvatars',
      aliasInfo: 'aliasInfo'
    }),
  },

  methods: {
    ...
      mapMutations('chat', [
        'removeRoom'
      ]),
    ...
      mapActions('chat', [
        'setUser',
        'setRooms',
        'setRoom',
        'setShowMyRooms',
        'setShowRoomList',
        'getAllRooms',
        'setToken',
        'setSocket',
        'setIsLoading',
        'setShowCreateRoom',
        'setShowCreateAvatar',
        'setErrorMessages',
        'setAvatarComponents',
        'setBuiltAvatars',
        'reloadUserInfo'
      ]),
    ...
      mapActions('avatar', [
        'getPrebuiltAvatars'
      ]),

    /**
     * Check if user role is allowed
     *
     * @param {string} whitelisted Roles.
     */
    isRoleAllowed(whitelistedRoles = []) {
      let allowed = false;

      // We bail early if the user object is empty.
      if (isEmpty(this.userInfo)) {
        return allowed;
      }

      forEach(this.userInfo.roles, (value, key) => {
        allowed = includes(whitelistedRoles, value.id);

        // We exit the lodash forEach loop when we hit true.
        if (allowed) {
          return false;
        }
      });

      return allowed;
    },
    /**
     * Check if user is a guest.
     *
     * @returns {boolean}
     */
    isGuest() {
      let isGuest = false;

      // We bail early if the user object is empty.
      if (isEmpty(this.userInfo)) {
        return isGuest;
      }

      forEach(this.userInfo.roles, (value, key) => {
        isGuest = includes([12], value.id);

        // We exit the lodash forEach loop when we hit true.
        if (isGuest) {
          return false;
        }
      });

      return isGuest;
    },
    /**
     * Connect to Socket.io Server.
     */
    connectToSocketIoServer() {
      const port = process.env.MIX_CHAT_PORT;
      const domain = process.env.MIX_CHAT_DOMAIN;

      this.socket = io(`${domain}:${port}/`);
    }
  },
};
