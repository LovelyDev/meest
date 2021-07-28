<template>
  <div class="layout">
    <div class="layout__header">
      <main-navigation/>
    </div>

    <div class="layout__main">
      <div class="layout__main-inner">

      </div>
    </div>

    <div class="layout__footer">
      <span>{{ settings.site_name }} Copyright {{ new Date().getFullYear() }} </span>
    </div>
  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';
  import Settings from 'Mixins/Settings';

  export default {
    components: {
    },

    mixins: [ChatStore, Settings],

    data() {
      return {
        clientsMap: [],
        roomMaps: [],
      };
    },

    mounted() {
      //this.setSocket();

      this.$nextTick(() => {
        this.socket.on('CONNECTION_STATS', (data) => {
          console.log('received coknection status');
          console.log(data);
          this.clientsMap = data.clientsMap;
          this.roomsMap = data.roomsMap;

        });
      });
    },

    methods: {

    },
  };
</script>
