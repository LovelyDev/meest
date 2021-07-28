<template>
  <div class="chat">
    <div class="chat__content">
      <!-- Room list -->
      <tabs v-show="showRoomList"
            :options="{ useUrlFragment: false }"
            active-tab="list"
            class="tabs">

        <tab prefix="<img src='/storage/logo.png' class='tab-icon'>"
             name="Official Chatrooms"
             :set-active="true"
             class="official">
          <room-list @joinRoom="joinToRoom($event)"
                     :items="officialRooms"
                     type="official">
          </room-list>
        </tab>

        <tab prefix="<img src='/storage/people.png' class='tab-icon'>"
             :set-active="false"
             name="Public Chatrooms">
          <room-list @joinRoom="joinToRoom($event)"
                     :items="publicRooms"
                     type="public">
          </room-list>
        </tab>
      </tabs>


      <!-- My Rooms -->
      <div v-show="showMyRooms"
           class="chat__classic">
        <tabs :options="getRoomTabOptions"
              :isRoom="true"
              class="tabs"
              ref="myRooms"
              :active-tab="`#${this.activeRoom}`"
              @tab-closed="leaveRoom($event)"
              @clicked="tabClicked"
              @changed="tabChanged">

          <tab v-for="room in rooms"
               :key="room.id"
               :name="`#${room.title}`"
               :set-active="room.id === parseInt(activeRoom)"
               :id="room.id.toString()"
               :refs="`tab-room-${room.id}`"
               @clicked="tabClicked"
               class="">

            <room :room="room"
                  :refs="`room-${room.id}`"
                  @leave="leaveRoom(room, $event)"/>
          </tab>
        </tabs>
      </div>
    </div>

    <interface @interface-close="closeInterfaceForm($event)"/>

    <alias-form @alias-form-close="closeAliasForm($event)"
                @avatar-form-close="closeAvatarForm($event)"/>

    <!-- Show error dialog -->
    <l-dialog id="modal-error"/>

    <loading-rhombus v-if="isLoading"/>
  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';
  import find from 'lodash/find';
  import get from 'lodash/get';
  import forEach from 'lodash/forEach';
  import reject from 'lodash/reject';
  import isEmpty from 'lodash/isEmpty';
  import Tabs from 'Components/base/Tabs'
  import Tab from 'Components/base/Tab'
  import RoomList from "Components/room/RoomList";
  import Interface from "Components/room/Interface";
  import AliasForm from "Components/room/AliasForm";
  import LDialog from "Tools/LDialog";

  export default {
    components: {
      AliasForm,
      Interface,
      RoomList,
      Tabs,
      Tab,
      LDialog
    },

    mixins: [ChatStore],

    props: {},

    data() {
      return {
        settings: {},
        activeRoom: '',
        socket: {},
        showAvatar: false,
        interfaceRoom: {},
        interfaceType: ''
      };
    },

    computed: {
      getRoomTabOptions() {
        return {
          useUrlFragment: false,
          defaultTabHash: `#${this.activeRoom}`
        };
      }
    },

    mounted() {
      this.connectToSocketIoServer();

      this.getAccessToken();

      this.$nextTick(() => {
        //this.leaveAllRooms();

        // Load the room list.
        this.socket.on('DISCONNECTING', (data) => {
          // this.removeUserInAllRooms();
        });

        // Load the room list.
        this.socket.on('REFRESH_ROOM_LIST', (data) => {
          this.getAllRooms();
        });

        // Update user's socket id.
        this.socket.on('UPDATE_USERS_SOCKET_ID', (data) => {
          //this.setUserSocketID(data);
        });
      });
    },

    methods: {
      /**
       * Closes the interface form.
       */
      closeInterfaceForm(interfaceType) {
        if (isEmpty(interfaceType)) {
          this.$store.commit('chat/setShowInterface', false);
          return;
        }

        // We reload the user information before opening the Alias select form.
        this.reloadUserInfo();
        this.interfaceType = interfaceType;
        this.$store.commit('chat/setShowInterface', false);
        this.$store.commit('chat/setShowAliasSelect', true);
        // this.setShowAliasSelect(true);
      },
      /**
       * Closes the Alias form.
       */
      closeAliasForm(aliasInfo) {
        if (!aliasInfo.userHasSelected) {
          this.$store.commit('chat/setShowAliasSelect', false);

          return;
        }

        // We set the selected aliasInfo in Vuex.
        this.$store.commit('chat/setAliasInfo', aliasInfo);

        // We set the default interface of the user.
        // This is only for members.
        if (!this.isGuest() && !isEmpty(this.interfaceType)) {
          this.$axios({
            method: 'post',
            url: `/api/auth/users/${this.userInfo.id}`,
            data: {
              chat_interface: this.interfaceType
            },
            headers: {
              Authorization: `Bearer ${this.token}`,
            }
          })
            .then((response) => {
              this.setUser(get(response, 'data', {}));
            })
            .catch((response) => {
              console.log(response.data);
            })
        }

        this.$store.commit('chat/setShowAliasSelect', false);
        this.enterRoom(this.interfaceRoom);
      },
      /**
       * Closes the Avatar form.
       */
      closeAvatarForm(avatarInfo) {
        console.log('avatar close detected in chat');
        console.log(avatarInfo);
        if (!avatarInfo.userHasSelected) {
          //this.$store.commit('chat/setShowCreateAvatar', false);

          return;
        }

        // We set the selected aliasInfo in Vuex.
        //this.$store.commit('chat/setAliasInfo', avatarInfo);

        this.$store.commit('chat/setShowAliasSelect', false);
        this.enterRoom(this.interfaceRoom);
      },
      /**
       * Removes the user in all rooms
       * This is called whenever the socket is disconnected.
       */
      removeUserInAllRooms() {
        this.$axios.delete(`/api/rooms/tenants/${this.userInfo.id}`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
          }
        })
          .then((response) => {
            this.leaveAllRooms();
          })
          .catch((response) => {
            console.log(response.data);
          })
      },
      setUserSocketID() {
        this.$axios({
          method: 'post',
          url: `/api/user`,
          data: data.user,
          headers: {
            Authorization: `Bearer ${data.token}`,
          }
        })
          .then((response) => {

          })
          .catch((response) => {
            console.log(response.data);
          })
      },
      /**
       * Leave all rooms that the user has joined.
       */
      leaveAllRooms() {
        forEach(this.userInfo.rooms, (room, key) => {
          this.socket.emit('LEAVE_ROOM', {
            room: {
              id: room.id,
              name: room.title,
            },
            user: this.userInfo,
            token: this.token,
          });
        });
      },
      /**
       * A user clicked the rooms list to join a room.
       *
       * @param $event
       */
      joinToRoom(room) {
        // Check if already a tenant.
        // We check if the user is already a tenant in the room.
        // So we just need to show the my rooms tab and hide the rooms list tabs.
        if (!isEmpty(find(this.rooms, ['id', room.id]))) {
          this.setShowRoomList(false);
          return;
        }

        // Check room limit.
        if (room.limit === room.tenants.length) {
          this.$store.commit('chat/setErrorMessages', [`Cannot join this room, it already reached its limit users`]);
          this.$bvModal.show('modal-error');
          return;
        }

        this.interfaceRoom = room;

        // We only display the interface form when members don't have a selected default interface.
        // Guess users will always be prompted with the interface form.
        const defaultInterface = this.userInfo.chat_interface;

        if (isEmpty(defaultInterface)) {
          this.$store.commit('chat/setShowInterface', true);
          return;
        }

        this.$store.commit('chat/setShowAliasSelect', true);
        // @Todo: find a way to enter room after selecting an Alias.
        //this.enterRoom(room);
      },

      /**
       * Set the active room.
       */
      enterRoom(room) {
        this.setIsLoading(true);
        this.setRooms(room);
        this.activeRoom = room.id.toString();

        setTimeout(() => {
          // The active room tab.
          this.$refs.myRooms.setActiveTab(`#${this.activeRoom}`);
          this.setIsLoading(false);
          this.setShowRoomList(false);
          this.setShowMyRooms(true);

        }, 2000);
      },
      /**
       * User leaves a room.
       * @Todo: here
       * @param room
       */
      leaveRoom(tab) {
        // We reload the user model, so we can grab the alias id used.
        this.reloadUserInfo();
      },

      /**
       * Set active room info in store.
       *
       * @param selectedTab
       */
      tabChanged(selectedTab) {
        const id = parseInt(selectedTab.tab.id);
        this.setRoom(find(this.rooms, ['id', id]));
      },
      /**
       * Returns the passport API token.
       */
      getAccessToken() {
        this.$axios.get(`api/get-token`)
          .then((response) => {
            this.setToken(get(response, 'data', ''));
            this.leaveAllRooms();
            this.getAllRooms();
            this.getPrebuiltAvatars();
          })
          .catch((response) => {
            //@Todo redirect when user is not logged in.
            console.log(response);
          });
      },
      tabClicked(selectedTab) {
//        console.log('Current tab re-clicked:' + selectedTab.tab.name);
      },
      handleTabsEdit(targetName, action) {
        if (action === 'add') {
          // let newTabName = ++this.tabIndex + '';
          // this.editableTabs.push({
          //   title: 'New Tab',
          //   name: newTabName,
          //   content: 'New Tab content'
          // });
          // this.editableTabsValue = newTabName;
        }


        if (action === 'remove') {
          // @Todo: replace alias with alias name when joining a room.
          this.socket.emit('LEAVE_ROOM', {
            roomId: parseInt(targetName),
            user: {
              id: this.user.id,
              alias: this.user.name,
              name: this.user.name,
              roles: [],
            },
          });
        }
      },
    },
  }
</script>
