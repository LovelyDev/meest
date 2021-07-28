import get from 'lodash/get';

export default {
  data() {
    return {
      settings: {},
    }
  },

  mounted() {
    this.getSettings();
  },

  methods: {
    /**
     * Get application settings.
     */
    getSettings() {
      this.$axios.get(`/api/settings`)
        .then((response) => {
          this.settings = get(response, 'data', {});
        })
        .catch((response) => {
          console.log(response);
        });
    },
  },
};
