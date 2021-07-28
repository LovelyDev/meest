<template>
  <div class="create-room popup">

    <div class="create-room__header popup__header">
      <div class="popup__header-inner-wrapper">
        <span>Create Room</span>

        <a href='#'
           class="popup__close-link"
           @click.prevent='closePopup'>
          <i class='popup__close-icon'/>
        </a>
      </div>
    </div>


    <div class="create-room__body popup__body">
      <div class="form-elements">
        <div class="form-element__wrapper left">
          <label for="title">Name:</label>
          <input type="text"
                 v-model="title"
                 class="input--text form-element"
                 minlength="3"
                 name="name">
          </input>
        </div>

        <div class="form-element__wrapper right">
          <label for="roomType">Room type:</label>
          <v-select name="roomType"
                    class="form-element"
                    :reduce="roomType => roomTypes.id"
                    label="name"
                    :options="roomTypes">
          </v-select>
        </div>

        <div class="form-element__wrapper left">
          <label for="roomAccess">Room access:</label>
          <v-select name="roomAccess"
                    class="form-element"
                    :reduce="roomAccess => roomAccesses.id"
                    label="name"
                    :options="roomAccesses">
          </v-select>
        </div>

        <div class="form-element__wrapper right">
          <label for="roomYoutubeAccess">Youtube access:</label>
          <v-select name="roomYoutubeAccess"
                    class="form-element right"
                    :reduce="roomYoutubeAccess => roomYoutubeAccesses.id"
                    label="name"
                    :options="roomYoutubeAccesses">
          </v-select>
        </div>

        <div class="form-element__wrapper">
          <label for="welcome">Welcome message:</label>
          <input type="text"
                 v-model="welcome"
                 class="input--text form-element"
                 minlength="1"
                 name="welcome">
          </input>
        </div>

        <div class="form-element__wrapper">
          <label for="description">Description:</label>

          <textarea class="form-element"
                    v-model="description"
                    rows="5"
                    cols="50">
          </textarea>
        </div>
      </div>
    </div>

    <div class="create-room__footer popup__footer">
      <div class="popup__footer-inner-wrapper">
        <button type="button"
                @click.prevent="createRoom"
                class="create-room__submit btn--primary">
          Create
        </button>
      </div>
    </div>

    <error-message title=""/>

  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';
  import get from 'lodash/get';
  import ErrorMessage from 'Components/base/Error';
  import forEach from 'lodash/forEach';
  import isEmpty from 'lodash/isEmpty';

  export default {
    mixins: [ChatStore],

    components: {
      ErrorMessage,
    },

    props: {
      /**
       * The room object.
       */
      // room: {
      //   type: Object,
      //   default: () => {
      //   }
      // },
    },

    data() {
      return {
        title: '',
        welcome: '',
        description: '',
        roomType: 3,
        roomTypes: [],
        roomAccess: 2,
        roomAccesses: [],
        roomYoutubeAccess: 3,
        roomYoutubeAccesses: [],
        language: 'en',
        languages: [],
        limit: 25,
      }
    },

    computed: {
      /**
       * Returns the interface logo.
       */
      getInterfaceLogoPath() {
        return this.interfaceType === 'classic' ? 'storage/interface/interface-classic.png' :
          'storage/interface/interface-graphic.png';
      },
      /**
       * Returns the interface type text.
       */
      getInterfaceText() {
        return this.interfaceType === 'classic' ?
          'The classic design is simple but stylish and user friendly interface.' :
          'The graphic design is avatar based interface.';
      },
    },

    mounted() {
      this.getRoomTypes();
      this.getRoomAccesses();
      this.getRoomYoutubeAccesses();
    },

    methods: {
      /**
       * Closes the popup.
       */
      closePopup() {
        this.$emit('create-room-close');
      },
      /**
       * Creates the new room.
       */
      createRoom() {
        this.setIsLoading(true);

        this.$axios({
          method: 'post',
          url: `/api/rooms`,
          data: {
            room_youtube_access_id: this.roomYoutubeAccess,
            room_access_id: this.roomAccess,
            room_type_id: this.roomType,
            title: this.title,
            welcome: this.welcome,
            description: this.description,
            limit: this.limit,
            language: this.language,
          },
          headers: {
            Authorization: `Bearer ${this.token}`,
          }
        })
          .then((response) => {
            const room = get(response, 'data', {});

            // this.setErrorMessages({
            //   show: true,
            //   messages: [`The room ${room.title}, has been created.`]
            // });

            this.$emit('join-room', room);
            this.getAllRooms();
            this.closePopup();

          })
          .catch((response) => {
            const errors = get(response, 'response.data.errors', '');
            const errorMessages = [];

            forEach(errors, (value, key) => {
              errorMessages.push(value[0]);
            });

            this.setErrorMessages({
              show: true,
              messages: errorMessages
            });
          })
          .finally(()=> {
            this.setIsLoading(false);
          });
      },
      /**
       * Get room types.
       */
      getRoomTypes() {
        this.$axios.get(`/api/room/types`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
          }
        })
          .then((response) => {
            this.roomTypes = get(response, 'data', []);
          })
          .catch((response) => {
            console.log(response);
          });
      },
      /**
       * Get room types.
       */
      getRoomAccesses() {
        this.$axios.get(`/api/room/accesses`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
          }
        })
          .then((response) => {
            this.roomAccesses = get(response, 'data', []);
          })
          .catch((response) => {
            console.log(response);
          });
      },
      /**
       * Get room types.
       */
      getRoomYoutubeAccesses() {
        this.$axios.get(`/api/room/youtube-accesses`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
          }
        })
          .then((response) => {
            this.roomYoutubeAccesses = get(response, 'data', []);
          })
          .catch((response) => {
            console.log(response);
          });
      },
    },
  }
</script>
