<template>
  <div class="tabs-component">
    <ul role="tablist" class="tabs-component-tabs">
      <li
        v-for="(tab, i) in tabs"
        :key="i"
        :class="{ 'is-active': tab.isActive, 'is-disabled': tab.isDisabled }"
        class="tabs-component-tab"
        role="presentation"
        v-show="tab.isVisible"
      >

        <template v-if="isRoom">
          <div class="tab-header">
            <img :src="getRoomLogo(tab.roomId)" class="logo-small">

            <a v-html="tab.header.substring(1,tab.header.length)"
               :aria-controls="tab.hash"
               :aria-selected="tab.isActive"
               :ref="`room_tab_${tab.hash.substr(1)}`"
               :href="tab.hash"
               class="tabs-component-tab-a"
               role="tab"
               @click="selectTab(tab.hash, $event)"/>

            <a v-if="isRoom"
               href='#'
               class="tab-close__link"
               @click.prevent="closeTab(tab)">
              <i class='tab-close'/>
            </a>
          </div>
        </template>

        <template v-else>
          <a v-html="tab.header"
             :aria-controls="tab.hash"
             :aria-selected="tab.isActive"
             :ref="`room_tab_${tab.hash.substr(1)}`"
             @click="selectTab(tab.hash, $event)"
             :href="tab.hash"
             class="tabs-component-tab-a"
             role="tab"/>
        </template>
      </li>
    </ul>
    <div class="tabs-component-panels">
      <slot/>
    </div>
  </div>
</template>

<script>
  import expiringStorage from 'Helpers/expiringStorage';
  import find from 'lodash/find';
  import pullAt from 'lodash/pullAt';
  import isEmpty from 'lodash/isEmpty';
  import ChatStore from 'Mixins/ChatStore';

  export default {
    mixins: [ChatStore],

    props: {
      cacheLifetime: {
        default: 5,
      },
      options: {
        type: Object,
        required: false,
        default: () => ({
          useUrlFragment: true,
          defaultTabHash: null,
        }),
      },
      /**
       *
       */
      activeTab: {
        type: String,
        default: ''
      },
      /**
       * Determines if the tab is for room.
       */
      isRoom: {
        type: Boolean,
        default: false,
      }
    },

    data: () => ({
      tabs: [],
      activeTabHash: '',
      activeTabIndex: 0,
      lastActiveTabHash: '',
    }),

    computed: {
      storageKey() {
        return `vue-tabs-component.cache.${window.location.host}${window.location.pathname}`;
      },
    },

    created() {
      this.tabs = this.$children;
    },

    mounted() {
      window.addEventListener('hashchange', () => this.selectTab(window.location.hash));

      if (this.findTab(window.location.hash)) {
        this.selectTab(window.location.hash);
        return;
      }

      //const previousSelectedTabHash = expiringStorage.get(this.storageKey);
      const previousSelectedTabHash = this.storageKey;

      if (this.findTab(previousSelectedTabHash)) {
        this.selectTab(previousSelectedTabHash);
        return;
      }

      if (this.options.defaultTabHash !== null && this.findTab("#" + this.options.defaultTabHash)) {
        this.selectTab("#" + this.options.defaultTabHash);
        return;
      }

      if (this.tabs.length) {
        this.selectTab(this.tabs[0].hash);
      }
    },

    methods: {
      /**
       * Returns the room logo.
       */
      getRoomLogo(roomId) {
        const room = find(this.rooms, ['id', parseInt(roomId)]);
        return !isEmpty(room) && room.room_type_id === 2 ? 'storage/people.png' : 'storage/logo.png';
      },
      /**
       * Sets the active tab.
       */
      setActiveTab(selectedTabHash) {
        const selectedTab = this.findTab(selectedTabHash);

        if (!selectedTab) {
          return;
        }

        if (this.lastActiveTabHash === selectedTab.hash) {
          this.tabs.forEach(tab => {
            tab.isActive = (tab.hash === selectedTab.hash);
          });

          this.$emit('clicked', {tab: selectedTab});

          return;
        }

        this.tabs.forEach(tab => {
          tab.isActive = (tab.hash === selectedTab.hash);
        });

        this.$emit('changed', {tab: selectedTab});
        this.activeTabHash = selectedTab.hash;
        this.activeTabIndex = this.getTabIndex(selectedTabHash);
        this.lastActiveTabHash = this.activeTabHash = selectedTab.hash;
        // expiringStorage.set(this.storageKey, selectedTab.hash, this.cacheLifetime);
      },

      findTab(hash) {
        return this.tabs.find(tab => tab.hash === hash);
      },

      selectTab(selectedTabHash, event) {
        // See if we should store the hash in the url fragment.
        if (event && !this.options.useUrlFragment) {
          event.preventDefault();
        }

        const selectedTab = this.findTab(selectedTabHash);

        if (!selectedTab) {
          return;
        }

        if (selectedTab.isDisabled) {
          event.preventDefault();
          return;
        }

        if (this.lastActiveTabHash === selectedTab.hash) {
          this.$emit('clicked', {tab: selectedTab});
          return;
        }

        this.tabs.forEach(tab => {
          tab.isActive = (tab.hash === selectedTab.hash);
        });

        this.$emit('changed', {tab: selectedTab});
        this.activeTabHash = selectedTab.hash;
        this.activeTabIndex = this.getTabIndex(selectedTabHash);
        this.lastActiveTabHash = this.activeTabHash = selectedTab.hash;
        // expiringStorage.set(this.storageKey, selectedTab.hash, this.cacheLifetime);
      },

      setTabVisible(hash, visible) {
        const tab = this.findTab(hash);

        if (!tab) {
          return;
        }

        tab.isVisible = visible;

        if (tab.isActive) {
          // If tab is active, set a different one as active.
          tab.isActive = visible;

          this.tabs.every((tab, index, array) => {
            if (tab.isVisible) {
              tab.isActive = true;

              return false;
            }

            return true;
          });
        }
      },

      getTabIndex(hash) {
        const tab = this.findTab(hash);

        return this.tabs.indexOf(tab);
      },

      getTabHash(index) {
        const tab = this.tabs.find(tab => this.tabs.indexOf(tab) === index);

        if (!tab) {
          return;
        }

        return tab.hash;
      },

      getActiveTab() {
        return this.findTab(this.activeTabHash);
      },

      getActiveTabIndex() {
        return this.getTabIndex(this.activeTabHash);
      },
      /**
       * Close the tab.
       *
       * @param tab
       */
      closeTab(tab) {
        this.$emit('tab-closed', tab);
        // Leave the room.
        this.$children[this.activeTabIndex].$children[0].leaveRoom();
        // delete tab
        this.tabs.splice(this.activeTabIndex, 1);
        // Remove room in Vuex room list.
        const targetRoom = find(this.rooms, ['id', parseInt(tab.id)]);
        this.removeRoom(targetRoom);
        // We set the active room.
        if (this.tabs.length > 0) {
          const tabHash = `#${this.tabs[0].id}`;
          this.setActiveTab(tabHash);
        } else {
          this.setShowRoomList(true);
          this.setShowMyRooms(false);
        }
      },
    },
  };
</script>
