<template>
  <div class="room-list">

    <div class="room-list__copy">
      Popular chatrooms are displayed at the top of the list.
    </div>

    <div class="room-list__items">
      <transition-group name="bounceLeft">
        <div v-for="room in items"
             :key="room.id"
             :class="type"
             @click.prevent="$emit('joinRoom', room)"
             class="room-list__item">

          <div class="room-list__item-logo-wrapper">
            <img :src="getRoomLogo"
                 :class="type"
                 class="room-list__item-logo">
          </div>

          <div class="room-list__item-title-wrapper">
            <div class="room-list__item-title">
              {{ room.title }}
            </div>
            <div class="room-list__item-description">
              {{ truncateText(room.description) }}
            </div>
          </div>

          <div class="room-list__item-stats">
            <i class="room-list__item-stats-users"/>
            {{room.tenants.length}} / {{ room.limit }}
          </div>

          <div class="room-list__item-language">
            {{ room.language }}
          </div>

        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
  import isEmpty from 'lodash/isEmpty';
  import ChatStore from 'Mixins/ChatStore';
  import Settings from 'Mixins/Settings';

  export default {
    mixins: [ChatStore, Settings],

    props: {
      /**
       * The room object.
       */
      type: {
        type: String,
        default: 'official',
        validator: (value) => {
          return ['official', 'public'].indexOf(value) !== -1
        }
      },
      items: {
        type: Array,
        default() {
          return [];
        }
      }
    },

    data() {
      return {
        tableData: [],
        tableColumns: [
          {prop: 'limit', label: 'limit'},
          {prop: 'language', label: 'Language'},
        ],
        currentPage: 1,
        total: 1,
        perPage: 30,
        sortBy: '',
        sortDesc: false,
        searchValue: '',
        currencySymbol: '$',
      };
    },

    computed: {
      /**
       * Returns the room logo.
       */
      getRoomLogo() {
        return this.type === 'public' ? 'storage/people.png' : 'storage/logo.png';
      },
    },

    mounted() {
      // this.getAllRooms();
    },

    methods: {
      /**
       * Truncates long text.
       *
       * @param {Number} value The currency value.
       * @returns {String} The truncated text.
       */
      truncateText(value) {
        return isEmpty(value) ? '' : this.$options.filters.truncate(`${value}...`, 80);
      },
    },
  };
</script>
