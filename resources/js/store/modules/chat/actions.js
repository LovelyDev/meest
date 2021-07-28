import axios from 'axios';
import get from 'lodash/get';
import orderBy from 'lodash/orderBy';
import sortBy from 'lodash/sortBy';
import each from 'lodash/each';
import filter from 'lodash/filter';
import isEmpty from "lodash/isEmpty";

export default {
  setUser({commit}, value) {
    commit('SET_USER', value);
  },

  setRooms({commit, dispatch}, value) {
    commit("SET_ROOMS", value);
    dispatch('setHostedRooms');
  },

  setRoom({commit, state}, value) {
    commit("SET_ROOM", value);
  },

  setShowMyRooms({commit}, value) {
    commit("SET_SHOW_MY_ROOMS", value);
  },

  setShowRoomList({commit}, value) {
    commit("SET_SHOW_ROOM_LIST", value);
  },

  setToken({commit}, value) {
    commit("SET_TOKEN", value);
  },

  setIsLoading({commit}, value) {
    commit("SET_IS_LOADING", value);
  },

  setShowCreateRoom({commit}, value) {
    commit("SET_SHOW_CREATE_ROOM", value);
  },

  setAvatarComponents({commit}, value) {
    let sorted = [];
    sorted = sortBy(value, ['weight']);

    // each(sorted, (object) => {
    //   //let newObject = {};
    //   sorted[object.key] = object;
    // });
    commit("SET_AVATAR_COMPONENTS", sorted);
  },

  setBuiltAvatars({commit}, value) {
    commit("SET_BUILT_AVATARS", value);
  },

  /**
   * Returns all rooms.
   */
  getAllRooms({commit, state}) {
    axios.get(`/api/rooms`, {
      headers: {
        Authorization: `Bearer ${state.token}`,
      }
    })
      .then((response) => {
        commit('SET_PUBLIC_ROOMS', orderBy(filter(get(response, 'data', []), (room) => {
            return room.room_type_id > 1;
          }),
          ['tenant_count', 'title'], ['desc', 'asc']));

        // Stores either the official or the registered rooms.
        commit('SET_OFFICIAL_ROOMS', orderBy(filter(get(response, 'data', []), (room) => {
            return room.room_type_id === 1;
          }),
          ['tenant_count', 'title'], ['desc', 'asc']));
      })
      .catch((response) => {
        console.log(response);
      });
  },

  /**
   * Returns all rooms created by the user.
   */
  setHostedRooms({commit, state}) {
    axios.get(`/api/rooms/users/${state.user.id}/host`, {
      headers: {
        Authorization: `Bearer ${state.token}`,
      }
    })
      .then((response) => {
        commit("SET_HOSTED_ROOMS", get(response, 'data', []));
      })
      .catch((response) => {
        console.log(response);
      });
  },

  /**
   * Reloads the user info from the database.
   * This includes all aliases attached to the user.
   * @param commit
   * @param state
   */
  async reloadUserInfo({ commit, state }) {
    axios.get(`/api/auth/user`, {
      headers: {
        Authorization: `Bearer ${state.token}`,
      }
    })
      .then((response) => {
        const data = get(response, 'data', {});
        commit('SET_USER', data);
      })
      .catch((response) => {
        console.log(response.data);
      })
  }
};
