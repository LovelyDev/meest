import Vue from 'vue'
import Vuex from 'vuex'
import chat from './modules/chat/index.js';
import avatar from './modules/avatar';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    chat,
    avatar,
  },
  state: {
  },
  // Derived state.
  getters: {

  },
  // Mutations.
  mutations: {
  },
  // Actions.
  actions: {
  },
});
