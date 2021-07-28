<template>
  <div class="room">
    <div class="room__window">
      <div class="room__header"/>

      <div class="room__body">
        <div class="room__messages-wrapper">
          <!--          <div class="room__info-wrapper">-->
          <!--            <div class="room__info">-->
          <!--              <div class="room__title">-->
          <!--                Connected to the #{{ room.title }}-->
          <!--              </div>-->

          <!--              <p class="room__topic">{{ room.description }}</p>-->
          <!--            </div>-->
          <!--          </div>-->

          <div class="room__messages"
               v-chat-scroll>
            <template v-for="message in messages">
              <div class="room__message">
                <template v-if="message.type == 'rules'">
                  <span :class="message.type">{{ message.message }}</span>
                </template>

                <template
                  v-if="message.type == 'room_info' || message.type == 'room_topic' || message.type == 'join' || message.type == 'welcome' || message.type == 'left'">
                  <span :class="message.type">{{ message.message }}</span>
                </template>

                <template v-if="message.type == 'message'">
                  <div class="room__message-item">
                    <div class="room__message-item-left">
                      <avatar-thumbnail-mini :avatar="message.user.alias"/>
                      <span class="room__message-user" :class="{'mine': userInfo.id === message.user.id}">{{ message.user.alias.alias }}:</span>
                    </div>
                    <div class="room__message-item-right">
                      <span class="room__message-text"
                            :class="{'mine': userInfo.id === message.user.id}">{{ message.message }}</span>
                    </div>
                  </div>
                </template>
              </div>
            </template>
          </div>
        </div>
      </div>

      <div class="room__footer">
        <form @submit.prevent="sendMessage">
          <i class="room__smiley"></i>
          <input type="text"
                 class="room__input"
                 ref="messageInput"
                 v-model="message">

          <el-button type="primary"
                     class="room__input-submit"
                     @click.prevent="sendMessage"
                     plain>
            Send
          </el-button>
        </form>
      </div>
    </div>

    <!--    <chatters></chatters>-->
    <div class="chatters">
      <div class="chatters__header">
        <div class="chatters__header-left">
          <avatar-thumbnail-mini :avatar="aliasObj"/>
          <span class="chatters__user-main">{{ aliasObj.alias }}</span>
        </div>
        <div class="chatters__header-right">
          <i class="chatters__elipses"
             @click.prevent.stop="$refs.userMenu.open($event, aliasObj)"/>
        </div>
      </div>

      <div class="chatters__body">
        <div class="chatters__users" v-chat-scroll>
          <template v-for="tenant in users">
            <div v-if="tenant.id != aliasObj.id"
                 class="chatters__user"
                 @contextmenu.prevent="$refs.menu.open($event, tenant)">
              <avatar-thumbnail-mini :avatar="tenant"/>
              <span class="chatters__user-alias">{{ tenant.alias }}</span>
            </div>
          </template>
        </div>
      </div>

      <div class="chatters__footer">

      </div>
    </div>

    <!--Show user contextual menu-->
    <vue-context ref="userMenu"
                 v-slot="{ data }">
      <li>
        <a data-key="user.im.away"
           class="chatters__user-menu"
           :class="{selected: userContextualMenu.imAway}"
           @click.prevent="chatterContextMenuClick($event, data)">
          I'm Away
        </a>
      </li>
      <li>
        <a data-key="user.ignore.room.requests"
           class="chatters__user-menu"
           :class="{selected: userContextualMenu.ignoreRoomRequests}"
           @click.prevent="chatterContextMenuClick($event, data)">
          Ignore Room Requests
        </a>
      </li>
      <li>
        <a data-key="user.ignore.private.requests"
           class="chatters__user-menu"
           :class="{selected: userContextualMenu.ignorePrivateRequests}"
           @click.prevent="chatterContextMenuClick($event, data)">
          Ignore Private Requests
        </a>
      </li>
    </vue-context>

    <!--Show chatter contextual menu-->
    <vue-context ref="menu"
                 v-slot="{ data }">
      <li>
        <a data-key="private"
           @click.prevent="chatterContextMenuClick($event, data)">
          Private Chat
        </a>
      </li>
      <li>
        <a data-key="ignore"
           @click.prevent="chatterContextMenuClick($event, data)">
          Ignore
        </a>
      </li>
      <li>
        <a data-key="local.time"
           @click.prevent="chatterContextMenuClick($event, data)">
          Local time
        </a>
      </li>
      <li class="line"></li>
      <li>
        <a data-key="profile"
           @click.prevent="chatterContextMenuClick($event, data)">
          View Profile
        </a>
      </li>
      <li>
        <a data-key="chatmail"
           @click.prevent="chatterContextMenuClick($event, data)">
          Send Chatmail
        </a>
      </li>
      <li class="line"></li>
      <li class="v-context__sub">
        <a data-key="kfr"
           @click.prevent="chatterContextMenuClick($event, data)">
          Kick from Room
        </a>
        <ul class="v-context">
          <li>
            <a data-key="kfr.profanity"
               @click.prevent="chatterContextMenuClick($event, data)">
              Profanity
            </a>
          </li>
          <li>
            <a data-key="kfr.spamming"
               @click.prevent="chatterContextMenuClick($event, data)">
              Spamming
            </a>
          </li>
          <li>
            <a data-key="kfr.flooding"
               @click.prevent="chatterContextMenuClick($event, data)">
              Flooding
            </a>
          </li>
          <li>
            <a data-key="kfr.adult"
               @click.prevent="chatterContextMenuClick($event, data)">
              Adult Content
            </a>
          </li>
          <li>
            <a data-key="kfr.rude"
               @click.prevent="chatterContextMenuClick($event, data)">
              Rude Behaviour
            </a>
          </li>
        </ul>
      </li>
      <li class="v-context__sub">
        <a data-key="kab"
           @click.prevent="chatterContextMenuClick($event, data)">
          Kick and Ban
        </a>
        <ul class="v-context">
          <li>
            <a data-key="kab.15min"
               @click.prevent="chatterContextMenuClick($event, data)">
              Ban for 15 Mins
            </a>
          </li>
          <li>
            <a data-key="kab.1hr"
               @click.prevent="chatterContextMenuClick($event, data)">
              Ban for 1 Hour
            </a>
          </li>
          <li>
            <a data-key="kab.24hr"
               @click.prevent="chatterContextMenuClick($event, data)">
              Ban for 24 Hours
            </a>
          </li>
          <li>
            <a data-key="kab.1w"
               @click.prevent="chatterContextMenuClick($event, data)">
              Ban for 1 Week
            </a>
          </li>
          <li>
            <a data-key="kab.permanently"
               @click.prevent="chatterContextMenuClick($event, data)">
              Ban Permanently
            </a>
          </li>
        </ul>
      </li>
      <li class="v-context__sub">
        <a data-key="kfs"
           @click.prevent="chatterContextMenuClick($event, data)">
          Kick from Service
        </a>
        <ul class="v-context">
          <li>
            <a data-key="kfs.profanity"
               @click.prevent="chatterContextMenuClick($event, data)">
              Profanity
            </a>
          </li>
          <li>
            <a data-key="kfs.spamming"
               @click.prevent="chatterContextMenuClick($event, data)">
              Spamming
            </a>
          </li>
          <li>
            <a data-key="kfs.flooding"
               @click.prevent="chatterContextMenuClick($event, data)">
              Flooding
            </a>
          </li>
          <li>
            <a data-key="kfs.adult"
               @click.prevent="chatterContextMenuClick($event, data)">
              Adult Content
            </a>
          </li>
          <li>
            <a data-key="kfs.rude"
               @click.prevent="chatterContextMenuClick($event, data)">
              Rude Behaviour
            </a>
          </li>
        </ul>
      </li>
<!--      <li>-->
<!--        <a @click.prevent="chatterContextMenuClick($event, data.target.innerText)">Option 2</a>-->
<!--      </li>-->
    </vue-context>

    <!-- Show error dialog-->
    <!--    <l-dialog id="dialog-error"/>-->
  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';
  import Settings from 'Mixins/Settings';
  import get from 'lodash/get';
  import isEmpty from 'lodash/isEmpty';
  import find from 'lodash/find';
  import AvatarThumbnailMini from "../avatar/AvatarThumbnailMini";
  import LDialog from "../tools/LDialog";
  import Chatters from "./Chatters";
  import VueContext from 'vue-context';

  export default {
    mixins: [ChatStore, Settings],

    components: {
      Chatters,
      AvatarThumbnailMini,
      LDialog,
      VueContext
    },

    props: {
      /**
       * The room object.
       */
      room: {
        type: Object,
        default: () => {
        }
      }
    },

    data() {
      return {
        alias_id: 0,
        alias: '',
        role_id: 0,
        roomInfoVisible: false,
        direction: 'btt',
        userId: 1,
        message: '',
        messages: [],
        users: [],
        socket: {},
        aliasObj: {},
        userContextualMenu: {
          imAway: false,
          ignoreRoomRequests: false,
          ignorePrivateRequests: false
        },
      }
    },

    mounted() {
      this.connectToSocketIoServer();

      // We load all users for this room if there is any.
      this.getUsersByRoomId();

      this.$nextTick(() => {

        // Capture server's broadcast message to this room.
        this.socket.on('MESSAGE', (data) => {
          this.messages.push(data);
        });

        // Capture server's message when a user has joined this room.
        this.socket.on('USER_JOINED', (data) => {
          const displayWelcome = this.aliasInfo.aliasId === data.user.alias_id;

          if (displayWelcome) {
            this.displayInfo();
            this.displayTopic();
            this.displayRules();
          }

          const message = {
            type: displayWelcome ? 'welcome' : 'join',
            message: displayWelcome ? `Hi ${data.user.alias}, Welcome to ${data.room.name}!` : `${data.user.alias} has joined the chatroom!`,
          };

          this.messages.push(message);
          this.getUsersByRoomId();
          this.getAllRooms();
        });

        // Capture server's message when a user has left this room.
        this.socket.on('USER_LEAVE', (data) => {
          const message = {
            type: 'left',
            message: `${data.user.alias} has left the chatroom.`,
          };

          this.messages.push(message);
          this.getUsersByRoomId();
          this.$emit('leave', data.user);
        });

        // Setup Alias id and name of the joining user.
        if (this.isGuest()) {
          this.alias_id = this.userInfo.aliases[0].id;
          this.alias = this.userInfo.aliases[0].alias;
        }

        if (!this.isGuest()) {
          this.alias_id = this.aliasInfo.id;
          this.alias = this.aliasInfo.aliasName;
        }

        // Spectator room mode is on.
        if (this.room.spectator_only) {
          // Apply spectator role in room.
          this.role_id = 9;
        } else {
          // Apply participant role in room.
          this.role_id = 8;
        }

        this.reloadUserInfo();
        this.aliasObj = find(this.userInfo.aliases, {'id': this.aliasInfo.aliasId});

        // This sends messages to the server when a user joins a the chat room.
        this.socket.emit('JOIN_ROOM', {
          token: this.token,
          roomId: this.room.id,
          userStatus: 1,
          room: {
            id: this.room.id,
            name: this.room.title,
          },
          user: {
            id: this.userInfo.id,
            alias_id: this.aliasInfo.aliasId,
            alias: this.aliasInfo.aliasName,
            role_id: this.role_id,
            name: this.userInfo.name,
            roles: [],
            aliasInfo: this.aliasObj,
          },
        });

        // We set the focus to the message input.
        setTimeout(() => {
          this.$refs.messageInput.focus();
        }, 2000);
      });
    },

    methods: {
      /**
       * Display connected details.
       */
      displayInfo() {
        let message = {
          type: 'room_info',
          message: `Connected to the #${this.room.title}`,
        };

        this.messages.push(message);
      },
      /**
       * Display the room topic.
       */
      displayTopic() {
        let message = {
          type: 'room_topic',
          message: this.room.description,
        };

        this.messages.push(message);
      },
      /**
       * Display chat Horison rules.
       */
      displayRules() {
        const message = {
          type: 'rules',
          message: this.settings.chat_welcome,
        };

        this.messages.push(message);
      },
      /**
       * Sending message
       */
      sendMessage(e) {
        e.preventDefault();

        if (isEmpty(this.message) || this.message.trim().length == 0) {
          return;
        }

        // Check if room is set to spectator only
        // Rules:
        //  Participants are not allowed to send messages.
        if (this.room.spectator_only) {
          this.$store.commit('chat/setErrorMessages', [`Sorry, spectators cannot send chat messages to this room!`]);
          return
        }

        this.socket.emit('SEND_MESSAGE', {
          roomId: this.room.id,
          userStatus: 1,
          user: {
            id: this.userInfo.id,
            name: this.userInfo.name,
            roles: [],
            alias: this.aliasObj,
          },
          message: this.message
        });

        this.message = '';
        this.$refs.messageInput.focus();
      },
      /**
       * Get the tenants in a room.
       */
      getUsersByRoomId() {
        this.$axios.get(`/api/rooms/${this.room.id}/users`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
          }
        })
          .then((response) => {
            this.users = get(response, 'data.tenants', []);
          })
          .catch((response) => {
            console.log(response);
          });
      },
      /**
       * Alias leave's a room.
       */
      leaveRoom() {
        // This sends messages to the server when a user leave's a the chat room.
        this.socket.emit('LEAVE_ROOM', {
          token: this.token,
          roomId: this.room.id,
          userStatus: 1,
          room: {
            id: this.room.id,
            name: this.room.title,
          },
          user: {
            id: this.userInfo.id,
            alias_id: this.aliasObj.id,
            alias: this.aliasObj.alias,
            role_id: this.role_id,
            name: this.userInfo.name,
            roles: [],
            aliasInfo: this.aliasObj,
          },
        });
      },

      chatterContextMenuClick(event, data) {
        const key = event.target.getAttribute('data-key');

        if (key === 'user.im.away') {
          this.userContextualMenu.imAway = !this.userContextualMenu.imAway;
          return;
        }

        if (key === 'user.ignore.room.requests') {
          this.userContextualMenu.ignoreRoomRequests = !this.userContextualMenu.ignoreRoomRequests;
          return;
        }

        if (key === 'user.ignore.private.requests') {
          this.userContextualMenu.ignorePrivateRequests = !this.userContextualMenu.ignorePrivateRequests;
          return;
        }

        // contextual menu on users list.
        event.target.setAttribute('class', 'selected');

        console.log(key);
        console.log(data);
      }
    },

    watch: {
      // Set focus on message box when this room is active.
      roomInfo(value) {
        if (value.id === this.room.id) {
          this.$refs.messageInput.focus();
        }
      },
    },
  }
</script>
