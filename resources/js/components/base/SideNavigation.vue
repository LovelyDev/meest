<template>
  <div class="side-navigation">
    <div class="side-navigation__top">
     <div class="side-navigation__title">
       Navigation
     </div>
    </div>

    <div class="side-navigation__body">
      <div class="side-navigation__navigation">
        <ul class="menu border-bottom">
          <li>
            <a href="#"
               class="create-room"
               @click.prevent="setShowCreateRoom(true)">
              Create Room
            </a>
          </li>
          <li>
            <a href="#" class="share-room">Share Room</a>
          </li>
        </ul>

        <ul class="menu">
          <li>
            <a href="#" class="usernames">My Usernames</a>
          </li>
          <li>
            <a href="#"
               @click.prevent="setShowMyRooms(true)"
               class="rooms">My Rooms</a>
          </li>
          <li v-if="isRoleAllowed([1,2,3,4])">
            <a href="#" class="administration">Administration</a>
          </li>
          <li>
            <a href="#" class="moderators">View Moderators</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';

  export default {
    mixins: [ChatStore],

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
