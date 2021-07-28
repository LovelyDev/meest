<template>
  <div class="layout"
       :class="getLoadingClass">
    <div class="layout__header">
      <main-navigation/>
    </div>

    <div class="layout__main">
      <div class="layout__main-inner">
        <div class="layout__sidebar-left">
          <side-navigation @showMyRooms=""
                           @showRoomList=""/>
        </div>

        <div :class="{'hasList' : showRoomList }"
             ref="content"
             class="layout__middle">
          <chat ref="chat"/>
        </div>

        <div v-if="showMyRooms"
             class="layout__sidebar-right">
<!--          <chatters/>-->
        </div>
      </div>
    </div>

    <div class="layout__footer">
      <span>{{ settings.site_name }} Copyright {{ new Date().getFullYear() }} </span>
    </div>

    <md-dialog :md-active.sync="showCreateRoom"
               :md-click-outside-to-close="false"
               :md-close-esc="false">

      <create-room @create-room-close="setShowCreateRoom(false)"
                   @join-room="joinRoom($event)"/>
    </md-dialog>

  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';
  import Settings from 'Mixins/Settings';
  import get from 'lodash/get';
  import forEach from 'lodash/forEach';
  import throttle from 'lodash/throttle';
  import orderBy from 'lodash/orderBy';
  import MainNavigation from "./MainNavigation";
  import Chat from "../Chat";
  import SideNavigation from "./SideNavigation";
  import Chatters from "../room/Chatters";
  import CreateRoom from "../room/CreateRoom";

  export default {
    components: {
      Chatters,
      SideNavigation,
      Chat,
      MainNavigation,
      CreateRoom,
    },

    mixins: [ChatStore, Settings],

    props: {
      /**
       * The user object.
       */
      user: {
        type: Object,
        default: () => {
        }
      },
      /**
       * The array of avatar components.
       */
      avatars: {
        type: Object,
        default: () => {}
      }
    },

    data() {
      return {
        serverRoomMaps: [],
        socket: {},
      };
    },

    computed: {
      getLoadingClass() {
        return this.isLoading ? 'has-loading' : '';
      }
    },

    mounted() {
      this.connectToSocketIoServer();
      this.setUser(this.user);
      this.setAvatarComponents(this.avatars);

      // We ensure that the we leave all rooms that
      // the user has joined when the browser has been refreshed.
      this.$nextTick(() => {
        this.socket.on('CONNECTION_STATS', (data) => {
          this.serverRoomMaps = data.roomsMap;

        });
      });
    },

    methods: {
      joinRoom(room) {
        this.$refs.chat.joinToRoom(room);
      },
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
