<template>
  <div v-if="showMyRooms"
       class="chatters">
    <div class="chatters__header">
      <i class="icon el-icon-user"/> {{ alias }}
    </div>

    <div class="chatters__body">
      <div class="chatters__users" v-chat-scroll>
        <template v-for="tenant in users">
          <div class="chatters__user">
            <div class="chatters__user-status">
            </div>

            <div class="chatters__user-name"
                 :class="{'mine': userInfo.id === tenant.id}">

              <el-popover
                  placement="bottom"
                  :title="tenant.name"
                  width="200"
                  trigger="click"
                  content="Some options will appear here">
                <div slot="reference">
                  <i class="el-icon-user-solid"/>{{ tenant.name }}
                </div>
              </el-popover>
            </div>
          </div>
        </template>
      </div>
    </div>

    <div class="chatters__footer">

    </div>
  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';
  import get from 'lodash/get';
  import isEmpty from 'lodash/isEmpty';
  import io from 'socket.io-client';

  export default {

    mixins: [ChatStore],

    props: {
      /**
       * The room object.
       */
      room: {
        type: Object,
        default: () => {
        }
      },
    },

    data() {
      return {
        aliasId: 0,
        alias: '',
        roomInfoVisible: false,
        direction: 'btt',
        userId: 1,
        message: '',
        messages: [],
        users: [],
      }
    },

    mounted() {
      this.aliasId = this.aliasInfo.aliasId;
      this.alias = this.aliasInfo.aliasName;
    },

    methods: {
      /**
       * Returns the room logo.
       */
      // getAvatar() {
      //   return this.roomInfo.official === 0 ? 'storage/people.png' : 'storage/logo.png';
      // },
      join() {
        this.socket.emit('SEND_MESSAGE', {
          intent: 'join',
          roomId: this.room.id,
          userStatus: 1,
          userId: this.userInfo.id,
          user: this.userInfo.name,
          message: '',
        });
      },
      sendMessage(e) {
        e.preventDefault();

        if (isEmpty(this.message)) {
          return;
        }

        this.socket.emit('SEND_MESSAGE', {
          roomId: this.room.id,
          userStatus: 1,
          user: {
            id: this.userInfo.id,
            name: this.userInfo.name,
            roles: [],
          },
          message: this.message
        });

        this.message = '';
      },
      /**
       * Get the tenants in a room.
       */
      getUsersByRoomId() {
        this.$axios.get(`/api/rooms/${this.room.id}/users`)
          .then((response) => {
            this.users = get(response, 'data.tenants', []);
          })
          .catch((response) => {
            console.log(response);
          });
      }
    },
  }
</script>
