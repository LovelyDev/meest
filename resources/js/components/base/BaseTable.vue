<template>
  <div class="base-table">

    <el-row>
      <el-col :span="18">
        <div class="grid-content bg-purple-dark">

          <el-table
              :data="tableData"
              style="width: 100%"
              :row-class-name="tableRowClassName">

            <template v-for="column in tableColumns">
              <el-table-column
                  :prop="column.prop"
                  :label="column.label">
              </el-table-column>
            </template>
          </el-table>


        </div>
      </el-col>
    </el-row>



    <!--    <div :class="getElClass('header')">-->
    <!--      <h3 :class="getElClass('heading')"-->
    <!--          v-if="heading">{{ heading }}-->
    <!--      </h3>-->

    <!--      <modal-link :redirect="addAction == 'redirect'"-->
    <!--                  :title="'New ' + heading"-->
    <!--                  :href="addUrl"-->
    <!--                  :button_classes="[getElClass('add'), 'btn btn-primary btn-sm mr-auto']"-->
    <!--                  v-if="addUrl">+ Add-->
    <!--      </modal-link>-->

    <!--      <div v-if="inputFilterSearch.length">-->
    <!--        <input v-model="searchValue"-->
    <!--               @keyup.enter="filterSearch"-->
    <!--               :placeholder="'Search ' + inputFilterSearch"-->
    <!--               type="text"-->
    <!--               class="col-sm-2 ml-4">-->
    <!--      </div>-->

    <!--      <span :class="[getElClass('count'), 'ml-auto text-muted']">-->
    <!--        {{ total }} records found-->
    <!--      </span>-->

    <!--    </div>-->

    <!--    <b-table :fields="getFields()"-->
    <!--             :items="items"-->
    <!--             :class="getElClass('table')"-->
    <!--             :sort-by.sync="sortBy"-->
    <!--             :sort-desc.sync="sortDesc"-->
    <!--             @sort-changed="onSorted"-->
    <!--             v-if="items.length > 0"-->
    <!--             no-local-sorting-->
    <!--             hover>-->
    <!--      &lt;!&ndash; Create links from fields in asLinks &ndash;&gt;-->
    <!--      <template v-for="field in asLinks"-->
    <!--                :slot="field"-->
    <!--                slot-scope="data">-->
    <!--        <a v-if="typeof data.item[field] === 'string'"-->
    <!--           :href="data.item.url"-->
    <!--           class="getElClass('link')">{{ data.item[field] }}-->
    <!--        </a>-->

    <!--        <a v-else-if="data.item[field] !== null"-->
    <!--           :href="data.item[field].url"-->
    <!--           :class="getElClass('link')">{{ data.item[field].title }}-->
    <!--        </a>-->
    <!--      </template>-->

    <!--      &lt;!&ndash; Create actions &ndash;&gt;-->
    <!--      <template slot-scope="data"-->
    <!--                v-if="data.item.tasks"-->
    <!--                slot="actions">-->

    <!--        <b-dropdown :split="true"-->
    <!--                    @click="navigateTo(data.item.url)"-->
    <!--                    text="View"-->
    <!--                    size="sm"-->
    <!--                    right>-->

    <!--          <b-dropdown-item @click="navigateTo(path)"-->
    <!--                           v-for="(name, path) in data.item.tasks"-->
    <!--                           :key="path">{{ name }}-->
    <!--          </b-dropdown-item>-->
    <!--        </b-dropdown>-->
    <!--      </template>-->
    <!--    </b-table>-->

    <!--    <div :class="getElClass('pager')"-->
    <!--         v-if="(total > perPage) && pagination">-->

    <!--      <b-pagination :total-rows="total"-->
    <!--                    v-model="currentPage"-->
    <!--                    :per-page="perPage"-->
    <!--                    @change="pageChange"-->
    <!--                    size="md"/>-->
    <!--    </div>-->
  </div>
</template>

<script>
  import get from 'lodash/get';
  import orderBy from 'lodash/orderBy';

  export default {
    props: {
      /**
       * Url to consume api data.
       */
      dataUrl: {
        type: String,
        required: false
      },
      /**
       * Fields to pluck from the results.
       */
      fields: {
        type: Array,
        default: () => ['title']
      },
      /**
       * Heading for table.
       */
      heading: {
        type: String,
        default: ''
      },
      /**
       * Url for creating an entity.
       */
      addUrl: {
        type: String,
        default: ''
      },
      /**
       * title field for the table.
       */
      titleField: {
        type: String,
        default: 'title'
      },
      /**
       * Links array passed to table for view and edit.
       */
      asLinks: {
        type: Array,
        default: () => []
      },
      /**
       * Adding actions ta table like redirecting etc.
       */
      addAction: {
        type: String,
        default: 'modal'
      },
      /**
       * Column to sort by.
       */
      defaultSortBy: {
        type: String,
        default: ''
      },
      /**
       * Default sort by descending.
       */
      defaultSortDesc: {
        type: Boolean,
        default: false
      },
      /**
       * Number of pages on pagination.
       */
      dataPerPage: {
        type: Number,
        default: 30
      },
      /**
       * pagination for table.
       */
      pagination: {
        type: Number,
        default: 1
      },
      /**
       * Filter search for table for multiple columns.
       */
      inputFilterSearch: {
        type: Array,
        default: () => []
      },
    },

    data() {
      return {
        tableData: [],
        tableColumns: [
          {prop: 'title', label: 'Name'},
          {prop: 'limit', label: 'Limit'},
          {prop: 'language', label: 'Language'},
          {prop: 'description', label: 'Description'},
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

    mounted() {
      this.getAllRooms();
      // this.sortBy = this.defaultSortBy;
      // this.sortDesc = this.defaultSortDesc;
      // this.perPage = this.dataPerPage;
      // this.getData({
      //   page: 1,
      //   order_by: this.sortBy,
      //   sort: (this.sortDesc ? 'desc' : 'asc')
      // });
    },

    methods: {
      /**
       * Sets the product default field values.
       *
       * @param {Number} id
       *   The product catalogue id.
       */
      getAllRooms(id) {
        this.$axios.get(`/api/rooms`)
          .then((response) => {
            console.log(response);
            this.tableData = orderBy(get(response, 'data', []),
              ['official', 'title'], ['desc', 'asc']);
          })
          .catch((response) => {
            console.log(error);
          });
      },
      tableRowClassName({row, rowIndex}) {
        if (row.official) {
          return 'official';
        }

        if (row.registered) {
          return 'registered';
        }

        return '';
      },
      // /**
      //  * Render data on sorting.
      //  */
      // onSorted() {
      //   this.getData();
      // },
      // /**
      //  * Gets data on user input to filter query results.
      //  */
      // filterSearch() {
      //   this.getData();
      // },
      // /**
      //  * Gets data from API with options.
      //  * @param {object} options required to render desirable query.
      //  */
      // getData(options = {}) {
      //   this.getDataOptions(options);
      //   axios.get(this.dataUrl, {
      //     params: options
      //   })
      //     .then((response) => {
      //       this.items = response.data.data;
      //       this.mapMeta(response.data);
      //       this.parseDates();
      //     });
      // },
      // /**
      //  * Gets data options for query builder.
      //  * @param {object} options
      //  */
      // getDataOptions(options = {}) {
      //   options.order_by = this.sortBy;
      //   options.sort = (this.sortDesc ? 'desc' : 'asc');
      //   options.pagination = this.pagination;
      //   options.per_page = this.dataPerPage;
      //
      //   if (this.inputFilterSearch && this.searchValue) {
      //     options.filter_search = {};
      //     this.inputFilterSearch.forEach((item) => {
      //       options.filter_search[item] = this.searchValue;
      //     });
      //   }
      // },
      // /**
      //  * Returning class name of elements.
      //  * @param {string} name
      //  * @return {string}
      //  */
      // getElClass(name) {
      //   return `card-table__${name}`;
      // },
      // /**
      //  * Gets page Number.
      //  * @param {number} pageNum
      //  */
      // pageChange(pageNum) {
      //   this.getData({ page: pageNum });
      // },
      // /**
      //  * Map values to a newly generated array meta.
      //  * @param {object} responseData
      //  */
      // mapMeta(responseData) {
      //   if (responseData.meta) {
      //     this.currentPage = responseData.meta.current_page;
      //     this.total = responseData.meta.total;
      //     this.perPage = responseData.meta.per_page;
      //   } else {
      //     this.total = responseData.data.length;
      //   }
      // },
      // /**
      //  * Path to redirect to.
      //  * @param {string} path
      //  */
      // navigateTo(path) {
      //   document.location = path;
      // },
      // /**
      //  * Parse dates
      //  */
      // parseDates() {
      //   forEach(this.items, (item, key) => {
      //     if (item.created_at) {
      //       this.items[key].created_at = moment(this.items[key].created_at).format('D/M/YYYY');
      //     }
      //
      //     if (item.date) {
      //       this.items[key].date = moment(this.items[key].date).format('D/M/YYYY');
      //     }
      //   });
      // },
      // /**
      //  * Gets the actions for edit, view, delete of an entity.
      //  * @return {object}
      //  */
      // getFields() {
      //   const fields = this.fields;
      //   if (fields.includes('actions')) {
      //     pull(fields, 'actions');
      //     fields.push({ key: 'actions', class: this.getElClass('actions') });
      //   }
      //
      //   return fields;
      // },
    },

  };
</script>
