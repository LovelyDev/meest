<template>
  <div class="main-navigation">
    <div class="main-navigation__top">
      <div class="main-navigation__logo">
        <img src="/storage/logo.png" class="logo">
      </div>
      <div class="main-navigation__brand">
        <h1 class="main-navigation__app-name">{{ settings.site_name }}</h1>
        <h6 class="main-navigation__tag-line">{{ settings.site_slogan }}</h6>
      </div>
    </div>

    <div class="main-navigation__bottom">
      <div class="main-navigation__navigation">
        <ul class="menu">
          <li>
            <a href="#" class="home"
               @click.prevent="setShowRoomList(true)">
              Home
            </a>
          </li>
          <li>
            <a href="#" class="member-lookup">Member Lookup</a>
          </li>
          <li>
            <a href="#" class="help-center">Help Center</a>
          </li>
          <li v-if="isRoleAllowed([1,2,3,4,10,11])">
            <a href="#" class="message-center">Message Center</a>
          </li>
          <li v-if="isRoleAllowed([1,2,3,4,10,11])">
            <a href="#" class="manage-account">Manage Account</a>
          </li>
        </ul>

      </div>
      <div class="main-navigation__logout">

        <div v-if="hasActiveRoom"
             class="main-navigation__active-room">
          Now Chatting in:
          <img :src="getRoomLogo" class="logo-small">
          <span class="title">{{ roomInfo.title }}</span>
        </div>

        <div class="main-navigation__welcome-user">
          {{ welcomeName }}
        </div>
        <a href="#"
           @click.prevent="logout"
           class="logout">Sign Out</a>
      </div>
    </div>

  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';
  import Settings from 'Mixins/Settings';
  import isEmpty from 'lodash/isEmpty';

  export default {
    mixins: [ChatStore, Settings],

    props: {
      /**
       * Url to consume api data.
       */
      dataUrl: {
        type: String,
        required: false
      },
    },

    data() {
      return {
        activeIndex: '1',
        activeIndex2: '1'
      };
    },

    computed: {
      /**
       * Returns the the welcome name.
       */
      welcomeName() {
        const name = this.$options.filters.capitalize(this.userInfo.name);

        return this.isGuest() ? `Welcome, Guest_${name}` : `Welcome, ${name}`;
      },
      /**
       * Check if there is a active room.
       */
      hasActiveRoom() {
        return !isEmpty(this.roomInfo);
      },
      /**
       * Returns the room logo.
       */
      getRoomLogo() {
        return this.roomInfo.room_type_id === 2 ? 'storage/people.png' : 'storage/logo.png';
      }
    },

    methods: {
      /**
       * Logs the user out of the site.
       */
      logout() {
        this.$axios.post(`/logout`)
          .then((response) => {
            window.location.href = '/';
          })
          .catch((response) => {
            console.log(response);
          });
      },
    },
  };
</script>
