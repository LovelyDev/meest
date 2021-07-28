import store from '../chat/index.js';
import axios from 'axios';
import get from 'lodash/get';
import map from 'lodash/map';
import fromPairs from 'lodash/fromPairs';
import split from 'lodash/split';
import keyBy from 'lodash/keyBy';
import forEach from 'lodash/forEach';
import filter from 'lodash/filter';


export default {
  /**
   * Returns all rooms created by the user.
   */
  async getPrebuiltAvatars({ commit, state, rootState }) {
    axios.get(`/api/avatar/defaults`, {
      headers: {
        Authorization: `Bearer ${rootState.chat.token}`,
      }
    })
      .then((response) => {
        const avatarsDefaults = [];
        const avatars = get(response, 'data', []);

        forEach(avatars, (avatar, key) => {
          //let avatarObject = fromPairs(avatar.split(',').map(s => s.split('=')));
          //avatarObject['gender'] = avatar.gender === 'm' ? 'man' : 'woman';
          avatar['gender'] = avatar.gender === 'm' ? 'man' : 'woman';
          avatarsDefaults.push(avatar)
          //avatarsDefaults.push(avatarObject)
        });


       // console.log(avatarsDefaults);
       //  commit.PUSH_AVATAR_COMPONENT(store.state, {
       //    key: 'built',
       //    label: 'Choose a prebuilt avatar',
       //    weight: 9,
       //    man: filter(avatarsDefaults, ['gender', 'man']),
       //    woman: filter(avatarsDefaults, ['gender', 'man'])
       //  });

        const gender = rootState.chat.user.gender === 'm' ? 'man' : 'woman';
        commit("SET_AVATAR_DEFAULTS", filter(avatarsDefaults, ['gender', gender]));
      })
      .catch((response) => {
        console.log(response);
      });
  },
};
